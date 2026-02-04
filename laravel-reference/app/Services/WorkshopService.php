<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Country;
use App\Models\WorkshopCase;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class WorkshopService
{
    public function get_index_categories(?Company $company = null, ?Country $country = null): array
    {
        /** @var Collection<WorkshopCase> $workshop_cases */
        $workshop_cases = WorkshopCase::query()
            ->when($company, fn (Builder $query) => $query->where('company_id', $company->id))
            ->when($country, fn (Builder $query) => $query->where('country_id', $country->id))
            ->latest()
            ->get();

        $saved_workshop_cases = $workshop_cases->filter(fn (WorkshopCase $workshop_case): bool => ! $workshop_case->is_outsourced());

        $all_monts = [];
        $all_years = [];

        foreach ($workshop_cases->where('date', '!=', null) as $workshop_case) {
            $month = substr((string) $workshop_case->date, 0, -3);
            $all_monts[] = $month;

            $years = substr((string) $workshop_case->date, 0, -6);
            $all_years[] = $years;
        }

        $filtered_months = array_unique($all_monts);
        rsort($filtered_months);

        $filtered_years = array_unique($all_years);
        rsort($filtered_years);

        return [
            'saved_workshop_cases' => $saved_workshop_cases,
            'filtered_years' => $filtered_years,
            'filtered_months' => $filtered_months,
        ];
    }

    public function get_workshos(Company $company, ?array $filters = null)
    {
        $workshops = WorkshopCase::query()
            ->where('company_id', $company->id)
            ->latest()
            ->get();

        if ($filters) {
            foreach ($filters as $filter => $value) {
                if (empty($value)) {
                    continue;
                }

                $workshops = $workshops->filter(fn (WorkshopCase $workshop_case): bool => $workshop_case->{$filter} == $value);
            }
        }

        return $workshops;
    }
}
