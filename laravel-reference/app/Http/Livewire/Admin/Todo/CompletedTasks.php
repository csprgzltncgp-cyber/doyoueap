<?php

namespace App\Http\Livewire\Admin\Todo;

use App\Models\Task;
use Illuminate\Support\Facades\Cookie;
use Livewire\Component;

class CompletedTasks extends Component
{
    public $perPage = 10;

    public $orderBy;

    public function mount(): void
    {
        if (Cookie::get('orderBySelectCompletedDefault') !== null) {
            $this->orderBy = explode(',', Cookie::get('orderBySelectCompletedDefault'));
        } else {
            $this->orderBy = ['deadline', 'asc'];
        }
    }

    public function render()
    {
        $tasks = Task::query()
            ->where('status', Task::STATUS_COMPLETED)
            ->where(function ($query): void {
                $query->where('to_id', auth()->id())
                    ->orWhereHas('connected_users', function ($query): void {
                        $query->where('user_id', auth()->id());
                    });
            })
            ->join('users', 'users.id', '=', 'tasks.from_id')
            ->orderBy($this->orderBy[0], $this->orderBy[1])
            ->select('tasks.*')
            ->get();

        if ($this->orderBy[0] == 'status') {
            $tasks = $this->statusOrder($tasks);
        }

        $tasks = $tasks->paginate($this->perPage);

        return view('livewire.admin.todo.completed-tasks', ['tasks' => $tasks]);
    }

    public function updated($key, $value): void
    {
        Cookie::queue(Cookie::forever('orderBySelectCompletedDefault', $value));
        $this->orderBy = explode(',', (string) $value);
    }

    public function loadMore(): void
    {
        $this->perPage += 10;
    }

    public function loadAll(): void
    {
        $this->perPage = Task::query()->count();
    }

    public function statusOrder($tasks)
    {
        return $tasks->sortByDesc(fn ($task) => $task->has_new_comments());
    }
}
