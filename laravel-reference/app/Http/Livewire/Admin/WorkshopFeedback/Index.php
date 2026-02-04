<?php

namespace App\Http\Livewire\Admin\WorkshopFeedback;

use App\Services\WorkshopFeedbackService;
use Carbon\Carbon;
use Livewire\Component;

class Index extends Component
{
    protected WorkshopFeedbackService $workshop_feedback_service;

    public string $search = '';

    public array $visible_months = [];

    public string $sort = 'desc';

    public function boot(WorkshopFeedbackService $workshop_feedback_service): void
    {
        $this->workshop_feedback_service = $workshop_feedback_service;
    }

    public function render()
    {
        $workshops = $this->workshop_feedback_service->get_workshops(search: $this->search);

        if ($this->search === '') {
            $workshops = $this->workshop_feedback_service->get_workshops(search: $this->search)
                ->mapToGroups(fn ($item, $index): array => [
                    Carbon::parse($item['case']->date)->format('Y-m') => [$index => $item],
                ])
                ->map(fn ($group) => $group->reduce(fn ($carry, $item): array => $carry + $item, []));

            $workshops = $workshops->sortKeys(descending: $this->sort === 'desc');
        } else {
            $workshops = $workshops->sortBy(fn ($item) => Carbon::parse($item['case']->date), descending: $this->sort === 'desc');
        }

        return view('livewire.admin.workshop-feedback.index', ['workshops' => $workshops])->extends('layout.master');
    }

    public function reset_search(): void
    {
        $this->search = '';
    }

    public function show_month($month): void
    {
        if (($key = array_search($month, $this->visible_months)) !== false) {
            unset($this->visible_months[$key]);
        } else {
            $this->visible_months[] = $month;
        }
    }
}
