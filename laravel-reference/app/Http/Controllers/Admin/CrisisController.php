<?php

namespace App\Http\Controllers\Admin;

use App\Enums\CrisisCaseStatus;
use App\Http\Controllers\Controller;
use App\Mail\ExpertCrisisInterventionMail;
use App\Mail\ExpertCrisisInterventionPriceChangeMail;
use App\Models\City;
use App\Models\Company;
use App\Models\Country;
use App\Models\CrisisCase;
use App\Models\CrisisCaseEvent;
use App\Models\CrisisIntervention;
use App\Models\DirectInvoiceCrisisData;
use App\Models\InvoiceCrisisData;
use App\Models\User;
use App\Services\CrisisService;
use App\Traits\ContractDateTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CrisisController extends Controller
{
    use ContractDateTrait;

    public function index(CrisisService $crisis_service)
    {
        $categories = $crisis_service->get_index_categories();

        return view('admin.crisis.list_all', $categories);
    }

    public function get_crisis_interventions(Request $request)
    {
        $take = 10;
        $skip = ($request->page - 1) * $take;
        $crisis_interventions = CrisisCase::query()
            ->whereBetween('date', [
                Carbon::parse(Str::replace('_', '-', $request->date))->startOfMonth(),
                Carbon::parse(Str::replace('_', '-', $request->date))->endOfMonth(),
            ])->orderByDesc('date')->get();

        if (! filter_var($request->all, FILTER_VALIDATE_BOOLEAN)) {
            $crisis_interventions = $crisis_interventions->slice($skip, $take);
        } else {
            $crisis_interventions = $crisis_interventions->slice($skip);
        }

        $views = collect([]);

        foreach ($crisis_interventions as $crisis_intervention) {
            $views->push(
                view(
                    'components.crisises.crisis_case_component',
                    [
                        'crisis_case' => $crisis_intervention,
                    ]
                )->render()
            );
        }

        return response()->json(['html' => $views]);
    }

    public function create()
    {
        $active_companies = DB::table('org_data')
            ->leftJoin('companies', 'org_data.company_id', '=', 'companies.id')
            ->leftJoin('countries', 'org_data.country_id', '=', 'countries.id')
            ->where('companies.active', 1)
            ->where('org_data.crisis_number', '>', 0)
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
            $company->crisis_number = CrisisIntervention::query()
                ->where(['active' => 1, 'company_id' => $company->company_id, 'country_id' => $company->country_id])
                ->whereBetween('created_at', [$period_start, $period_end])->count();
        }

        $crisis_interventions = CrisisIntervention::query()
            ->leftJoin('countries', 'crisis_interventions.country_id', '=', 'countries.id')
            ->select('crisis_interventions.*', 'countries.name as name')
            ->where('active', 1)
            ->get();
        $cities = City::query()->orderBy('name', 'asc')->get();

        $countries = Country::query()->orderBy('name', 'asc')->get();

        $experts = User::query()->orderBy('name', 'asc')
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

        $data = [
            'cities' => $cities,
            'countries' => $countries,
            'crisis_interventions' => $crisis_interventions,
            'active_companies' => $active_companies,
            'experts' => $experts,
        ];

        return view('admin.crisis.new')->with($data);
    }

    public function store(Request $request)
    {
        $crisis_case = CrisisCase::query()->create();
        $crisis_case->fill(array_merge($request->except('_token'), ['status' => 1]));
        $crisis_case->save();

        if (! empty($request->input('expert_id'))) {
            $user = User::query()->findOrFail($request->expert_id);
            Mail::to($user->email)->send(new ExpertCrisisInterventionMail($user, $crisis_case));
        }

        CrisisIntervention::query()->where([
            'activity_id' => $request->activity_id,
        ])->update(['active' => 0]);

        return redirect()->route('admin.crisis.list');
    }

    public function view(CrisisCase $crisis_case)
    {
        $countries = Country::query()->orderBy('name', 'desc')->get();
        $companies = Company::query()->orderBy('name', 'desc')->get();
        $cities = City::query()->orderBy('name', 'desc')->get();
        $experts = User::query()
            ->where('type', 'expert')
            ->whereHas('outsource_countries', fn ($query) => $query->where('country_id', $crisis_case->country_id))
            ->orderBy('name', 'asc')->get();

        $expert_currency = $crisis_case->expert_currency;

        // IF expert currency is missing from workshop case than get it from the expert's invoice datas
        if (! $expert_currency && $crisis_case->user) {
            $expert_currency = optional($crisis_case->user->invoice_datas)->currency;
        }

        return view('admin.crisis.view', [
            'crisis_case' => $crisis_case,
            'cities' => $cities,
            'countries' => $countries,
            'companies' => $companies,
            'experts' => $experts,
            'is_outsorceable' => CrisisCase::query()->where('id', $crisis_case->id)->first()->is_outsourceable(),
            'is_outsourced' => CrisisCase::query()->where('id', $crisis_case->id)->first()->is_outsourced(),
            'expert_currency' => $expert_currency,
        ]);
    }

    public function update(Request $request, $id)
    {
        $start = strtotime((string) $request->start_time);
        $end = strtotime((string) $request->end_time);
        $neg = strtotime('4:00');
        $elapsed = $end - $start;
        $test = $elapsed - $neg;
        $full_time = date('H:i', $test);
        $input_type = $request->input;
        $crisis_case = CrisisCase::query()->where('id', $id)->first();

        if ($input_type == 'expert') {
            $crisis_case->update([
                'expert_id' => $request->expert,
                'expert_status' => null,
            ]);

            $user = User::query()->findOrFail($request->expert);
            if (isset($crisis_case->expert_phone)) {
                Mail::to($user->email)->send(new ExpertCrisisInterventionMail($user, $crisis_case));
                CrisisCaseEvent::query()->where(['crisis_case_id' => $id])->delete();
            }

            CrisisCaseEvent::query()->where(['crisis_case_id' => $id, 'event' => 'crisis_case_price_modified_by_expert'])->delete();
        } elseif ($input_type == 'start_time') {
            CrisisCase::query()
                ->where('id', $id)
                ->update([
                    'start_time' => $request->start_time,
                    'full_time' => $full_time,
                ]);
        } elseif ($input_type == 'end_time') {
            CrisisCase::query()
                ->where('id', $id)
                ->update([
                    'end_time' => $request->end_time,
                    'full_time' => $full_time,
                ]);
        } elseif ($input_type == 'select_out_price') {
            CrisisCase::query()
                ->where('id', $id)
                ->update([
                    'expert_price' => filter_var($request->expert_price, FILTER_SANITIZE_NUMBER_INT), // Filter out non numeric characters
                    'expert_currency' => $request->expert_currency,
                    'expert_status' => 3,
                ]);

            CrisisCaseEvent::query()
                ->where('crisis_case_id', $id)
                ->update([
                    'event' => 'crisis_case_price_modified_by_admin',
                ]);

            $user_id = User::query()
                ->select(['users.id'])
                ->leftJoin('crisis_cases', 'users.id', 'crisis_cases.expert_id')
                ->where(['crisis_cases.id' => $id])->first();

            $user = User::query()->findOrFail($user_id->id);

            Mail::to($user->email)->send(new ExpertCrisisInterventionPriceChangeMail($user, $crisis_case));
        } elseif ($input_type === 'activity_id') {
            CrisisIntervention::query()
                ->where('activity_id', $crisis_case->activity_id)
                ->update([
                    'activity_id' => $request->activity_id,
                    'active' => 0,
                ]);

            $crisis_case->update([
                'activity_id' => $request->activity_id,
            ]);
        } else {
            $crisis_case->update([
                $input_type => $request->{$input_type},
            ]);
        }

        if ($input_type == 'expert') {
            session()->flash('expert-outsourced', true);
        }

        return redirect()->route('admin.crisis.view', $id);
    }

    public function close($id)
    {
        $crisis_case = CrisisCase::query()->where('id', $id)->first();

        // VALIDATE crisis case data
        try {
            Validator::make($crisis_case->toArray(),
                [
                    'expert_price' => 'required',
                    'expert_currency' => 'required',
                ],
                [
                    'expert_currency' => __('crisis.msg_crisis_expert_currency_required'),
                ]
            )->validate();
        } catch (ValidationException $e) {
            session()->flash('missing_crisis_data', $e->getMessage());

            return redirect()->back();
        }
        // VALIDATE crisis case data

        $crisis_case->closed_at = now();
        $crisis_case->status = CrisisCaseStatus::CLOSED;
        $crisis_case->save();

        InvoiceCrisisData::query()->create([
            'crisis_case_id' => $id,
            'activity_id' => $crisis_case->activity_id,
            'price' => $crisis_case->expert_price,
            'currency' => $crisis_case->expert_currency,
            'expert_id' => $crisis_case->expert_id,
        ]);

        if (! $crisis_case->crisis_intervention->free) {
            DirectInvoiceCrisisData::query()->create([
                'crisis_id' => $crisis_case->crisis_intervention->id,
                'company_id' => $crisis_case->crisis_intervention->company_id,
                'country_id' => $crisis_case->crisis_intervention->country_id,
            ]);
        }

        return redirect()->route('admin.crisis.list');
    }

    public function accpet_expert_price($id)
    {
        $final_price = CrisisCase::query()->where('id', $id)->first();

        CrisisCase::query()
            ->where('id', $id)
            ->update([
                'price' => $final_price->price,
                'currency' => $final_price->expert_currency,
                'expert_status' => 1,
                'status' => CrisisCaseStatus::PRICE_ACCEPTED,
            ]);

        CrisisCaseEvent::query()->where(['crisis_case_id' => $id])->update([
            'event' => 'crisis_case_accepted_by_admin',
        ]);

        return redirect()->route('admin.crisis.list');
    }

    public function filter()
    {
        $companies = Company::query()
            ->whereHas('crisis_cases')
            ->select(['name', 'companies.id'])
            ->orderBy('name')
            ->get();

        $experts = User::query()
            ->whereHas('crisis_cases')
            ->where(['users.type' => 'expert', 'users.active' => 1])
            ->select(['name', 'id'])
            ->orderBy('name')
            ->get();

        return view('admin.crisis.filter')->with(['companies' => $companies, 'experts' => $experts]);
    }

    public function filterResult(Request $request)
    {
        $filters = array_filter($request->all());

        $crisis_cases = CrisisCase::query()->get();

        foreach ($filters as $key => $value) {
            if ($key == 'date') {
                if (! empty($value[0]) && ! empty($value[1])) {
                    $crisis_cases = $crisis_cases->whereBetween('date', [$value[0], $value[1]]);
                }
            } else {
                $crisis_cases = $crisis_cases->where($key, $value);
            }
        }

        return view('admin.crisis.result')->with(['crisis_cases' => $crisis_cases]);
    }

    public function delete($id)
    {
        $cc = CrisisCase::query()->where('id', $id)->first();
        CrisisCaseEvent::query()->where('crisis_case_id', $id)->delete();
        CrisisCase::query()->where('id', $id)->delete();
        CrisisIntervention::query()->where('activity_id', $cc->activity_id)->update(['active' => 1]);

        return redirect()->route('admin.crisis.list');
    }

    public function setCrisisToPaid(Request $request)
    {
        $workshop_case = CrisisCase::query()->findOrFail($request->get('id'));
        $workshop_case->closed = 1;
        $workshop_case->save();

        return response('ok!');
    }
}
