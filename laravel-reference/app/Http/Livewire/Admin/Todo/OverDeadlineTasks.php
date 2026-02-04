<?php

namespace App\Http\Livewire\Admin\Todo;

use App\Models\Task;
use Illuminate\Support\Facades\Cookie;
use Livewire\Component;

class OverDeadlineTasks extends Component
{
    public $perPage = 10;

    public $orderBy;

    public function mount(): void
    {
        if (Cookie::get('orderBySelectOverDeadlineDefault') !== null) {
            $this->orderBy = explode(',', Cookie::get('orderBySelectOverDeadlineDefault'));
        } else {
            $this->orderBy = ['deadline', 'asc'];
        }
    }

    public function render()
    {
        $tasks = Task::query()
            ->where('deadline', '<', today())
            ->where('status', '!=', Task::STATUS_COMPLETED)
            ->where(function ($query): void {
                $query->where('to_id', auth()->id())
                    ->orWhereHas('connected_users', function ($query): void {
                        $query->where('user_id', auth()->id());
                    });
            })
            ->join('users', 'users.id', '=', 'tasks.from_id')
            ->select('tasks.*')
            ->orderBy($this->orderBy[0], $this->orderBy[1])
            ->paginate($this->perPage);

        return view('livewire.admin.todo.over-deadline-tasks', ['tasks' => $tasks]);
    }

    public function updated($key, $value): void
    {
        Cookie::queue(Cookie::forever('orderBySelectOverDeadlineDefault', $value));
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
}
