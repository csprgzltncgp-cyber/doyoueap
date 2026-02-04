<?php

namespace App\Http\Livewire\Admin\ActivityPlan;

use App\Enums\ActivityPlanCategoryFieldTypeEnum;
use App\Models\ActivityPlan;
use App\Models\ActivityPlanCategoryCase;
use App\Models\ActivityPlanMember;
use App\Models\CrisisCase;
use App\Models\OtherActivity;
use App\Models\WorkshopCase;
use App\Traits\ActivityPlan\UserCountriesTrait;
use Illuminate\Support\Collection;
use Livewire\Component;

class Map extends Component
{
    use UserCountriesTrait;

    // Current activity plan
    public ActivityPlan $activity_plan;

    public int $current_company_id;

    public int $current_country_id;

    public Collection $countries;

    public Collection $companies;

    public Collection $activity_plan_members;

    protected $listeners = ['refresh_activity_plan'];

    public function rules(): array
    {
        return [
            'current_company' => 'required',
            'current_country' => 'required',
        ];
    }

    public function mount(ActivityPlan $activity_plan): void
    {
        $this->activity_plan = $activity_plan;

        $this->countries = $this->get_user_countries();
        $this->companies = ActivityPlan::query()
            ->when(! has_super_access_to_activity_plan(), fn ($query) => $query->where('user_id', auth()->id()))
            ->whereNotNull('user_id')
            ->get()
            ->map(fn ($activity_plan) => $activity_plan->company)
            ->unique()
            ->sortBy('name');

        $this->current_company_id = $activity_plan->company->id;
        $this->current_country_id = $this->countries->first()->id;

        $this->activity_plan_members = $this->get_activity_plan_members();
    }

    public function render()
    {
        return view('livewire.admin.activity-plan.map');
    }

    public function updatedCurrentCompanyId()
    {
        $activity_plan = ActivityPlan::query()
            ->when(! has_super_access_to_activity_plan(), fn ($query) => $query->where('user_id', auth()->id()))
            ->whereNotNull('user_id')
            ->where('company_id', $this->current_company_id)
            ->first();

        return redirect()->route(auth()->user()->type.'.activity-plan.index', ['activity_plan' => $activity_plan]);
    }

    public function updatedCurrentCountryId(): void
    {
        $this->activity_plan_members = $this->get_activity_plan_members();

        $this->emit('country_changed', $this->current_country_id);
    }

    public function refresh_activity_plan(): void
    {
        $this->activity_plan_members = $this->get_activity_plan_members();
    }

    private function get_activity_plan_members()
    {
        return ActivityPlanMember::query()
            ->with('activity_plan_memberable')
            ->where('activity_plan_id', $this->activity_plan->id)
            ->get()
            ->filter(fn ($activity_plan_member): bool => optional($activity_plan_member->activity_plan_memberable)->country_id == $this->current_country_id)
            ->sortBy(fn ($activity_plan_member) => match ($activity_plan_member->activity_plan_memberable::class) {
                WorkshopCase::class => $activity_plan_member->activity_plan_memberable->date,
                CrisisCase::class => $activity_plan_member->activity_plan_memberable->date,
                OtherActivity::class => $activity_plan_member->activity_plan_memberable->date,
                ActivityPlanCategoryCase::class => optional($activity_plan_member->activity_plan_memberable->activity_plan_category_case_values()
                    ->whereHas('activity_plan_category_field', fn ($query) => $query->where('type', ActivityPlanCategoryFieldTypeEnum::EVENT_DATE))
                    ->first())->value,
                default => null,
            });
    }
}
