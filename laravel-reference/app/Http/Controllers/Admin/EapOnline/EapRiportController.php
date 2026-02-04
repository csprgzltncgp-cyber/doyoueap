<?php

namespace App\Http\Controllers\Admin\EapOnline;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Country;
use App\Models\EapOnline\EapRiport;
use App\Models\EapOnline\Statistics\EapLogin;
use App\Traits\EapOnline\Riport;
use Carbon\Carbon;

class EapRiportController extends Controller
{
    use Riport;

    public function create()
    {
        $interval = collect($this->get_eap_online_riport_intervals())->last();
        $from = $interval['from'];
        $to = $interval['to'];

        $companies = Company::query()->whereHas('org_datas', fn ($query) => $query->where('contract_holder_id', 2))->with(['countries', 'eap_riports' => fn ($query) => $query->where('from', $from)->where('to', $to)])->get();

        $companies->each(function ($company) use ($from, $to): void {
            $company->countries->each(function (Country &$country) use ($company, $from, $to): void {
                if ($company->eap_riports->first()) {
                    $login_riport = $company->eap_riports->first()->eap_riport_values()->where('statistics', EapLogin::class)->where('country_id', $country->id)->first();
                    $country['login_number'] = optional($login_riport)->count;
                } else {
                    $login_count = EapLogin::query()
                        ->whereBetween('created_at', [Carbon::parse($from)->startOfDay(), Carbon::parse($to)->endOfDay()])
                        ->whereIn('user_id', $company->eap_users->pluck('id')->toArray())
                        ->where('country_id', $country->id)
                        ->count();

                    $country['login_number'] = $login_count;
                }
            });
        });

        return view('admin.eap-online.riports.create', ['from' => $from, 'to' => $to, 'companies' => $companies]);
    }

    public function activate_riports()
    {
        foreach (request()->input('companyIds') as $company_id) {
            $company = Company::query()->where('id', $company_id)->with('countries')->first();

            $riport = $company->eap_riports()->updateOrCreate([
                'from' => request()->input('from'),
                'to' => request()->input('to'),
            ], [
                'is_active' => 1,
            ]);

            if ($riport->wasRecentlyCreated) {
                $riport->update([
                    'is_active' => 1,
                ]);
                $this->generate_riport($company, $riport, request()->input('from'), request()->input('to'));
            }
        }

        return response()->json(['status' => 0]);
    }

    public function deactivate_riports()
    {
        foreach (request()->input('companyIds') as $company_id) {
            $company = Company::query()->where('id', $company_id)->with('countries')->first();

            $riport = $company->eap_riports()->updateOrCreate([
                'from' => request()->input('from'),
                'to' => request()->input('to'),
            ], [
                'is_active' => 0,
            ]);

            if ($riport->wasRecentlyCreated) {
                $this->generate_riport($company, $riport, request()->input('from'), request()->input('to'));
            }
        }

        return response()->json(['status' => 0]);
    }

    public function activate_riport()
    {
        EapRiport::query()->where('id', request()->input('riportId'))->update(['is_active' => 1]);

        return response()->json(['status' => 0]);
    }

    public function deactivate_riport()
    {
        EapRiport::query()->where('id', request()->input('riportId'))->update(['is_active' => 0]);

        return response()->json(['status' => 0]);
    }
}
