<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\CrisisIntervention;
use App\Traits\ContractDateTrait;
use App\Traits\ReadableTimeTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CrisisController extends Controller
{
    use ContractDateTrait;
    use ReadableTimeTrait;

    public function index()
    {
        try {
            $country_id = auth()->user()->country_id;
            $user_company = auth()->user()->companies->first();
            $connected_companies = $user_company->get_connected_companies();

            $data = [];

            $company_crisis_interventions = CrisisIntervention::query()
                ->with('crisis_case')
                ->where('company_id', $user_company->id)
                ->when(! auth()->user()->all_country, function ($query) use ($country_id): void {
                    $query->where('country_id', $country_id);
                })
                ->orderBy('active', 'desc')
                ->get();

            $data = [
                'crisis_interventions' => $company_crisis_interventions,
            ];
        } catch (ModelNotFoundException) {
            abort(404);
        }

        foreach ($company_crisis_interventions as $crisis) {
            if ($crisis->crisis_case) {
                $crisis->crisis_case->full_time = $this->readable_time($crisis->crisis_case->full_time);
            }
        }

        if ($user_company->id == 717) {
            $countries = $user_company->countries->map(function ($country, string $index) {
                if (! in_array($country->id, [2, 6, 12])) {
                    $country->name = __('common.country').' '.$index;
                }

                return $country;
            });
        } else {
            $countries = $user_company->countries;
        }

        $data['connected_companies'] = $connected_companies;
        $data['company'] = $user_company;
        $data['countries'] = $countries;

        return view('client.crisis')->with($data);
    }
}
