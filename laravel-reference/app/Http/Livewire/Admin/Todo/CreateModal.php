<?php

namespace App\Http\Livewire\Admin\Todo;

use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\WithFileUploads;
use LivewireUI\Modal\ModalComponent;

class CreateModal extends ModalComponent
{
    use WithFileUploads;

    protected static array $maxWidths = [
        'sm' => 'sm:max-w-sm',
        'md' => 'sm:max-w-md',
        'lg' => 'sm:max-w-lg',
        'xl' => 'sm:max-w-xl',
        '2xl' => 'sm:max-w-2xl',
        '3xl' => 'sm:max-w-3xl',
        '4xl' => 'sm:max-w-4xl',
        '5xl' => 'sm:max-w-5xl',
        '6xl' => 'sm:max-w-6xl',
        '7xl' => 'sm:max-w-7xl',
        'full' => 'sm:max-w-full',
    ];

    public $admins;

    public $connectable_users;

    public $show_connected_user_select;

    public $connected_users = [];

    public $new_connected_user;

    public $attachments;

    public $task;

    public $start_date;

    protected $rules = [
        'task.title' => 'required',
        'task.to_id' => 'exists:users,id|required',
        'task.from_id' => 'required',
        'task.deadline' => 'required|date',
        'task.description' => 'required',
        'task.status' => 'required',
    ];

    public static function modalMaxWidth(): string
    {
        return '2xl';
    }

    public function render()
    {
        return view('livewire.admin.todo.create-modal');
    }

    public function mount(): void
    {
        $this->admins = User::query()
            ->when(Auth::id() == 506, fn ($query) => $query->whereIn('id', [706, 1087, 1088, 1113, 167, 1093])) // Show specified users to select for tasks for Peter Janky
            ->where('type', 'like', '%admin%')
            ->has('assigned_task')
            ->orderBy('name')
            ->get();

        $this->connectable_users = User::query()
            ->where('type', 'like', '%admin%')
            ->whereNot('type', 'production_translating_admin')
            ->whereNull('connected_account')
            ->whereNot('id', Auth::id())
            ->get();

        $this->show_connected_user_select = false;
        $this->task = new Task;
    }

    public function show_user_select(): void
    {
        $this->show_connected_user_select = ! $this->show_connected_user_select;
    }

    public function add_connected_user(): void
    {
        $connected_user = $this->connectable_users->where('id', $this->new_connected_user)->first();

        if ($connected_user) {
            $this->connected_users[] = $this->connectable_users->where('id', $this->new_connected_user)->first()->toArray();
            $this->show_user_select();
        }
    }

    public function remove_connected_user($key): void
    {
        unset($this->connected_users[$key]);
    }

    public function create_task(): void
    {
        $this->task->from_id = Auth::id();
        $this->task->status = Task::STATUS_CREATED;
        $this->task->created_at = $this->start_date;

        $this->validate();

        $this->task->save();

        if (! empty($this->connected_users)) {
            $this->task->connected_users()->attach(collect($this->connected_users)->map(fn ($item): mixed => $item['id'])->toArray());
        }

        if (! empty($this->attachments)) {
            foreach ($this->attachments as $attachment) {
                $this->task->attachments()->create([
                    'filename' => $attachment->getClientOriginalName(),
                    'path' => $attachment->store('task-attachments/'.$this->task->id, 'private'),
                ]);
            }
        }

        $this->emit('task_created');
        $this->emit('closeModal');
    }
}
