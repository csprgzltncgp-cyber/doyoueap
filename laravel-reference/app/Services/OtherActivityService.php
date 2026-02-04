<?php

namespace App\Services;

use App\Models\Company;
use App\Models\OtherActivity;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class OtherActivityService
{
    /**
     * @param  Collection<OtherActivity>  $other_activities
     */
    public function get_index_categories(Collection $other_activities): array
    {

        $saved_activities = $other_activities
            ->filter(fn (OtherActivity $otherActivity): bool => ! $otherActivity->is_outsourced() || empty($otherActivity->date));

        $months = $other_activities
            ->whereNotNull('date')
            ->map(fn (OtherActivity $otherActivity): string => Carbon::parse($otherActivity->date)->year.'-'.Carbon::parse($otherActivity->date)
                ->format('m'))
            ->sort()
            ->unique()
            ->reverse();

        $years = $other_activities
            ->whereNotNull('date')
            ->map(fn (OtherActivity $otherActivity) => Carbon::parse($otherActivity->date)->year)
            ->sort()
            ->unique()
            ->reverse();

        return [
            'years' => $years,
            'months' => $months,
            'saved_activities' => $saved_activities,
        ];
    }

    public function get_other_activities(?Company $company = null, ?array $filters = null): Collection
    {
        $other_activities = OtherActivity::query()
            ->when($company, fn (Builder $query) => $query->where('company_id', $company->id))
            ->latest()
            ->orderByDesc('date')
            ->get();

        if ($filters) {
            foreach ($filters as $filter => $value) {
                if (empty($value)) {
                    continue;
                }

                $other_activities = $other_activities->filter(fn (OtherActivity $other_activity_case): bool => $other_activity_case->{$filter} == $value);
            }
        }

        return $other_activities;
    }
}
