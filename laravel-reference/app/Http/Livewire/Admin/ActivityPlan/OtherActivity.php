<?php

namespace App\Http\Livewire\Admin\ActivityPlan;

use App\Models\ActivityPlan;
use App\Models\Company;
use App\Models\Country;
use App\Models\User;
use App\Services\OtherActivityService;
use App\Traits\ActivityPlan\UserCountriesTrait;
use Exception;
use Illuminate\Support\Collection;
use Livewire\Component;

class OtherActivity extends Component
{
    use UserCountriesTrait;

    public ActivityPlan $activity_plan;

    public Country $country;

    public Collection $other_activities;

    private OtherActivityService $other_activity_service;

    public bool $is_filtered = false;

    protected $listeners = [
        'other_activity_filter' => 'filter',
        'country_changed',
    ];

    public function boot(
        OtherActivityService $other_activity_service
    ): void {
        $this->other_activity_service = $other_activity_service;
    }

    public function mount(ActivityPlan $activity_plan): void
    {
        $this->activity_plan = $activity_plan;
        $this->country = $this->get_user_countries()->first();

        $this->other_activities = $this->other_activity_service->get_other_activities(
            company: $this->activity_plan->company,
            filters: ['country_id' => $this->country->id]
        );
    }

    public function render()
    {
        $other_activities = $this->other_activity_service->get_other_activities(
            company: $this->activity_plan->company,
            filters: ['country_id' => $this->country->id]
        );
        $categories = $this->other_activity_service->get_index_categories($other_activities);

        $experts = User::query()->whereIn('id', $other_activities->pluck('user_id')->unique())->orderBy('name')->get();
        $companies = Company::query()->orderBy('name')->get();

        return view('livewire.admin.activity-plan.other-activity',
            array_merge($categories, ['experts' => $experts, 'companies' => $companies])
        );
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    public function filter(array $filters): void
    {
        $this->is_filtered = true;

        $this->other_activities = $this->other_activity_service->get_other_activities(
            company: $this->activity_plan->company,
            filters: array_merge($filters, ['country_id' => $this->country->id]),
        );
    }

    public function clear_filter(): void
    {
        $this->is_filtered = false;

        $this->other_activities = $this->other_activity_service->get_other_activities(
            company: $this->activity_plan->company,
            filters: ['country_id' => $this->country->id],
        );
    }

    public function country_changed(int $country_id): void
    {
        try {
            $this->country = Country::query()->findOrFail($country_id);
        } catch (Exception) {
            $this->country = $this->activity_plan->company->countries->first();
        }

        $this->other_activities = $this->other_activity_service->get_other_activities(
            company: $this->activity_plan->company,
            filters: ['country_id' => $this->country->id],
        );
    }
}
