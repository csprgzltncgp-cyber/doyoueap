<?php

namespace App\Http\Livewire\Admin\ActivityPlanCategoryCase;

use App\Enums\ActivityPlanCategoryCaseStatusEnum;
use App\Models\ActivityPlan;
use App\Models\ActivityPlanCategoryCase;
use Livewire\Component;

class Show extends Component
{
    public ActivityPlanCategoryCase $activity_plan_category_case;

    protected $listeners = ['deleteActivityPlanCategoryCase' => 'delete'];

    public function render()
    {
        return view('livewire.admin.activity-plan-category-case.show');
    }

    public function close()
    {
        $this->activity_plan_category_case->update([
            'status' => ActivityPlanCategoryCaseStatusEnum::CLOSED,
            'closed_at' => now(),
        ]);

        $activity_plan = ActivityPlan::query()
            ->when(! has_super_access_to_activity_plan(), fn ($query) => $query->where('user_id', auth()->id()))
            ->whereNotNull('user_id')
            ->where('company_id', $this->activity_plan_category_case->activity_plan_category->company_id)
            ->first();

        return redirect()->route(auth()->user()->type.'.activity-plan.index', ['activity_plan' => $activity_plan]);
    }

    public function delete()
    {
        $this->activity_plan_category_case->delete();

        $activity_plan = ActivityPlan::query()
            ->when(! has_super_access_to_activity_plan(), fn ($query) => $query->where('user_id', auth()->id()))
            ->whereNotNull('user_id')
            ->where('company_id', $this->activity_plan_category_case->activity_plan_category->company_id)
            ->first();

        return redirect()->route(auth()->user()->type.'.activity-plan.index', ['activity_plan' => $activity_plan]);
    }
}
