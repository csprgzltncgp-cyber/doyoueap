<?php

namespace App\Http\Livewire\Admin\ActivityPlan;

use App\Models\ActivityPlan;
use App\Models\Company;
use App\Models\Country;
use App\Models\User;
use App\Services\CrisisService;
use App\Traits\ActivityPlan\UserCountriesTrait;
use Exception;
use Illuminate\Support\Collection;
use Livewire\Component;

class Crisis extends Component
{
    use UserCountriesTrait;

    public ActivityPlan $activity_plan;

    public Country $country;

    public Collection $crisis_interventions;

    private CrisisService $crisis_service;

    public bool $is_filtered = false;

    protected $listeners = [
        'crisis_intervention_filter' => 'filter',
        'country_changed',
    ];

    public function boot(
        CrisisService $crisis_service
    ): void {
        $this->crisis_service = $crisis_service;
    }

    public function mount(ActivityPlan $activity_plan): void
    {
        $this->activity_plan = $activity_plan;
        $this->country = $this->get_user_countries()->first();

        $this->crisis_interventions = $this->crisis_service->get_crisis_interventions(
            company: $this->activity_plan->company,
            filters: ['country_id' => $this->country->id]
        );
    }

    public function render()
    {
        $categories = $this->crisis_service->get_index_categories(
            company: $this->activity_plan->company,
            country: $this->country,
        );

        $experts = User::query()->whereIn('id', $this->crisis_interventions->pluck('expert_id')->unique())->orderBy('name')->get();
        $companies = Company::query()->orderBy('name')->get();

        return view('livewire.admin.activity-plan.crisis', [
            'categories' => $categories,
            'experts' => $experts,
            'companies' => $companies,
        ]);
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    public function filter(array $filters): void
    {
        $this->is_filtered = true;

        $this->crisis_interventions = $this->crisis_service->get_crisis_interventions(
            company: $this->activity_plan->company,
            filters: array_merge($filters, ['country_id' => $this->country->id]),
        );
    }

    public function clear_filter(): void
    {
        $this->is_filtered = false;

        $this->crisis_interventions = $this->crisis_service->get_crisis_interventions(
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

        $this->crisis_interventions = $this->crisis_service->get_crisis_interventions(
            company: $this->activity_plan->company,
            filters: ['country_id' => $this->country->id],
        );
    }
}
