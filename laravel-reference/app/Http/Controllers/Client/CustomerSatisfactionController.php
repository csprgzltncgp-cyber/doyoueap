<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Support\Facades\Auth;

class CustomerSatisfactionController extends Controller
{
    public function show()
    {
        $company = Auth::user()->companies()->first();
        $connected_companies = $company->get_connected_companies();
        $calculated_indexes = collect([]);

        foreach ($company->countries as $country) {
            $calculated_values = collect([]);

            $company->customer_satisfactions->where('is_active', 1)->map(function ($customer_satisfaction) use ($country, $calculated_values): void {
                $calculated_values->push($customer_satisfaction->values()->where('country_id', $country->id)->avg('value'));
            });

            $value = $calculated_values->avg();

            $calculated_indexes->push([
                'value' => empty($value) ? 0 : round($value, 1),
                'country_id' => $country->id,
                'country_name' => $country->name,
            ]);
        }
        // When company is Superbet(717), need to hide all countries name except Romania, Poland and Croatia (6, 2, 12)
        if ($company->id == 717) {
            $calculated_indexes = $calculated_indexes->map(function (array $satisfaction, string $index): array {
                if (! in_array($satisfaction['country_id'], [2, 6, 12])) {
                    $satisfaction['country_name'] = __('common.country').' '.$index;
                }

                return $satisfaction;
            });
        } else {
            $countries = $company->countries;
        }

        return view('client.customer-satisfaction', ['calculated_indexes' => $calculated_indexes, 'connected_companies' => $connected_companies, 'current_company' => $company]);
    }
}
