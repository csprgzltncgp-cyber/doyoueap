<?php

namespace App\Http\Livewire\Admin\Todo;

use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Cookie;
use Livewire\Component;

class UserTasksLine extends Component
{
    public User $user;

    public $perPage = 10;

    public $isOpened = false;

    public $orderBy;

    public function mount(User $user): void
    {
        $this->user = $user;

        if (Cookie::get('orderByUserLine'.$this->user->id) !== null) {
            $this->orderBy = explode(',', Cookie::get('orderByUserLine'.$this->user->id));
        } else {
            $this->orderBy = ['deadline', 'asc'];
        }
    }

    public function render()
    {
        $tasks = Task::query()
            ->where('to_id', $this->user->id)
            ->join('users', 'users.id', '=', 'tasks.from_id')
            ->orderBy($this->orderBy[0], $this->orderBy[1])
            ->select('tasks.*')
            ->get();

        if ($this->orderBy[0] == 'status') {
            $tasks = $this->statusOrder($tasks);
        }

        $tasks = $tasks->paginate($this->perPage);

        $has_over_deadline_tasks = Task::query()
            ->where('to_id', $this->user->id)
            ->get()
            ->map(fn ($task) => $task->is_over_deadline())->sum();

        return view('livewire.admin.todo.user-tasks-line', ['tasks' => $tasks, 'has_over_deadline_tasks' => $has_over_deadline_tasks]);
    }

    public function updated($key, $value): void
    {
        Cookie::queue(Cookie::forever('orderByUserLine'.$this->user->id, $value));
        $this->orderBy = explode(',', (string) $value);
    }

    public function toggleOpen(): void
    {
        $this->isOpened = ! $this->isOpened;
    }

    public function loadMore(): void
    {
        $this->perPage += 10;
    }

    public function loadAll(): void
    {
        $this->perPage = Task::query()->where('to_id', $this->user->id)->count();
    }

    public function statusOrder($tasks)
    {
        $taskWithCompletedNotification = $tasks->filter(fn ($task): bool => $task->status == Task::STATUS_COMPLETED && ! $task->confirmed);

        $inProgressTasks = $tasks->filter(fn ($task): bool => in_array($task->status, [Task::STATUS_OPENED, Task::STATUS_CREATED]))->sortBy('deadline');

        $completedTasks = $tasks->filter(fn ($task): bool => $task->status == Task::STATUS_COMPLETED && $task->confirmed)->sortByDesc(fn ($task) => $task->has_new_comments());

        return $taskWithCompletedNotification->merge($inProgressTasks)->merge($completedTasks);
    }
}
