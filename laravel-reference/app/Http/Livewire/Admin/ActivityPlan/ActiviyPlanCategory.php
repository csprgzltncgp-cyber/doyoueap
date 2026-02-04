<?php

namespace App\Http\Livewire\Admin\ActivityPlan;

use App\Models\ActivityPlan;
use App\Models\ActivityPlanCategory;
use App\Models\ActivityPlanCategoryCase;
use App\Models\Company;
use App\Models\Country;
use App\Traits\ActivityPlan\UserCountriesTrait;
use Exception;
use Illuminate\Support\Collection;
use Livewire\Component;

class ActiviyPlanCategory extends Component
{
    use UserCountriesTrait;

    public ActivityPlan $activity_plan;

    public Country $country;

    public Company $company;

    public ActivityPlanCategory $activity_plan_category;

    public Collection $cases;

    protected $listeners = [
        'country_changed',
    ];

    public function mount(ActivityPlan $activity_plan): void
    {
        $this->activity_plan = $activity_plan;
        $this->company = $activity_plan->company;
        $this->country = $this->get_user_countries()->first();
        $this->cases = $this->get_cases();
    }

    public function render()
    {

        return view('livewire.admin.activity-plan.activiy-plan-category');
    }

    public function country_changed(int $country_id): void
    {
        try {
            $this->country = Country::query()->findOrFail($country_id);
        } catch (Exception) {
            $this->country = $this->activity_plan->company->countries->first();
        }

        $this->cases = $this->get_cases();
    }

    private function get_cases(): Collection
    {
        return ActivityPlanCategoryCase::query()
            ->latest()
            ->where('activity_plan_category_id', $this->activity_plan_category->id)
            ->where('country_id', $this->country->id)
            ->where('company_id', $this->company->id)
            ->get();
    }
}
