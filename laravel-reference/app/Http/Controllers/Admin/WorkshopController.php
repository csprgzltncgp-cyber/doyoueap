<?php

namespace App\Http\Controllers\Admin;

use App\Enums\WorkshopCaseExpertStatus;
use App\Enums\WorkshopCaseStatus;
use App\Http\Controllers\Controller;
use App\Mail\ExpertWorkshopMail;
use App\Mail\ExpertWorkshopPriceChangeMail;
use App\Models\City;
use App\Models\Company;
use App\Models\Country;
use App\Models\DirectInvoiceWorkshopData;
use App\Models\InvoiceWorkshopData;
use App\Models\User;
use App\Models\Workshop;
use App\Models\WorkshopCase;
use App\Models\WorkshopCaseEvent;
use App\Services\WorkshopService;
use App\Traits\ContractDateTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class WorkshopController extends Controller
{
    use ContractDateTrait;

    public function index(WorkshopService $workshop_service)
    {
        $categories = $workshop_service->get_index_categories();

        return view('admin.workshops.list_all', $categories);
    }

    public function get_workshops(Request $request)
    {
        $take = 10;
        $skip = ($request->page - 1) * $take;
        $workshops = WorkshopCase::query()
            ->whereBetween('date', [
                Carbon::parse(Str::replace('_', '-', $request->date))->startOfMonth(),
                Carbon::parse(Str::replace('_', '-', $request->date))->endOfMonth(),
            ])->orderByDesc('date')->get();

        if (! filter_var($request->all, FILTER_VALIDATE_BOOLEAN)) {
            $workshops = $workshops->slice($skip, $take);
        } else {
            $workshops = $workshops->slice($skip);
        }

        $views = collect([]);

        foreach ($workshops as $workshop) {
            $views->push(
                view(
                    'components.workshops.workshop_case_component',
                    [
                        'workshop_case' => $workshop,
                    ]
                )->render()
            );
        }

        return response()->json(['html' => $views]);
    }

    public function view($id)
    {
        $countries = Country::query()->orderBy('name', 'desc')->get();
        $companies = Company::query()->orderBy('name', 'desc')->get();
        $cities = City::query()->orderBy('name', 'asc')->get();
        $workshop_case = WorkshopCase::query()->where('id', $id)->first();
        $experts = User::query()
            ->where('type', 'expert')
            ->whereHas('outsource_countries', fn ($query) => $query->where('country_id', $workshop_case->country_id))
            ->orderBy('name', 'asc')->get();

        $overall_feedback = $workshop_case->feedbacks->avg(fn ($feedback): int|float => ($feedback->question_1 + $feedback->question_2 + $feedback->question_3 + $feedback->question_4 + $feedback->question_5) / 5) ?? null;

        $expert_currency = $workshop_case->expert_currency;

        // IF expert currency is missing from workshop case than get it from the expert's invoice datas
        if (! $expert_currency && $workshop_case->user) {
            $expert_currency = optional($workshop_case->user)->invoice_datas->currency;
        }

        return view('admin.workshops.view', [
            'workshop_case' => $workshop_case,
            'cities' => $cities,
            'countries' => $countries,
            'companies' => $companies,
            'experts' => $experts,
            'prefix' => 'admin',
            'is_outsorceable' => WorkshopCase::query()->where('id', $workshop_case->id)->first()->is_outsourceable(),
            'is_outsourced' => WorkshopCase::query()->where('id', $workshop_case->id)->first()->is_outsourced(),
            'overall_feedback' => round($overall_feedback, 2),
            'expert_currency' => $expert_currency,
        ]);
    }

    public function create()
    {
        $active_companies = DB::table('org_data')
            ->leftJoin('companies', 'org_data.company_id', '=', 'companies.id')
            ->leftJoin('countries', 'org_data.country_id', '=', 'countries.id')
            ->where('companies.active', 1)
            ->where('org_data.workshops_number', '>', 0)
            ->select([
                'companies.name as company_name',
                'companies.id as company_id',
                'countries.code as country_code',
                'countries.id as country_id',
                'org_data.contract_date',
            ])
            ->orderBy('company_name')
            ->get();

        foreach ($active_companies as $company) {
            $period_end = $this->getPeriodEnd($company->contract_date);
            $period_start = $this->getPeriodStart($company->contract_date);
            $company->period_end = $period_end;
            $company->period_start = $period_start;
            $company->workshops_number = Workshop::query()
                ->where(['active' => 1, 'company_id' => $company->company_id, 'country_id' => $company->country_id])
                ->whereBetween('created_at', [$period_start, $period_end])->count();
        }

        $workshops = Workshop::query()->where('active', 1)->get();
        $cities = City::query()->orderBy('name', 'asc')->get();
        $countries = Country::query()->orderBy('name', 'asc')->get();
        $experts = DB::table('users')->orderBy('name', 'asc')
            ->where('users.type', 'expert')
            ->where('users.deleted_at', null)
            ->leftJoin('expert_x_country', 'users.id', 'expert_x_country.expert_id')
            ->leftJoin('countries', 'expert_x_country.country_id', 'countries.id')
            ->leftJoin('invoice_datas', 'users.id', 'invoice_datas.user_id')
            ->leftJoin('expert_datas', 'users.id', 'expert_datas.user_id')
            ->select('users.name',
                'users.id as user_id',
                'countries.id as country_id',
                'countries.name as country_name',
                'expert_x_country.country_id as expert_country_id',
                'users.email as user_email',
                'invoice_datas.currency as currency',
                'expert_datas.is_cgp_employee as is_cgp_employee',
            )
            ->get();

        return view('admin.workshops.new', [
            'cities' => $cities,
            'countries' => $countries,
            'workshops' => $workshops,
            'active_companies' => $active_companies,
            'experts' => $experts,
        ]);
    }

    public function store(Request $request)
    {
        // Check if workshop activity id already exists in workshop_cases
        if ($request->input('activity_id') && WorkshopCase::query()->where('activity_id', $request->input('activity_id'))->exists()) {
            session()->flash('activity-id-exists', Str::replace('#activity_id', $request->input('activity_id'), __('workshop.msg_activity_id_exists')));

            return redirect()->back();
        }

        $workshop_case = WorkshopCase::query()->create();
        $workshop_case->fill(array_merge($request->except('_token'), ['status' => WorkshopCaseStatus::OUTSOURCED, 'created_by' => Auth()->user()->id]));
        $workshop_case->invoiceable_after = Carbon::parse($request->input('date'))->endOfMonth()->addDay();
        $workshop_case->save();

        if (! empty($request->input('expert_id'))) {
            $user = User::query()->findOrFail($request->expert_id);
            Mail::to($user->email)->send(new ExpertWorkshopMail($user, $workshop_case));
        }

        Workshop::query()->where([
            'activity_id' => $request->activity_id,
        ])->update(['active' => 0]);

        return redirect()->route(auth()->user()->type.'.workshops.list');
    }

    public function update(Request $request, $id)
    {
        $full_time_diff = Carbon::parse($request->start_time)->diffInSeconds(Carbon::parse($request->end_time));
        $full_time = gmdate('H:i', $full_time_diff); // gmdate only works if the number of seconds is not bigger than a day!
        $input_type = $request->input;
        $workshop_case = WorkshopCase::query()->where('id', $id)->first();

        if ($input_type == 'date') {
            $current = $workshop_case->invoiceable_after ?? Carbon::parse($workshop_case->date)->endOfMonth();
            $updated = Carbon::parse($request->input('date'))->endOfMonth();

            if ($current->lt($updated)) {
                $workshop_case->update([
                    'invoiceable_after' => $updated,
                ]);
            }
        }

        if ($input_type == 'expert') {
            $workshop_case->update([
                'expert_id' => $request->expert,
                'expert_status' => null,
            ]);

            $user = User::query()->findOrFail($request->expert);
            Mail::to($user->email)->send(new ExpertWorkshopMail($user, $workshop_case));
            WorkshopCaseEvent::query()->where(['workshop_case_id' => $id])->delete();
            session()->flash('expert-outsourced', true);
        } elseif ($input_type == 'start_time') {
            $workshop_case->update([
                'start_time' => $request->start_time,
                'full_time' => $full_time,
            ]);
        } elseif ($input_type == 'end_time') {
            $workshop_case->update([
                'end_time' => $request->end_time,
                'full_time' => $full_time,
            ]);
        } elseif ($input_type == 'select_out_price') {
            $workshop_case->update([
                'expert_price' => filter_var($request->expert_price, FILTER_SANITIZE_NUMBER_INT), // Filter out non numeric characters
                'expert_currency' => $request->expert_currency,
                'expert_status' => WorkshopCaseExpertStatus::ADMIN_PRICE_CHANGE,
            ]);

            DB::table('workshop_case_events')
                ->where('workshop_case_id', $id)
                ->update([
                    'event' => 'workshop_case_price_modified_by_admin',
                ]);

            $user_id = DB::table('users')
                ->select(['users.id'])
                ->leftJoin('workshop_cases', 'users.id', 'workshop_cases.expert_id')
                ->where(['workshop_cases.id' => $id])->first();

            $user = User::query()->findOrFail($user_id->id);
            Mail::to($user->email)->send(new ExpertWorkshopPriceChangeMail($user, $workshop_case));
        } elseif ($input_type === 'activity_id') {
            $workshop_case = WorkshopCase::query()->where('id', $id)->first();

            Workshop::query()
                ->where('activity_id', $workshop_case->activity_id)
                ->update([
                    'activity_id' => $request->activity_id,
                    'active' => 0,
                ]);

            $workshop_case->update([
                'activity_id' => $request->activity_id,
            ]);
        } elseif ($input_type === 'contract_price') {
            if ($request->input('is_free') == '1') {
                $workshop_case->workshop->update([
                    'valuta' => null,
                    'workshop_price' => null,
                    'free' => 1,
                ]);
            } else {
                $workshop_case->workshop->update([
                    'valuta' => $request->input('valuta'),
                    'workshop_price' => $request->input('price'),
                    'free' => 0,
                ]);
            }
        } else {
            $workshop_case->update([
                $input_type => $request->{$input_type},
            ]);
        }

        return redirect()->route(auth()->user()->type.'.workshops.view', $id);
    }

    public function close($id)
    {
        $workshop_case = WorkshopCase::query()->where('id', $id)->first();
        $is_cgp_employee = $workshop_case->user->expert_data->is_cgp_employee;

        // IF expert currency is missing from workshop case than get it and set it from the expert's invoice datas
        if (! $workshop_case->expert_currency && ! $is_cgp_employee) {
            $expert_currency = optional($workshop_case->user)->invoice_datas->currency;
            $workshop_case->update(['expert_currency' => $expert_currency]);
        }

        if (empty($workshop_case->activity_id)
            || ! $is_cgp_employee && is_null($workshop_case->expert_price)
            || ! $is_cgp_employee && is_null($workshop_case->expert_currency)
            || is_null($workshop_case->expert_id)
        ) {
            session()->flash('data_error', true);

            return redirect()->route(auth()->user()->type.'.workshops.view', $id);
        }

        $workshop_case->closed_at = now();
        $workshop_case->status = WorkshopCaseStatus::CLOSED;
        $workshop_case->save();

        InvoiceWorkshopData::query()->create([
            'workshop_case_id' => $id,
            'activity_id' => $workshop_case->activity_id,
            'price' => (is_null($workshop_case->expert_price)) ? 0 : $workshop_case->expert_price,
            'currency' => (is_null($workshop_case->expert_currency)) ? 0 : $workshop_case->expert_currency,
            'expert_id' => $workshop_case->expert_id,
        ]);

        if (! $workshop_case->workshop->free) {
            DirectInvoiceWorkshopData::query()->create([
                'workshop_id' => $workshop_case->workshop->id,
                'company_id' => $workshop_case->workshop->company_id,
                'country_id' => $workshop_case->workshop->country_id,
                'invoiceable_after' => $workshop_case->invoiceable_after,
            ]);
        }

        return redirect()->route(auth()->user()->type.'.workshops.list');
    }

    public function accept_expert_price($id)
    {
        $final_price = WorkshopCase::query()->where('id', $id)->first();

        WorkshopCase::query()
            ->where('id', $id)
            ->update([
                'price' => $final_price->expert_price,
                'currency' => $final_price->expert_currency,
                'expert_status' => WorkshopCaseExpertStatus::ACCEPTED,
                'status' => WorkshopCaseStatus::PRICE_ACCEPTED,
            ]);

        DB::table('workshop_case_events')->where(['workshop_case_id' => $id])->update([
            'event' => 'workshop_case_accepted_by_admin',
        ]);

        return redirect()->route(auth()->user()->type.'.workshops.list');
    }

    public function filter()
    {
        $companies = Company::query()
            ->whereHas('workshop_cases')
            ->select(['name', 'companies.id'])
            ->orderBy('name')
            ->get();

        $experts = User::query()
            ->whereHas('workshop_cases')
            ->where(['users.type' => 'expert', 'users.active' => 1])
            ->select(['name', 'id'])
            ->orderBy('name')
            ->get();

        return view('admin.workshops.filter')->with(['companies' => $companies, 'experts' => $experts]);
    }

    public function filterResult(Request $request)
    {
        $filters = array_filter($request->all());

        $workshop_cases = WorkshopCase::query()->get();

        foreach ($filters as $key => $value) {
            if ($key == 'date') {
                if (! empty($value[0]) && ! empty($value[1])) {
                    $workshop_cases = $workshop_cases->whereBetween('date', [$value[0], $value[1]]);
                }
            } else {
                $workshop_cases = $workshop_cases->where($key, $value);
            }
        }

        return view('admin.workshops.result')->with(['workshop_cases' => $workshop_cases]);
    }

    public function delete($id)
    {
        $wsc = WorkshopCase::query()->where('id', $id)->first();
        WorkshopCaseEvent::query()->where('workshop_case_id', $id)->delete();
        WorkshopCase::query()->where('id', $id)->delete();
        Workshop::query()->where('activity_id', $wsc->activity_id)->update(['active' => 1]);

        return redirect()->route(auth()->user()->type.'.workshops.list');
    }

    public function setWorkshopToPaid(Request $request)
    {
        $workshop_case = WorkshopCase::query()->findOrFail($request->get('id'));
        $workshop_case->closed = 1;
        $workshop_case->save();

        return response('ok!');
    }

    public function check_expert_availability(Request $request)
    {
        // Check if expert has others workshop with the same date.
        $exists = WorkshopCase::query()
            ->where('expert_id', $request->expert_id)
            ->where('date', $request->date)
            ->exists();

        if ($exists) {
            return response(['status' => 0]);
        }

        return response(['status' => 1]);
    }

    public function get_experts_by_outsource_country(Request $request)
    {
        $request->validate([
            'country_id' => 'required|exists:countries,id',
        ]);

        return User::query()
            ->with(['expert_data', 'invoice_datas', 'expertCountries'])
            ->where('type', 'expert')
            ->where('deleted_at', null)
            ->whereHas('outsource_countries', fn ($query) => $query->where('country_id', $request->country_id))
            ->orderBy('name', 'asc')
            ->get()->map(fn ($user): array =>
                // Faltten to one dimensional array with column aliases
                [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'user_email' => $user->email,
                    'currency' => optional($user->invoice_datas)->currency,
                    'is_cgp_employee' => optional($user->expert_data)->is_cgp_employee,
                    'expert_country_id' => optional($user->expertCountries->first())->id,
                ]);
    }
}
