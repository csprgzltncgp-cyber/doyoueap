<?php

namespace App\Http\Livewire\Admin\ActivityPlan;

use App\Models\ActivityPlan;
use App\Services\WorkshopFeedbackService;
use Livewire\Component;

class WorkshopFeedback extends Component
{
    public ActivityPlan $activity_plan;

    private WorkshopFeedbackService $workshop_feedback_service;

    public function boot(
        WorkshopFeedbackService $workshop_feedback_service
    ): void {
        $this->workshop_feedback_service = $workshop_feedback_service;
    }

    public function render()
    {
        $workshops = $this->workshop_feedback_service->get_workshops($this->activity_plan->company);

        return view('livewire.admin.activity-plan.workshop-feedback', ['workshops' => $workshops]);
    }
}
