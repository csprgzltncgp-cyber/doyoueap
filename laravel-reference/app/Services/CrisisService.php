<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Country;
use App\Models\CrisisCase;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class CrisisService
{
    public function get_index_categories(?Company $company = null, ?Country $country = null): array
    {
        /** @var Collection<CrisisCase> $crisis_cases */
        $crisis_cases = CrisisCase::query()
            ->when($company, fn (Builder $query) => $query->where('company_id', $company->id))
            ->when($country, fn (Builder $query) => $query->where('country_id', $country->id))
            ->latest()
            ->get();

        $saved_crisis_cases = $crisis_cases->filter(fn ($crisis_case): bool => ! $crisis_case->is_outsourced());

        $all_monts = [];
        $all_years = [];

        foreach ($crisis_cases->where('date', '!=', null) as $crisis_case) {
            $month = substr((string) $crisis_case->date, 0, -3);
            $all_monts[] = $month;

            $years = substr((string) $crisis_case->date, 0, -6);
            $all_years[] = $years;
        }

        $filtered_months = array_unique($all_monts);
        rsort($filtered_months);

        $filtered_years = array_unique($all_years);
        rsort($filtered_years);

        return [
            'saved_crisis_cases' => $saved_crisis_cases,
            'filtered_years' => $filtered_years,
            'filtered_months' => $filtered_months,
        ];
    }

    public function get_crisis_interventions(Company $company, ?array $filters = null)
    {
        $crisis_interventions = CrisisCase::query()
            ->where('company_id', $company->id)
            ->latest()
            ->get();

        if ($filters) {
            foreach ($filters as $filter => $value) {
                if (empty($value)) {
                    continue;
                }

                $crisis_interventions = $crisis_interventions->filter(fn (CrisisCase $crisis_intervention_case): bool => $crisis_intervention_case->{$filter} == $value);
            }
        }

        return $crisis_interventions;
    }
}
