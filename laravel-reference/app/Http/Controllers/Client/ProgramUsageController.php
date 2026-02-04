<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\CaseInputValue;
use App\Models\RiportValue;
use App\Traits\Riport as NormalRiportTrait;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ProgramUsageController extends Controller
{
    use NormalRiportTrait;

    public function show($country = null, $year = null)
    {
        $company = Auth::user()->companies()->with('countries')->first();
        $connected_companies = $company->get_connected_companies();

        $country = $country
            ? $company->countries->where('id', $country)->first()
            : $company->countries->sortBy('name')->first();

        $year ??= Carbon::now()->subYearNoOverflow()->year;

        $calculated_records = [];

        $org_data = $company->org_datas->where('country_id', $country->id)->first();

        $riport_data = [];

        foreach (range(1, 4) as $quarter) {
            $generated_data = Cache::remember(
                'riport-'.$quarter.'-'.$country->id.'-'.$company->id.'-'.$year,
                60 * 60 * 24 * 30,
                fn (): ?array => $this->get_riport_data($quarter, $country, $year)
            );

            if ($generated_data) {
                $riport_data = array_merge_recursive($riport_data, $generated_data['values']);
            }
        }

        if (! array_key_exists('problem_type', $riport_data)) {
            $calculated_records[$country->id] = null;

            return view('client.program-usage', ['calculated_records' => $calculated_records, 'country' => $country, 'company' => $company,  'connected_companies' => $connected_companies, 'year' => $year]);
        }

        $problem_type = [
            'count' => 0,
            'title' => '',
            'id' => null,
        ];

        foreach ($riport_data['problem_type'] as $key => $value) {
            if (collect($value['count'])->sum() > $problem_type['count']) {
                $problem_type['count'] = collect($value['count'])->sum();
                $problem_type['title'] = $key;
                $problem_type['id'] = collect($value['id'])->first();
            }
        }

        $calculated_records[$country->id]['problem_type'] = [
            'title' => $problem_type['title'],
            'icon' => $this->get_problem_type_icon($problem_type['id']),
        ];

        // Use updated calculation for age and gender data after 2024 or when company is Lidl Stiftung & Co. KG (1312)
        if ($year >= 2025 || $company->id === 1312) {

            // Get riport values of the year starting from february
            $riport_year_start_date = Carbon::createFromDate($year)->startOfYear()->addMonth()->startOfMonth();

            // Get riport values of the year up until the end of january next year.
            $riport_year_end_date = Carbon::createFromDate($year)->endOfYear()->addMonth()->endOfMonth();

            $gender = RiportValue::query()
                ->where('country_id', $country->id)
                ->whereHas('riport', function ($query) use ($company): void {
                    $query->where('company_id', $company->id);
                })
                ->whereBetween('created_at', [$riport_year_start_date, $riport_year_end_date])
                ->where('type', RiportValue::TYPE_GENDER)
                ->get()->groupBy('value')->sortByDesc(fn ($item): int => $item->count())->keys()->first();

            $age = RiportValue::query()
                ->where('country_id', $country->id)
                ->whereHas('riport', function ($query) use ($company): void {
                    $query->where('company_id', $company->id);
                })
                ->whereBetween('created_at', [$riport_year_start_date, $riport_year_end_date])
                ->where('type', RiportValue::TYPE_AGE)
                ->get()->groupBy('value')->sortByDesc(fn ($item): int => $item->count())->keys()->first();
        } else {
            $gender = RiportValue::query()
                ->where('country_id', $country->id)
                ->whereHas('riport', function ($query) use ($company): void {
                    $query->where('company_id', $company->id);
                })
                ->whereDate('created_at', '<', Carbon::createFromDate($year)->endOfYear())
                ->where('type', RiportValue::TYPE_GENDER)
                ->get()->groupBy('value')->sortByDesc(fn ($item): int => $item->count())->keys()->first();

            $calculated_records[$country->id]['gender'] = [
                'icon' => $this->get_gender_icon($gender),
                'title' => optional(optional(CaseInputValue::query()->where('id', $gender)->first())->translation)->value,
            ];

            $age = RiportValue::query()
                ->where('country_id', $country->id)
                ->whereHas('riport', function ($query) use ($company): void {
                    $query->where('company_id', $company->id);
                })
                ->whereDate('created_at', '<', Carbon::createFromDate($year)->endOfYear())
                ->where('type', RiportValue::TYPE_AGE)
                ->get()->groupBy('value')->sortByDesc(fn ($item): int => $item->count())->keys()->first();

        }

        $calculated_records[$country->id]['gender'] = [
            'icon' => $this->get_gender_icon($gender),
            'title' => optional(optional(CaseInputValue::query()->where('id', $gender)->first())->translation)->value,
        ];

        $calculated_records[$country->id]['age'] = optional(optional(CaseInputValue::query()->where('id', $age)->first())->translation)->value;

        try {
            $calculated_records[$country->id]['usage'] = Cache::remember(
                'program-usage-'.$company->id.'-'.optional($country)->id.'-'.$year,
                60 * 60 * 24 * 30,
                fn (): int|float => calculate_program_usage($company, $country, $org_data, null, $year)
            );

            $calculated_records[$country->id]['usage_global'] = round($calculated_records[$country->id]['usage'] / 1.5, 1);
            $calculated_records[$country->id]['best_usage_month'] = Carbon::parse($year.'-'.$this->get_most_used_month($company, $country, $org_data, $year).'-01')->translatedFormat('F');
            $calculated_records[$country->id]['show_badge'] = $calculated_records[$country->id]['usage'] > 1.5;
        } catch (Exception $e) {
            $calculated_records[$country->id]['usage'] = 0;
            $calculated_records[$country->id]['usage_global'] = 0;
            $calculated_records[$country->id]['best_usage_month'] = null;
            $calculated_records[$country->id]['show_badge'] = false;

            Log::error('Error while calculating program usage: '.$e->getMessage());
        }

        return view('client.program-usage', ['year' => $year, 'calculated_records' => $calculated_records, 'country' => $country, 'company' => $company, 'connected_companies' => $connected_companies]);
    }

    private function get_problem_type_icon($id)
    {
        return match ((int) $id) {
            1 => asset('assets/img/client/program-usage/pschiology.svg'),
            2 => asset('assets/img/client/program-usage/law.svg'),
            3 => asset('assets/img/client/program-usage/finance.svg'),
            7 => asset('assets/img/client/program-usage/health-coach.svg'),
            default => asset('assets/img/client/program-usage/pschiology.svg'),
        };
    }

    private function get_gender_icon($id)
    {
        if ((int) $id == 10) {
            return asset('assets/img/client/program-usage/women.svg');
        }

        return asset('assets/img/client/program-usage/men.svg');
    }

    private function get_most_used_month($company, $country, $org_data, $year = null): int|bool
    {
        $months = [];
        $year ??= Carbon::now()->subYearNoOverflow()->year;

        if (Carbon::parse($company->created_at)->isAfter(Carbon::parse('2022-02-01'))) {
            $from = Carbon::parse($org_data->contract_date)->month;
        } else {
            $from = 4;
        }

        for ($i = $from; $i <= 12; $i++) {
            $months[$i] = calculate_program_usage($company, $country, $org_data, $i, $year);
        }

        return array_search(max($months), $months);
    }
}
