<?php

namespace App\Http\Controllers\Operator;

use App\Exports\ClosedCasesExport;
use App\Http\Controllers\Controller;
use App\Mail\AssignCaseMail;
use App\Models\CaseInput;
use App\Models\Cases;
use App\Models\CaseValues;
use App\Models\City;
use App\Models\Company;
use App\Models\ContractHolder;
use App\Models\Country;
use App\Models\DeutscheTelekomEmail;
use App\Models\LanguageSkill;
use App\Models\OrgData;
use App\Models\Permission;
use App\Models\Specialization;
use App\Models\User;
use App\Traits\SendMailToLifeworksTrait;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Request as Input;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class CaseController extends Controller
{
    use SendMailToLifeworksTrait;

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

        return view('operator.cases.filter', ['filters' => $filters, 'companies' => $companies, 'permissions' => $permissions, 'countries' => $countries, 'experts' => $experts, 'contractHolders' => $contractHolders, 'languageSkills' => $languageSkills, 'specializations' => $specializations]);
    }

    public function filter_process(Request $request)
    {
        $query_string = Cases::createFilterQueryString($request->except('_token'));

        return redirect()->route('operator.cases.filtered', $query_string);
    }

    public function filtered()
    {
        $attributes = Input::get('attributes');
        $inputs = Input::get('inputs');
        $expert = Input::get('expert');
        $contractHolder = Input::get('contract_holder_id');
        $orgId = Input::get('org_id');
        $consultation_date_from = Input::get('consultation_date_from');
        $consultation_date_to = Input::get('consultation_date_to');
        $cases = Cases::filter(
            attributes: $attributes,
            inputs: $inputs,
            expert: $expert,
            contract_holder_id: $contractHolder,
            org_id: $orgId,
            consultation_date_from: $consultation_date_from,
            consultation_date_to: $consultation_date_to
        );
        $cases = $cases->paginate(15);

        return view('operator.cases.list.filtered', ['cases' => $cases]);
    }

    public function list_in_progress()
    {
        $cases = Cases::query()->where('status', '!=', 'confirmed')
            ->where('status', '!=', 'client_unreachable_confirmed')
            ->where('status', '!=', 'interrupted')
            ->where('status', '!=', 'interrupted_confirmed')
            ->whereDoesntHave('consultations')
            ->with(['values', 'values.input', 'experts', 'consultations', 'company'])
            /* ->where('status', '!=', 'employee_contacted') */
            ->get();

        $cases = Cases::query()->where(function ($query): void {
            $query->where('status', '!=', 'confirmed')
                ->where('status', '!=', 'client_unreachable_confirmed')
                ->where('status', '!=', 'interrupted')
                ->where('status', '!=', 'interrupted_confirmed')
                ->whereDoesntHave('consultations');
        })
            ->orWhere(function ($query): void {
                $query->where('status', 'assigned_to_expert')
                    ->whereHas('consultations');
            })
            ->with(['values', 'values.input', 'experts', 'consultations', 'company'])
            ->get();

        $cases->each(function ($item, $key): void {
            if ($item->status == 'opened' || $item->status == 'assigned_to_expert') {
                $item->setAttribute('percentage', 0);
            } else {
                $case_type = $item->case_type->value;
                if (
                    $item->closed_by_expert ||
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
                            (in_array($case_type, [1, 2, 3]) && $item->customer_satisfaction != null) ||
                            (! in_array($case_type, [1, 2, 3]) && $item->customer_satisfaction_not_possible == 1)
                        )
                        &&
                        ($item->consultations->count() > 0)
                        && ! in_array($case_type, [1, 6, 7])
                    )
                ) {
                    $item->setAttribute('percentage', 100);
                } elseif ($item->consultations->count() > 0 && $item->getRawOriginal('status') == 'employee_contacted') {
                    $item->setAttribute('percentage', 66);
                } elseif ($item->getRawOriginal('status') == 'employee_contacted') {
                    $item->setAttribute('percentage', 33);
                }
            }
        });

        $cases = $cases->sortByDesc(fn ($case, $key) => $case->date);

        return view('operator.cases.list.in_progress', ['cases' => $cases]);
    }

    // ESET MEGTEKINTÉSE
    public function view($id)
    {
        $case = Cases::query()->findOrFail($id);
        $language_skills = LanguageSkill::query()->get()
            ->sortBy(fn ($query) => $query->translation->value);

        return view('operator.cases.view', ['case' => $case, 'language_skills' => $language_skills]);
    }

    public function assingNewValueToCaseInput(Request $request)
    {
        $case = Cases::query()->findOrFail($request->case_id);
        // if($case->created_by == \Auth::user()->id){
        if ($case->country_id == Auth::user()->country_id) {
            CaseValues::query()->where('case_id', $request->case_id)->where('case_input_id', $request->input_id)->update([
                'value' => $request->value,
            ]);

            return response()->json(['status' => 0]);
        }

        return response()->json(['status' => 1]);
    }

    public function export(Request $request)
    {
        if (! $request->cases) {
            return redirect()->back();
        }

        // $data = Cases::getExportData($request->cases);
        /*Excel::create('New file', function($excel) use ($data){
          $excel->sheet('New sheet', function($sheet) use ($data) {
            $sheet->loadView('excels.cases',['data' => $data]);
          });
        })->export('xls');*/
        return Excel::download(new ClosedCasesExport($request->cases), 'esetek.xlsx');
    }

    // ÚJ ESET LÉTREHOZÁSA
    public function create()
    {
        $case_inputs = CaseInput::query()->whereNotIn('id', [64])->where('company_id', null)->with('values')->get();
        $companies = Company::query()->orderBy('name', 'asc')
            ->where('active', 1)
            ->whereNotIn('id', config('filter-out-company-for-case'))
            ->get();
        $permissions = Permission::query()->get();
        $languageSkills = LanguageSkill::query()->get();
        $specializations = Specialization::query()->get();
        $consultation_types_values = CaseInput::query()->where('id', 24)->with('values')->first()->values; // 24 - consultation type

        $cities = City::query()->orderBy('name', 'asc')
            // When operator is not in a lifeworks/telus country, check if city has experts
            ->when(! in_array(auth()->user()->country_id, config('lifeworks-countries')), function ($query): void {
                $query->whereHas('experts', function ($query): void {
                    $query->where('type', 'expert')
                        ->where('active', 1)
                        ->where('locked', 0)
                        ->whereNotNull('last_login_at');
                });
            })
            ->get();

        $permissions = $permissions->sortBy(fn ($permission, $key) => $permission->translation->value);
        $languageSkills = $languageSkills->sortBy(fn ($languageSkills, $key) => optional($languageSkills->translation)->value);
        $specializations = $specializations->sortBy(fn ($specializations, $key) => optional($specializations->translation)->value);

        return view('operator.cases.new', ['case_inputs' => $case_inputs, 'companies' => $companies, 'permissions' => $permissions, 'cities' => $cities, 'languageSkills' => $languageSkills, 'specializations' => $specializations, 'consultation_types_values' => $consultation_types_values]);
    }

    public function edit_process($id, Request $request)
    {
        Cases::editCase($id, $request);

        return redirect()->route('operator.cases.view', ['id' => $id]);
    }

    public function edit($id)
    {
        $case = Cases::query()->findOrFail($id);
        $case_inputs = CaseInput::query()->whereNotIn('id', [64])->where('company_id', null)->get();
        $permissions = Permission::query()->get();
        $cities = City::query()->orderBy('name', 'asc')->get();

        return view('operator.cases.edit', ['case' => $case, 'case_inputs' => $case_inputs, 'permissions' => $permissions, 'cities' => $cities]);
    }

    public function get_company_permissions_and_steps(Request $request)
    {
        $permissions = Company::getPermissions($request->id);
        $steps = Company::getSteps($request->id);

        return response()->json(['status' => 0, 'steps' => $steps, 'permissions' => $permissions]);
    }

    public function get_contract_holder_by_company()
    {
        $org_data = OrgData::query()
            ->where('company_id', request()->company_id)
            ->where('country_id', request()->country_id)
            ->first();

        if (empty($org_data)) {
            return response()->json(['status' => 1]);
        }

        return response()->json(['status' => 0, 'contract_holder_id' => $org_data->contract_holder_id]);
    }

    public function get_available_expert_by_permission(Request $request)
    {
        $experts = User::query()->where('type', 'expert')->where('users.active', 1)
            ->leftJoin('user_x_permission', function ($join) use ($request): void {
                $join->on('users.id', '=', 'user_x_permission.user_id')->where('user_x_permission.permission_id', $request->permission_id);
            })
            ->leftJoin('user_x_city', function ($join) use ($request): void {
                $join->on('users.id', '=', 'user_x_city.user_id')->where('user_x_city.city_id', $request->city_id);
            })
            ->select('name', 'users.country_id as country', 'email', 'users.id', 'user_x_city.city_id as city_id', 'user_x_permission.permission_id as permission_id', 'users.last_login_at as last_login_at')
            // ->whereNotNUll('city_id')
            ->whereRaw('(user_x_city.city_id IS NOT NULL)')
            // ez az új szűrés
            // ->where('users.country_id',\Auth::user()->country_id)
            ->whereHas('expertCountries', function ($query): void {
                $query->where('country_id', Auth::user()->country_id);
            })
            ->whereNotNUll('permission_id')
            ->whereNotNull('last_login_at')
            ->where('locked', 0)
            ->orderBy('name', 'asc')
            ->get()->filter(function ($expert) use ($request) {
                if ($request->permission_id != 1) {
                    return true;
                }
                if ($expert->expert_data) {
                    return $expert->expert_data->can_accept_more_cases;
                }

                return false;
            });

        return response()->json(['status' => 0, 'experts' => $experts]);
    }

    public function get_available_experts(Request $request): JsonResponse
    {
        return response()->json(['status' => 0, 'experts' => query_available_experts(
            is_crisis: (int) $request->is_crisis == 1,
            permission_id: (int) $request->permission_id,
            country_id: (int) auth()->user()->country_id,
            city_id: (int) $request->city_id,
            specialization_id: (int) $request->specialization_id,
            language_skill_id: (int) $request->language_skill_id,
            consultation_minute: (int) $request->consultation_minute,
            is_personal: filter_var($request->is_personal, FILTER_VALIDATE_BOOLEAN),
            company_id: (int) $request->company_id,
            problem_details: (int) $request->problem_details
        )]);
    }

    public function store(Request $request)
    {
        $case = Cases::createCase($request);

        if ($case === null) {
            return redirect()->route('operator.cases.new');
        }

        return redirect()->route('operator.cases.created', ['id' => $case->id]);
    }

    public function created($id)
    {
        $case = Cases::query()->findOrFail($id);

        return view('operator.cases.created', ['case' => $case]);
    }

    public function sendMailToExpert(Request $request)
    {
        User::assignCase($request->case_id, $request->expert_id);

        return response()->json(['status' => 0]);
    }

    public function assign_single_session_expert(Request $request)
    {
        User::assign_single_session_case($request);

        return response()->json(['status' => 0]);
    }

    public function sendMailToLifeWorks(Request $request)
    {
        return $this->send_mail_to_lifeworks($request);
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
            'email_sent_3months' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]]);

        $isnerted_case_id = DB::getPdo()->lastInsertId();
        $new_expert_id = $request->experts;

        DB::table('expert_x_case')->insert([[
            'user_id' => $new_expert_id,
            'case_id' => $isnerted_case_id,
            //            'accepted' => "-1",
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
        // $case->experts()->wherePivot('accepted',-1)->detach();
        // $case->experts()->syncWithoutDetaching($request->expert_id);
        $case->experts()->attach($new_expert_id);

        Mail::to($user->email)->send(new AssignCaseMail($user, $case));

        return redirect()->back();
    }

    public function get_available_consultation_types(Request $request): ?Collection
    {
        $company = Company::query()
            ->where('id', $request->company_id)
            ->first();

        if ($company) {
            return $company->get_contact_types_by_permission($request->permission_id);
        }

        return null;
    }

    public function check_telekom_email_address(): array
    {
        if (! request()->get('email')) {
            return [
                'valid' => 0,
                'message' => __('common.telekom_email_invalid'),
            ];
        }

        // Check email domain
        $email_domain = Str::of(request()->get('email'))->after('@')->lower()->trim();
        $email_user = Str::of(request()->get('email'))->before('@')->lower()->trim();
        if (! in_array($email_domain, config('auth.telekom_email_domains'))) {
            return [
                'valid' => false,
                'message' => __('common.telekom_email_invalid'),
            ];
        }

        // Check if username already exists with other domain(s)
        $alt_emails = collect(config('auth.telekom_email_domains'))->diff($email_domain)->map(fn ($domain) => Str::of($email_user.'@'.$domain)->lower()->trim());

        if (DeutscheTelekomEmail::query()->whereIn('email', $alt_emails)->exists()) {
            return [
                'valid' => false,
                'message' => __('common.telekom_email_invalid'),
            ];
        }

        // Check if email was used 3 times before
        $telekom_email = DeutscheTelekomEmail::query()->where('email', Str::of(request()->get('email'))->lower()->trim())->first();

        if ($telekom_email) {
            $count = collect([$telekom_email->case_id_1, $telekom_email->case_id_2, $telekom_email->case_id_3])
                ->filter(fn ($case_id): bool => ! is_null($case_id))
                ->count();

            return [
                'valid' => $count < 3,
                'message' => ($count === 3) ? __('common.telekom_email_not_available') : '',
            ];
        }

        return [
            'valid' => true,
            'message' => '',
        ];
    }

    public function check_application_code_requirement(): array
    {
        if (! request()->get('company_id')) {
            return [
                'required' => false,
            ];
        }

        return [
            'required' => Cases::check_application_code_requirement(request()->get('company_id')),
        ];
    }

    public function check_application_code(): array
    {
        if (! request()->get('code') || ! request()->get('company_id')) {
            return [
                'valid' => false,
            ];
        }

        return [
            'valid' => Cases::check_application_code(request()->get('code'), request()->get('company_id')),
        ];
    }
}
