<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\ContractHolder;
use App\Models\Riport;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class RiportController extends Controller
{
    use \App\Traits\Riport;

    public function index()
    {
        $contract_holders = ContractHolder::query()->orderBy('name')->get();
        $curr_month = Carbon::now()->startOfMonth();

        $contract_holder_months = CarbonPeriod::create('2022-01-01', '1 month', '2022-12-30');
        $contract_holder_years = $this->get_contract_holder_years();

        return view('admin.riports.index', ['contract_holders' => $contract_holders, 'curr_month' => $curr_month, 'contract_holder_months' => $contract_holder_months, 'contract_holder_years' => $contract_holder_years]);
    }

    public function create()
    {
        $from = Carbon::now()->startOfMonth();
        $to = Carbon::now()->endOfMonth();

        $companies = Company::query()
            ->whereHas('org_datas', fn ($query) => $query->where('contract_holder_id', 2))
            ->get();

        foreach ($companies as $company) {
            $company_case_numbers = collect([]);

            foreach ($company->countries as $country) {
                $riport = Riport::query()
                    ->where('company_id', $company->id)
                    ->where('from', $from->format('Y-m-d'))
                    ->where('to', $to->format('Y-m-d'))
                    ->first();

                // if riport is already generated
                if ($riport) {
                    $company_case_numbers->put($country->id, $this->generate_riport_values($country, $riport, true));
                } else { // if riport is not generated count numbers from cases
                    $case_numbers = collect([]);
                    // In progress cases -> 'opened', 'assigned_to_expert', 'employee_contacted'
                    $case_numbers->put(
                        'in_progress',
                        $company->cases()
                            ->whereIn('status', ['opened', 'assigned_to_expert', 'employee_contacted'])
                            ->where('country_id', $country->id)
                            ->count()
                    );

                    // Closed cases -> 'confirmed'
                    $case_numbers->put(
                        'closed',
                        $company->cases()
                            ->whereIn('status', ['confirmed'])
                            ->whereHas('values', function ($query) use ($from, $to): void {
                                $query->where('case_input_id', 1)->whereBetween('value', [$from, $to]);
                            })->where('country_id', $country->id)
                            ->count()
                    );

                    // Interrupted cases -> 'interrupted', 'interrupted_confirmed'
                    $case_numbers->put(
                        'interrupted',
                        $company->cases()
                            ->whereIn('status', ['interrupted', 'interrupted_confirmed'])
                            ->whereHas('values', function ($query) use ($from, $to): void {
                                $query->where('case_input_id', 1)->whereBetween('value', [$from, $to]);
                            })->where('country_id', $country->id)
                            ->count()
                    );

                    // Client unreachable cases -> 'client_unreachable', 'client_unreachable_confirmed'
                    $case_numbers->put(
                        'client_unreachable',
                        $company->cases()
                            ->whereIn('status', ['client_unreachable', 'client_unreachable_confirmed'])
                            ->whereHas('values', function ($query) use ($from, $to): void {
                                $query->where('case_input_id', 1)->whereBetween('value', [$from, $to]);
                            })->where('country_id', $country->id)
                            ->count()
                    );

                    $company_case_numbers->put($country->id, ['case_numbers' => $case_numbers]);
                }
            }

            $company->setAttribute('case_numbers', $company_case_numbers);
        }

        return view('admin.riports.create', ['companies' => $companies, 'from' => $from, 'to' => $to]);
    }

    public function show()
    {
        request()->validate([
            'user_id' => 'required',
            'url' => 'required',
        ]);

        if (str_contains((string) Auth::user()->type, 'admin')) {
            session(['myAdminId' => Auth::id()]);
            session(['myAdminLastUrl' => url()->previous()]);
        }

        Auth::loginUsingId(request('user_id'));

        return redirect(request('url'));
    }

    public function activate()
    {
        if (! request()->has('companyIds')) {
            return response()->json(['status' => 1]);
        }

        foreach (request()->input('companyIds') as $companyId) {
            Riport::query()
                ->updateOrCreate(
                    [
                        'company_id' => $companyId,
                        'from' => request()->input('from'),
                        'to' => request()->input('to'),
                    ],
                    [
                        'is_active' => 1,
                    ]
                );
        }

        return response()->json(['status' => 0]);
    }

    public function deactivate()
    {
        if (! request()->has('companyIds')) {
            return response()->json(['status' => 1]);
        }

        foreach (request()->input('companyIds') as $companyId) {
            Riport::query()
                ->where('company_id', $companyId)
                ->where('from', request()->input('from'))
                ->where('to', request()->input('to'))
                ->update(['is_active' => 0]);
        }

        return response()->json(['status' => 0]);
    }

    private function get_contract_holder_years()
    {
        try {
            $contract_holder_exports = File::files(storage_path('app').'/contract-holder-exports/1');
        } catch (Exception) {
            return [now()];
        }

        $year = now()->year;

        foreach ($contract_holder_exports as $export) {
            $curr_year = (int) Str::of(pathinfo($export)['filename'])->beforeLast('-')->after('-')->__toString();

            if ($curr_year < $year) {
                $year = $curr_year;
            }
        }

        return collect(CarbonPeriod::create($year.'-01-01', '1 year', now())->toArray())->reverse();
    }
}
