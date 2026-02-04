<?php

namespace App\Http\Livewire\Admin\ActivityPlan;

use App\Models\ActivityPlan;
use App\Models\ActivityPlanCategory;
use Illuminate\Support\Collection;
use Livewire\Component;

class Show extends Component
{
    public ActivityPlan $activity_plan;

    public Collection $activity_plan_categories;

    public function mount(ActivityPlan $activity_plan): void
    {
        $this->activity_plan = $activity_plan;

        $this->activity_plan_categories = ActivityPlanCategory::query()
            ->where('company_id', $activity_plan->company_id)
            ->orWhereNull('company_id')
            ->get();
    }

    public function render()
    {
        return view('livewire.admin.activity-plan.show');
    }
}
