<?php

namespace App\Http\Livewire\Admin\Todo;

use App\Mail\TaskCommentCreated;
use App\Models\Task;
use App\Models\TaskCompletionPoint;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class TaskEdit extends Component
{
    use WithFileUploads;

    public Task $task;

    public $newComment;

    public $newConnectedUser;

    public $newAttachments = [];

    protected $rules = [
        'task.title' => 'string|max:255',
        'task.description' => 'string',
        'task.deadline' => 'date',
        'task.to_id' => 'exists:users,id',
        'newComment' => 'required',
        'newAttachments.*' => 'max:2048|mimes:jpeg,png,jpg,gif,svg,pdf,doc,docx,xls,xlsx,ppt,pptx,zip,rar',
        'newConnectedUser' => 'exists:users,id',
    ];

    public function mount(Task $task): void
    {
        if (! (auth()->user()->type == 'admin' || auth()->user()->id == 506 || auth()->user()->id == $task->from_id)) {
            abort(403);
        }

        session()->put('todo-prev-url', url()->previous());

        $this->task = $task;
        $this->task->load(['comments', 'comments.user', 'from', 'to', 'attachments']);

        if ($this->task->status != Task::STATUS_COMPLETED && $this->task->to_id == Auth::id() && $this->task->from_id == Auth::id()) {
            $this->task->status = Task::STATUS_OPENED;
            $this->task->save();
        }

        $this->task->comments->where('user_id', '!=', auth()->id())->map(function ($comment): void {
            $comment->seen = true;
            $comment->save();
        });
    }

    public function render()
    {
        $admins = User::query()
            ->where('type', 'like', '%admin%')
            ->where('type', '!=', 'production_translating_admin')
            ->where('connected_account', null)
            ->orderBy('name')
            ->get();

        $connected_users = $this->task->connected_users()->get();
        $connectable_users = User::query()
            ->where('type', 'like', '%admin%')
            ->where('type', '!=', 'production_translating_admin')
            ->where('connected_account', null)
            ->whereNotIn('id', array_merge([$this->task->from_id, $this->task->to_id], $connected_users->pluck('id')->toArray()))
            ->get();

        return view('livewire.admin.todo.task-edit', ['admins' => $admins, 'connected_users' => $connected_users, 'connectable_users' => $connectable_users])->extends('layout.master');
    }

    public function updated($field, $value): void
    {
        if ($field == 'newComment') {
            return;
        }

        $this->validateOnly($field, $this->rules);

        if ($field == 'newAttachments') {
            foreach ($this->newAttachments as $attachment) {
                $this->task->attachments()->create([
                    'filename' => $attachment->getClientOriginalName(),
                    'path' => $attachment->store('task-attachments/'.$this->task->id, 'private'),
                ]);
            }

            $this->task->load('attachments');

            return;
        }

        if (! array_key_exists(Str::afterLast($field, '.'), $this->task->getAttributes())) {
            return;
        }

        $this->task->{Str::afterLast($field, '.')} = $value;
        $this->task->save();
    }

    public function confirm(): void
    {
        $this->task->update([
            'confirmed' => true,
        ]);

        $this->emit('alert', ['message' => __('task.confirm_success')]);
    }

    public function reopen(): void
    {
        $this->task->update([
            'status' => Task::STATUS_OPENED,
            'confirmed' => false,
        ]);

        if (TaskCompletionPoint::query()->where('task_id', $this->task->id)->where('user_id', $this->task->to_id)->exists()) {
            TaskCompletionPoint::query()->where('task_id', $this->task->id)->where('user_id', $this->task->to_id)->delete();
        }

        $this->emit('alert', ['message' => __('task.repoen_sussess')]);
    }

    public function saveComment(): void
    {
        $this->validateOnly('newComment', $this->rules);

        $this->task->comments()->create([
            'value' => $this->newComment,
            'user_id' => auth()->id(),
        ]);

        if (config('app.env') === 'production' || config('app.env') === 'local') {
            Mail::to($this->task->to->email)->send(new TaskCommentCreated($this->task, auth()->user(), $this->task->to));
        }

        $this->newComment = null;
        $this->emit('commentSaved');
        $this->task->load(['comments', 'comments.user']);
    }

    public function deleteAttachment($id): void
    {
        $attachment = $this->task->attachments()->find($id);
        $attachment->delete();
        $this->task->load('attachments');
    }

    public function connectUser(): void
    {
        if (! $this->task->connected_users()->where('user_id', $this->newConnectedUser)->exists()) {
            $this->task->connected_users()->attach($this->newConnectedUser);
        }

        $this->newConnectedUser = null;
        $this->emit('userConnected');
    }

    public function detachConnectedUser($user_id): void
    {
        $this->task->connected_users()->detach($user_id);
    }

    public function backToList()
    {
        if (session()->get('todo-prev-url') == route(auth()->user()->type.'.todo.edit', ['task' => $this->task])) {
            return redirect()->route(auth()->user()->type.'.dashboard');
        }

        return redirect(session()->get('todo-prev-url'));
    }

    public function save(): void
    {
        $this->emit('alert', ['message' => __('common.edit-successful')]);
    }
}
