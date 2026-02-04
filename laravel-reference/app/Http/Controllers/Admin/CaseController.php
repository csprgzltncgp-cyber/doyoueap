<?php

namespace App\Http\Controllers\Admin;

use App\Enums\CaseExpertStatus;
use App\Exports\ClosedCasesExport;
use App\Http\Controllers\Controller;
use App\Mail\AssignCaseMail;
use App\Models\CaseInput;
use App\Models\Cases;
use App\Models\CaseValues;
use App\Models\Company;
use App\Models\Consultation;
use App\Models\ContractHolder;
use App\Models\Country;
use App\Models\InvoiceCaseData;
use App\Models\LanguageSkill;
use App\Models\Permission;
use App\Models\Specialization;
use App\Models\User;
use App\Models\WosAnswers;
use App\Traits\EapOnline\OnlineTherapyTrait;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Request as Input;
use Maatwebsite\Excel\Facades\Excel;

class CaseController extends Controller
{
    use OnlineTherapyTrait;

    public function delete($id)
    {
        $case = Cases::query()->findOrFail($id);
        $case->forceDelete();

        return response()->json(['status' => 0]);
    }

    public function deleteAll(Request $request)
    {
        if (! $request->cases) {
            return redirect()->back();
        }

        foreach ($request->cases as $id) {
            $case = Cases::query()->findOrFail($id);
            $case->delete();
        }

        return redirect()->back();
    }

    // LEZÁRT ESETEK LISTÁJA
    public function list_closed()
    {
        $cases = Cases::query()
            ->orderBy('id', 'desc')
            ->when(! Auth::user()->type == 'admin', fn ($query) => $query->where('status', '!=', 'confirmed')
                ->where('status', '!=', 'client_unreachable_confirmed')
                ->where('status', '!=', 'interrupted_confirmed'))
            ->when(Auth::user()->type == 'admin', fn ($query) => $query->where('status', 'confirmed')
                ->orWhere('status', 'client_unreachable_confirmed')
                ->orWhere('status', 'interrupted_confirmed'))
            ->with(['values', 'values.input', 'experts', 'consultations', 'date', 'values.input.translation', 'values.select_value', 'values.city'])
            ->paginate(15);

        return view('admin.cases.list.closed', ['cases' => $cases]);
    }

    public function filter()
    {
        $filters = CaseInput::all();
        $companies = Company::query()->orderBy('name', 'asc')->get();
        $permissions = Permission::query()->get();
        $countries = Country::query()->get();
        $experts = User::query()->where('type', 'expert')->orderBy('name', 'asc')->get();
        $contractHolders = ContractHolder::query()->orderBy('name', 'asc')->get();
        $languageSkills = LanguageSkill::query()->get();
        $specializations = Specialization::query()->get();

        return view('admin.cases.filter', ['filters' => $filters, 'companies' => $companies, 'permissions' => $permissions, 'countries' => $countries, 'experts' => $experts, 'contractHolders' => $contractHolders, 'languageSkills' => $languageSkills, 'specializations' => $specializations]);
    }

    public function filter_process(Request $request)
    {
        if ((int) $request->show_consultation_numbers_total === 1 && ($request->expert == '-1'
        || $request->consultation_date_from == '-1' || $request->consultation_date_from === null || $request->consultation_date_to == '-1' || $request->consultation_date_to == null)) {
            session()->flash('expert_and_date_interval_required');

            return redirect()->back();
        }

        $query_string = Cases::createFilterQueryString($request->except('_token'));

        return redirect()->route('admin.cases.filtered', $query_string);
    }

    public function filtered()
    {
        $attributes = Input::get('attributes');
        $inputs = Input::get('inputs');
        $expert = Input::get('expert');
        $contractHolder = Input::get('contract_holder_id');
        $org_id = Input::get('org_id');
        $activity_code = Input::get('activity_code');
        $consultation_date_from = Input::get('consultation_date_from');
        $consultation_date_to = Input::get('consultation_date_to');
        $case_confirmed_at_from = Input::get('case_confirmed_at_from');
        $case_confirmed_at_to = Input::get('case_confirmed_at_to');
        $show_consultation_numbers_total = Input::get('show_consultation_numbers_total');
        $cases = Cases::filter(
            attributes: $attributes,
            inputs: $inputs,
            expert: $expert,
            contract_holder_id: $contractHolder,
            org_id: $org_id,
            activity_code: $activity_code,
            consultation_date_from: $consultation_date_from,
            consultation_date_to: $consultation_date_to,
            case_confirmed_at_from: $case_confirmed_at_from,
            case_confirmed_at_to: $case_confirmed_at_to,
        );

        $total_consultations = ($show_consultation_numbers_total) ? Consultation::query()
            ->whereIn('case_id', $cases->cursor()->pluck('id')->toArray())
            ->whereBetween('created_at', [Carbon::parse($consultation_date_from)->startOfDay(), Carbon::parse($consultation_date_to)->endOfDay()])
            ->count() : null;

        $expert = ($show_consultation_numbers_total && $expert) ? User::query()->where('id', $expert)->first() : null;

        $cases = $cases->paginate(15);

        return view('admin.cases.list.filtered', [
            'cases' => $cases,
            'total_consultations' => $total_consultations,
            'consultations_from' => $consultation_date_from,
            'consultations_to' => $consultation_date_to,
            'expert' => $expert,
        ]);
    }

    public function need_exclamation($country_id): bool
    {
        $cases = Cases::query()
            ->with(['experts', 'consultations'])
            ->where('status', '!=', 'confirmed')
            ->where('country_id', $country_id)
            ->where('status', '!=', 'client_unreachable_confirmed')
            ->where('status', '!=', 'interrupted_confirmed')
            ->get();

        foreach ($cases as $case) {
            if (
                $case->experts->first() &&
                $case->experts->first()->getRelationValue('pivot')->accepted == CaseExpertStatus::REJECTED->value
                || ! ($case->experts->count())
            ) {
                return true;
            }

            $over_5_days_without_consultation = 0;
            if ($case->employee_contacted_at && ! $case->consultations->count()) {
                $employee_contacted_at = Carbon::parse($case->employee_contacted_at, 'Europe/Budapest');
                $over_5_days_without_consultation = Carbon::now()->setTimezone('Europe/Budapest')->diffInDays($employee_contacted_at) >= 4 ? 1 : 0;
            }

            if ($over_5_days_without_consultation !== 0) {
                return true;
            }

            $is_case_accepted = null;

            if ($case->experts->first()) {
                $expert = $case->experts->first()->getRelationValue('pivot');
                $start = Carbon::parse($expert->created_at, 'Europe/Budapest');
                $resolution = CarbonInterval::hour();
                $hours = $start->diffFiltered($resolution, fn ($date) => $date->isWeekday(), Carbon::now()->setTimezone('Europe/Budapest'));
                if ($hours >= 24 && $expert->accepted == CaseExpertStatus::ASSIGNED_TO_EXPERT->value) {
                    $is_case_accepted = -1;
                }
            }

            if ($is_case_accepted == -1) {
                return true;
            }
        }

        return false;
    }

    public function list_in_progress()
    {
        $countries = Country::query()->orderBy('code', 'asc')->get();

        return view('admin.cases.list.in_progress', ['countries' => $countries]);
    }

    public function getCases(Request $request)
    {
        $take = 10;
        $skip = ($request->page - 1) * $take;
        $cases = Cases::query()->where('status', '!=', 'confirmed')
            ->where('country_id', $request->country_id)
            ->where('status', '!=', 'client_unreachable_confirmed')
            ->where('status', '!=', 'interrupted_confirmed')
            ->with(['values', 'values.input', 'experts', 'consultations', 'company', 'date', 'case_type', 'case_location', 'case_client_name'])
            ->get();

        $cases = $cases->sortBy(function (Cases $case): array {
            $no_expert_selected = ! count($case->experts);
            $eap_consultation_deleted = (bool) $case->eap_consultation_deleted;
            $client_is_unreachable = $case->getRawOriginal('status') == 'client_unreachable';
            $interrupted = $case->getRawOriginal('status') == 'interrupted';
            $rejected_case = ! $case->case_accepted_expert() && $case->experts->where('pivot.accepted', CaseExpertStatus::REJECTED->value)->first();
            $wpo_additional_information_required = $case->values->where('case_input_id', 97)->first()?->value == 576;

            $over_24_hours = false;
            if ($case->experts->first()) {
                $resolution = CarbonInterval::hour();
                $hours = Carbon::parse($case->created_at)->diffFiltered($resolution, fn ($date) => $date->isWeekday(), now());
                $expert = $case->experts->first()->getRelationValue('pivot');
                $over_24_hours = $hours >= 24 && $expert->accepted == CaseExpertStatus::ASSIGNED_TO_EXPERT->value;
            }

            // Determine sort bucket
            $sortValue = 2;

            if ($over_24_hours || $no_expert_selected || $eap_consultation_deleted || $client_is_unreachable || $interrupted || $rejected_case || $wpo_additional_information_required) {
                $sortValue = 1;
            }

            $case->setAttribute('notification_sort', $sortValue);

            // Handle secondary sort:
            if ($sortValue == 2) {
                return [$sortValue, -strtotime($case->created_at)]; // most recent first
            } else {
                return [$sortValue, strtotime($case->created_at)]; // oldest first
            }
        });

        $cases = filter_var($request->all, FILTER_VALIDATE_BOOLEAN) ? $cases->slice($skip) : $cases->slice($skip, $take);

        $cases->each(function ($item, $key): void {
            if ($item->status == 'opened' || $item->status == 'assigned_to_expert') {
                $item->setAttribute('percentage', 0);
            } else {
                $case_type = $item->case_type->value;
                if (
                    $item->closed_by_expert
                    ||
                    (
                        /* MUNKAJOGI ESETKENÉL */
                        $case_type == 5 && (
                            $item->getRawOriginal('status') == 'employee_contacted' ||
                            $item->customer_satisfaction_not_possible ||
                            $item->customer_satisfaction
                        )
                    )
                    ||
                    (
                        /* EGYÉB ESETKNÉL */
                        $case_type == 4 && (
                            $item->getRawOriginal('status') == 'employee_contacted' ||
                            $item->customer_satisfaction_not_possible
                        )
                    )
                    ||
                    /* COACHING ESTEK */
                    ($case_type == 11 && $item->customer_satisfaction_not_possible)
                    ||
                    (
                        (
                            (in_array($case_type, [2, 3]) && $item->customer_satisfaction != null) ||
                            (! in_array($case_type, [2, 3]) && $item->customer_satisfaction_not_possible == 1)
                        )
                        &&
                        $item->consultations->count() > 0
                        && ! in_array($case_type, [1, 6, 7])
                    )
                ) {
                    $item->setAttribute('percentage', 100);
                } elseif ($item->consultations->count() && $item->getRawOriginal('status') == 'employee_contacted') {
                    $item->setAttribute('percentage', 66);
                } elseif ($item->getRawOriginal('status') == 'employee_contacted') {
                    $item->setAttribute('percentage', 33);
                }
            }

            $item->setAttribute('text_date', isset($item->date[0]) ? $item->date[0]->value : '');
            $item->setAttribute('text_company_name', $item->company != null ? $item->company->name : '');
            $item->setAttribute('text_expert_name', $item->experts->count() ? $item->experts->first()->name : '');
            $item->setAttribute('text_case_type', $item->case_type != null ? $item->case_type->getValue() : null);
            $item->setAttribute('text_case_location', $item->case_location != null ? $item->case_location->getValue() : null);
            $item->setAttribute('text_client_name', $item->case_client_name != null ? $item->case_client_name->getValue() : null);
            $item->setAttribute('diff', 0);
            $item->setAttribute('over_5_days_without_consultation', 0);

            $case_consultations = $item->consultations;

            $c = $case_consultations->sortBy('consultations.id')->first();
            $now = Carbon::now()->setTimezone('Europe/Budapest');

            if ($c) {
                $date = Carbon::parse($c->created_at, 'Europe/Budapest');
                $item->setAttribute('diff', $now->diffInDays($date));
            }

            $item->setAttribute('is_case_accepted', null);

            if ($item->experts->first()) {
                $expert = $item->experts->first()->getRelationValue('pivot');
                $start = Carbon::parse($expert->created_at, 'Europe/Budapest');
                $resolution = CarbonInterval::hour();
                $hours = $start->diffFiltered($resolution, fn ($date) => $date->isWeekday(), $now);
                if ($hours >= 24 && $expert->accepted == CaseExpertStatus::ASSIGNED_TO_EXPERT->value) {
                    $item->setAttribute('is_case_accepted', CaseExpertStatus::ASSIGNED_TO_EXPERT->value);
                }
            }

            if ($item->employee_contacted_at && ! $case_consultations->count()) {
                $employee_contacted_at = Carbon::parse($item->employee_contacted_at, 'Europe/Budapest');
                $item->setAttribute('over_5_days_without_consultation', $now->diffInDays($employee_contacted_at) >= 4 ? 1 : 0);
            }

            $case_type = $item->case_type->value;

            $item->setAttribute('closable', (
                $item->closed_by_expert
                ||
                (
                    $case_type == 5 && (
                        $item->getRawOriginal('status') == 'employee_contacted' ||
                        $item->customer_satisfaction_not_possible ||
                        $item->customer_satisfaction
                    )
                )
                ||
                (
                    $case_type == 4 && (
                        $item->getRawOriginal('status') == 'employee_contacted' ||
                        $item->customer_satisfaction_not_possible
                    )
                )
                ||
                /* COACHING ESTEK */
                ($case_type == 11 && $item->customer_satisfaction_not_possible)
                ||
                (
                    (
                        (
                            (in_array($case_type, [2, 3]) && $item->customer_satisfaction != null) ||
                            (! in_array($case_type, [2, 3]) && $item->customer_satisfaction_not_possible == 1)
                        )
                    )
                    &&
                    ($case_consultations->count())
                    && ! in_array($case_type, [1, 6, 7])
                )
            ) ? 1 : 0);
        });

        $views = [];
        foreach ($cases as $case) {
            $views[] = view('components.cases.list_in_progress', ['case' => $case, 'class' => ''])->render();
        }

        return response()->json(['html' => $views]);
    }

    // ESET MEGTEKINTÉSE
    public function view($id)
    {
        $case = Cases::query()->findOrFail($id);
        $language_skills = LanguageSkill::query()->get()
            ->sortBy(fn ($query) => $query->translation->value);

        $online_appointment_booking = DB::connection('mysql_eap_online')->table('online_appointment_bookings')->where('case_id', $case->id)->get()->sortByDesc('id')->first();
        $intake_online_booking = DB::connection('mysql_eap_online')->table('intake_bookings')->where('case_id', $case->id)->get()->sortByDesc('id')->first();

        return view('admin.cases.view', [
            'case' => $case,
            'language_skills' => $language_skills,
            'online_appointment_booking' => $online_appointment_booking,
            'intake_online_booking' => $intake_online_booking,
        ]);
    }

    public function export(Request $request)
    {
        if (! $request->cases) {
            return redirect()->back();
        }

        return Excel::download(new ClosedCasesExport($request->cases), 'esetek.xlsx');
    }

    public function assignExpertCase(Request $request)
    {
        $user = User::assignCase($request->case_id, $request->expert_id);

        return response()->json(['name' => $user->name]);
    }

    public function addConsultationToCase(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'case_id' => 'required|exists:cases,id',
        ]);

        $consultation = Consultation::addConsultation($request);

        return response()->json($consultation);
    }

    public function addWosToCase(Request $request)
    {
        $wosAnswer = WosAnswers::addWosAnswers($request);

        return response()->json($wosAnswer);
    }

    public function setStatus(Request $request)
    {
        $case = Cases::query()->where('id', $request->case_id);

        // Case was previously closed, but new status is different (Case is "reopened")
        if ($case->first()->getRawOriginal('status') == 'confirmed' && $request->status != 'confirmed') {
            $case->update([
                'status' => $request->status,
                'confirmed_by' => null,
                'confirmed_at' => null,
            ]);

            // Remove any invoice data created for the case
            InvoiceCaseData::query()->where('case_identifier', $case->first()->case_identifier)->delete();
        }

        if ($request->status == 'employee_contacted') {
            $case->update([
                'status' => $request->status,
                'employee_contacted_at' => Carbon::now('Europe/Budapest'),
            ]);
        } elseif ($request->status == 'assigned_to_expert') {
            $case->update([
                'status' => 'assigned_to_expert',
                'employee_contacted_at' => null,
            ]);
        } else {
            $case->update([
                'status' => $request->status,
            ]);
        }

        $case = Cases::query()->findOrFail($request->case_id);
        if (Auth::user()->type == 'expert') {
            $user_id = Auth::user()->id;
            $case->experts()->syncWithoutDetaching([$user_id => ['accepted' => CaseExpertStatus::ACCEPTED->value]]);
        }

        return response()->json(['status' => $case->status]);
    }

    public function assingNewValueToCaseInput(Request $request)
    {
        Cases::query()->findOrFail($request->case_id);
        CaseValues::query()->where('case_id', $request->case_id)->where('case_input_id', $request->input_id)->update([
            'value' => $request->value,
        ]);

        return response()->json(['status' => 0]);
    }

    public function deleteConsultation($id)
    {
        if (Auth::user()->type == 'admin' || Auth::user()->type == 'eap_admin') {
            Consultation::query()->where('id', $id)->delete();
        }

        return response()->json(['status' => 0]);
    }

    public function deleteCustomerSatisfaction($id)
    {
        Cases::query()->where('id', $id)->update(['customer_satisfaction' => null]);

        return response()->json(['status' => 0]);
    }

    public function revertExpertCannotAssign($caseId, $userId)
    {
        $case = Cases::query()->findOrFail($caseId);
        $case->experts()->updateExistingPivot($userId, ['accepted' => CaseExpertStatus::ASSIGNED_TO_EXPERT->value]);

        return response()->json(['status' => 0]);
    }

    public function generate_new_cases(Request $request)
    {
        $caseId = $request->old_case_id;
        $case = Cases::query()->findOrFail($caseId);

        DB::table('cases')->insert([[
            'status' => 'opened',
            'company_id' => $case->company_id,
            'country_id' => $case->country_id,
            'employee_contacted_at' => now(),
            'confirmed_by' => null,
            'confirmed_at' => null,
            'created_by' => $case->created_by,
            'customer_satisfaction' => null,
            'customer_satisfaction_not_possible' => null,
            'closed_by_expert' => null,
            'email_sent_3months' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]]);

        $isnerted_case_id = DB::getPdo()->lastInsertId();
        $new_expert_id = $request->experts;

        DB::table('expert_x_case')->insert([[
            'user_id' => $new_expert_id,
            'case_id' => $isnerted_case_id,
            //            'accepted' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]]);

        $case_input_value = DB::table('case_values')->where('case_id', $caseId)->get();

        foreach ($case_input_value as $value) {
            DB::table('case_values')->insert([[
                'case_id' => $isnerted_case_id,
                'case_input_id' => $value->case_input_id,
                'value' => $value->value,
                'created_at' => now(),
                'updated_at' => now(),
            ]]);
        }

        $user = User::query()->findOrFail($new_expert_id);
        $case = Cases::query()->findOrFail($isnerted_case_id);
        $case->status = 'assigned_to_expert';
        $case->save();
        // minden olyan expert hozzárendelést törlünk, ahol -1 az accepted
        $case->experts()->attach($new_expert_id);

        Mail::to($user->email)->send(new AssignCaseMail($user, $case));

        return redirect()->back();
    }

    public function select_all_cases()
    {
        $attributes = Input::get('attributes');
        $inputs = Input::get('inputs');
        $expert = Input::get('expert');
        $contract_holder_id = Input::get('contract_holder_id');
        $org_id = Input::get('org_id');
        $activity_code = Input::get('activity_code');
        $consultation_date_from = Input::get('consultation_date_from');
        $consultation_date_to = Input::get('consultation_date_to');
        $cases = Cases::filter(
            attributes: $attributes,
            inputs: $inputs,
            expert: $expert,
            contract_holder_id: $contract_holder_id,
            org_id: $org_id,
            activity_code: $activity_code,
            consultation_date_from: $consultation_date_from,
            consultation_date_to: $consultation_date_to
        );
        $ids = $cases->get()->pluck('id');

        return response(json_encode($ids, JSON_THROW_ON_ERROR));
    }

    public function set_activity_code()
    {
        request()->validate([
            'case_id' => 'required|exists:cases,id',
            'activity_code' => 'required',
        ]);

        Cases::query()
            ->where('id', request('case_id'))->update([
                'activity_code' => request('activity_code'),
            ]);

        return response()->json(['success' => true]);
    }

    public function list_summary()
    {
        return view('admin.cases.list.summary');
    }
}
