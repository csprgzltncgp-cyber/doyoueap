<?php

namespace App\Http\Livewire\Admin\Todo;

use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Livewire\Component;

class IssuedTasks extends Component
{
    public $perPage = 10;

    public $orderBy;

    public function mount(): void
    {
        if (Cookie::get('orderBySelectDefault') !== null) {
            $this->orderBy = explode(',', Cookie::get('orderBySelectDefault'));
        } else {
            $this->orderBy = ['id', 'desc'];
        }
    }

    public function render()
    {
        $tasks = Task::query()
            ->where('from_id', Auth::id())
            ->join('users', 'users.id', '=', 'tasks.to_id')
            ->orderBy($this->orderBy[0], $this->orderBy[1])
            ->select('tasks.*')
            ->get();

        if ($this->orderBy[0] == 'status') {
            $tasks = $this->statusOrder($tasks);
        }

        $tasks = $tasks->paginate($this->perPage);

        return view('livewire.admin.todo.issued-tasks', ['tasks' => $tasks])->extends('layout.master');
    }

    public function updated($key, $value): void
    {
        Cookie::queue(Cookie::forever('orderBySelectDefault', $value));
        $this->orderBy = explode(',', (string) $value);
    }

    public function loadMore(): void
    {
        $this->perPage += 10;
    }

    public function loadAll(): void
    {
        $this->perPage = Task::query()
            ->where('from_id', Auth::id())
            ->orderByDesc('deadline')
            ->count();
    }

    public function statusOrder($tasks)
    {
        $taskWithCompletedNotification = $tasks->filter(fn ($task): bool => $task->status == Task::STATUS_COMPLETED && ! $task->confirmed);

        $inProgressTasks = $tasks->filter(fn ($task): bool => in_array($task->status, [Task::STATUS_OPENED, Task::STATUS_CREATED]))->sortBy('deadline');

        $completedTasks = $tasks->filter(fn ($task): bool => $task->status == Task::STATUS_COMPLETED && $task->confirmed)->sortByDesc(fn ($task) => $task->has_new_comments());

        return $taskWithCompletedNotification->merge($inProgressTasks)->merge($completedTasks);
    }
}
