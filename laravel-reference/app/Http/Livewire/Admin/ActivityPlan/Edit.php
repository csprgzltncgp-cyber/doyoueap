<?php

namespace App\Http\Livewire\Admin\ActivityPlan;

use App\Models\ActivityPlan;
use App\Models\ActivityPlanCategory;
use Illuminate\Support\Collection;
use Livewire\Component;

class Edit extends Component
{
    public ActivityPlan $activity_plan;

    /** @var Collection<ActivityPlanCategory> */
    public Collection $activity_plan_categories;

    protected $listeners = ['create_category', 'delete_category'];

    public function mount(ActivityPlan $activity_plan): void
    {
        $this->activity_plan = $activity_plan;
        $this->activity_plan_categories = ActivityPlanCategory::query()
            ->orderBy('name')
            ->where('company_id', $activity_plan->company_id)
            ->orWhereNull('company_id')
            ->get();
    }

    public function render()
    {
        return view('livewire.admin.activity-plan.edit');
    }

    public function create_category(string $name, bool $all_companies): void
    {
        $activity_plan_category = ActivityPlanCategory::query()->create([
            'company_id' => $all_companies ? null : $this->activity_plan->company_id,
            'name' => $name,
        ]);

        $this->activity_plan_categories->push($activity_plan_category);
    }

    public function delete_category(ActivityPlanCategory $activity_plan_category): void
    {
        $activity_plan_category->delete();

        $this->activity_plan_categories = $this->activity_plan_categories->reject(fn (ActivityPlanCategory $item): bool => $item->id == $activity_plan_category->id)->sortBy('name');
    }
}
