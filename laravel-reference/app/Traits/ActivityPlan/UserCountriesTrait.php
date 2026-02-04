<?php

namespace App\Traits\ActivityPlan;

use App\Models\ActivityPlan;
use Illuminate\Support\Collection;

trait UserCountriesTrait
{
    public function get_user_countries(): Collection
    {
        if (ActivityPlan::query()->where('user_id', auth()->id())->where('country_id', null)->exists() || has_super_access_to_activity_plan()) {
            return $this->activity_plan->company->countries;
        }

        return ActivityPlan::query()
            ->where('user_id', auth()->id())
            ->whereNotNull('country_id')
            ->get()
            ->map(fn ($activity_plan) => $activity_plan->country)
            ->unique()
            ->sortBy('name');
    }
}
