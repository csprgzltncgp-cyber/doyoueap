<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Cases;
use App\Models\Country;
use App\Traits\Riport as RiportTrait;

class RiportController extends Controller
{
    use RiportTrait;

    public function show($quarter = null, ?Country $country = null)
    {
        $company = auth()->user()->companies()->first();
        $totalView = request('totalView') && has_connected_companies(auth()->user());

        // Store company user ID in session if the current company serves as a master for others
        if ($company->is_master_company()) {
            session(['masterCompanyAccountId' => auth()->user()->id]);
        }

        $normal_riport_data = $this->get_cached_riport_data($quarter, $country, null, $totalView);

        $in_progress_numbers = [];

        // get in progress cases
        Cases::query()
            ->when($totalView, fn ($query) => $query->whereIn('company_id', $company->get_connected_companies()->pluck('id')))
            ->when(! $totalView, fn ($query) => $query
                ->where('company_id', $company->id)
                ->where('country_id', $country->id ?? auth()->user()->country->id))
            ->get()->filter(fn ($case): bool => in_array($case->getRawOriginal('status'), ['opened', 'assigned_to_expert', 'employee_contacted']))->each(function ($case) use (&$in_progress_numbers): void {
                if (array_key_exists($case->values()->where('case_input_id', 7)->first()->value, $in_progress_numbers)) {
                    $in_progress_numbers[$case->values()->where('case_input_id', 7)->first()->value] += 1;
                } else {
                    $in_progress_numbers[$case->values()->where('case_input_id', 7)->first()->value] = 1;
                }
            });

        // Set original client user for when switching between different connected company users.
        if (! session('originalClient')) {
            session(['originalClient' => auth()->id()]);
        }

        return view('client.riport', ['country' => $country, 'normal_riport_data' => $normal_riport_data, 'company' => $company, 'in_progress_numbers' => $in_progress_numbers, 'totalView' => $totalView]);
    }

    public function login_to_connected_company()
    {
        request()->validate([
            'user_id' => 'required',
            'route' => 'required',
        ]);

        auth()->loginUsingId(request('user_id'));

        return redirect(route(request()->input('route')));
    }
}
