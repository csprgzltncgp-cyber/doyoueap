<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\CustomerSatisfaction;
use Carbon\Carbon;

class CustomerSatisfactionController extends Controller
{
    public function index()
    {
        $from = Carbon::now()->startOfMonth();
        $to = Carbon::now()->endOfMonth();

        // check if we before the 10th day of the month
        if (Carbon::now()->day <= 10) {
            $from = Carbon::parse($from)->subMonthWithNoOverflow()->startOfMonth();
            $to = Carbon::parse($to)->subMonthWithNoOverflow()->endOfMonth();
        }

        $companies = Company::query()
            ->whereHas('org_datas', fn ($query) => $query->where('contract_holder_id', 2))
            ->get();

        foreach ($companies as $company) {
            $calculated_indexes = collect([]);

            foreach ($company->countries as $country) {
                $customer_satisfaction = CustomerSatisfaction::query()
                    ->where('company_id', $company->id)
                    ->where('from', $from->format('Y-m-d'))
                    ->where('to', $to->format('Y-m-d'))
                    ->first();

                if ($customer_satisfaction) {
                    $calculated_values = collect([]);

                    $company->customer_satisfactions->where('to', '<=', Carbon::parse($to)->endOfDay())->map(function ($customer_satisfaction) use ($country, $calculated_values): void {
                        $calculated_values->push($customer_satisfaction->values()->where('country_id', $country->id)->avg('value'));
                    });

                    $value = $calculated_values->avg();
                } else {
                    $value = $company->cases()
                        ->whereNotNull(['customer_satisfaction'])
                        ->where('country_id', $country->id)
                        ->whereHas('values', function ($query) use ($to): void {
                            $query->where('case_input_id', 1)
                                ->where('value', '>=', Carbon::parse('2022-03-01 00:00:00')->startOfDay())
                                ->where('value', '<=', Carbon::parse($to)->endOfDay());
                        })->avg('customer_satisfaction');
                }

                $calculated_indexes->put($country->id, empty($value) ? 'Nincs adat!' : round($value, 1));
            }

            $company->setAttribute('calculated_indexes', $calculated_indexes);
        }

        return view('admin.customer-satisfaction.index', ['companies' => $companies, 'from' => $from, 'to' => $to]);
    }
}
