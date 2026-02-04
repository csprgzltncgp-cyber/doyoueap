<?php

namespace App\Http\Livewire\Admin;

use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Symfony\Component\HttpFoundation\Exception\JsonException;

class Calendar extends Component
{
    use WithFileUploads;

    public $formated_tasks_data;

    public $admins;

    public $connectable_users;

    public $connected_users;

    public $task_title;

    public $listeners = [
        'task_created' => 'refresh_task',
    ];

    public function render()
    {
        return view('livewire.admin.calendar')->extends('layout.master');
    }

    public function mount(): void
    {
        $tasks = Task::query()
            ->where('to_id', Auth::id())
            ->orWhereHas('connected_users', function ($query): void {
                $query->where('user_id', Auth::id());
            })
            ->get();

        try {
            $this->formated_tasks_data = json_encode($this->format_task($tasks), JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            $this->formated_tasks_data = '';
        }
    }

    protected function format_task($tasks): array
    {
        $formatted = [];
        foreach ($tasks as $task) {
            $connected_users = ($task->connected_users->count() > 0) ? '('.__('task.colleague').': '.implode(',', $task->connected_users->pluck('name')->toArray()).')' : '';
            $event = [
                'title' => $task->title.' ('.optional($task->to)->name.') '.$connected_users,
                'start' => Carbon::parse($task->created_at)->format('Y-m-d'),
                'end' => Carbon::parse($task->deadline)->addDay()->format('Y-m-d'),
                'id' => $task->id,
                'url' => route(auth()->user()->type.'.todo.show', ['task' => $task->id]),
            ];

            $this->event_color($event, $task);

            $formatted[] = $event;
        }

        return $formatted;
    }

    protected function event_color(array &$event, Task $task): void
    {
        // Set event color for tasks that were not confirmed and the deadline has passed.
        if (Carbon::parse($task->deadline)->lt(Carbon::now()) && $task->status != Task::STATUS_COMPLETED) {
            $event['color'] = 'rgb(219, 11, 32)';
        }

        // Set event color for tasks when the dead line is the current day (today)
        if (Carbon::parse(Carbon::parse($task->deadline)->format('Y-m-d'))->eq(Carbon::now()->format('Y-m-d'))) {
            $event['color'] = 'rgb(235, 126, 48)';
        }
    }

    public function refresh_task(): void
    {
        $task = Task::query()
            ->where('to_id', Auth::id())
            ->orWhereHas('connected_users', function ($query): void {
                $query->where('user_id', Auth::id());
            })
            ->orderByDesc('id')
            ->first();

        $connected_users = ($task->connected_users->count() > 0) ? '('.__('task.colleague').': '.implode(',', $task->connected_users->pluck('name')->toArray()).')' : '';

        $event = [
            'title' => $task->title.' ('.$task->to->name.') '.$connected_users,
            'start' => Carbon::parse($task->created_at)->format('Y-m-d'),
            'end' => Carbon::parse($task->deadline)->addDay()->format('Y-m-d'),
            'id' => $task->id,
            'url' => route(auth()->user()->type.'.todo.show', ['task' => $task->id]),
        ];

        $this->event_color($event, $task);

        $this->emit('task_refresh', $event);
    }
}
