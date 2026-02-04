<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityPlan;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ActivityPlanController extends Controller
{
    public function index(?ActivityPlan $activity_plan = null): View
    {
        $activity_plans = ActivityPlan::query()
            ->when(! has_super_access_to_activity_plan(), fn ($query) => $query->where('user_id', auth()->id()))
            ->whereNotNull('user_id')
            ->get();

        $activity_plan ??= $activity_plans->first();

        if (count($activity_plans) == 0) {
            abort(403);
        }

        return view('admin.activity-plan.index', ['activity_plans' => $activity_plans, 'activity_plan' => $activity_plan]);
    }

    public function edit(ActivityPlan $activity_plan): View
    {
        return view('admin.activity-plan.edit', ['activity_plan' => $activity_plan]);
    }

    public function toggle_activity_plan_member(Request $request): void
    {
        $request->validate([
            'model_id' => 'required|integer',
            'model_class' => 'required|string',
            'activity_plan_id' => 'required|integer|exists:activity_plans,id',
        ]);

        $activity_plan = ActivityPlan::query()->find($request->activity_plan_id);

        $model = $request->model_class::find($request->model_id);

        if (! $model) {
            throw new Exception('Model not found');
        }

        if ($model->activity_plan_members()
            ->where('activity_plan_id', $activity_plan->id)
            ->where('activity_plan_memberable_type', $model::class)
            ->exists()
        ) {
            $model->activity_plan_members()
                ->where('activity_plan_id', $activity_plan->id)
                ->where('activity_plan_memberable_type', $model::class)
                ->delete();
        } else {
            $model->activity_plan_members()->create([
                'activity_plan_id' => $activity_plan->id,
            ]);
        }
    }
}
