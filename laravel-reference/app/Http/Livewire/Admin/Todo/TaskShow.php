<?php

namespace App\Http\Livewire\Admin\Todo;

use App\Mail\TaskCommentCreated;
use App\Mail\TaskCompleted;
use App\Models\Task;
use App\Models\TaskCompletionPoint;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class TaskShow extends Component
{
    public $task;

    public $newComment;

    protected $rules = [
        'newComment' => 'required',
    ];

    protected $listeners = [
        'completeTask' => 'completeTask',
    ];

    public function mount(Task $task): void
    {
        session()->put('todo-prev-url', url()->previous());

        $this->task = $task;

        $task->load(['comments', 'comments.user', 'from', 'to', 'attachments']);

        if ($this->task->status != Task::STATUS_COMPLETED) {
            $this->task->update([
                'status' => Task::STATUS_OPENED,
            ]);
        }

        $this->task->comments->where('user_id', '!=', auth()->id())->map(function ($comment): void {
            $comment->seen = true;
            $comment->save();
        });
    }

    public function render()
    {
        return view('livewire.admin.todo.task-show')->extends('layout.master');
    }

    public function forwardTask()
    {
        if (! in_array(auth()->id(), [$this->task->from_id, $this->task->to_id])) {
            return null;
        }

        session()->flash('forwarded-task-title', $this->task->title);
        session()->flash('forwarded-task-description', $this->task->description);

        return redirect()->route(auth()->user()->type.'.todo.create');
    }

    public function saveComment(): void
    {
        $this->validate();

        $this->task->comments()->create([
            'value' => $this->newComment,
            'user_id' => auth()->id(),
        ]);

        if (config('app.env') === 'production' || config('app.env') === 'local') {
            Mail::to($this->task->from->email)->send(new TaskCommentCreated($this->task, auth()->user(), $this->task->from));
        }

        $this->newComment = null;
        $this->emit('commentSaved');
        $this->task->load(['comments', 'comments.user']);
    }

    public function completeTask()
    {
        if ($this->task->to_id != $this->task->from_id) {
            auth()->user()->task_completion_points()->create([
                'user_id' => auth()->id(),
                'type' => $this->getTaskCompletionPointType(),
                'task_id' => $this->task->id,
            ]);
        } else {
            $this->task->update([
                'confirmed' => true,
            ]);
        }

        $this->task->update([
            'status' => Task::STATUS_COMPLETED,
        ]);

        if ((config('app.env') === 'production' || config('app.env') === 'local') && $this->task->to_id != $this->task->from_id) {
            Mail::to($this->task->from->email)->send(new TaskCompleted($this->task));
        }

        if (session()->get('todo-prev-url') == route(auth()->user()->type.'.todo.edit', ['task' => $this->task])) {
            return redirect()->route(auth()->user()->type.'.dashboard');
        }

        return redirect(session()->get('todo-prev-url'));
    }

    public function reopen(): void
    {
        $this->task->update([
            'status' => Task::STATUS_OPENED,
            'confirmed' => false,
        ]);

        if (auth()->user()->task_completion_points()->where('task_id', $this->task->id)->exists()) {
            auth()->user()->task_completion_points()->where('task_id', $this->task->id)->delete();
        }

        $this->emit('reopened');
    }

    public function backToList()
    {
        if (session()->get('todo-prev-url') == route(auth()->user()->type.'.todo.show', ['task' => $this->task->id])) {
            return redirect()->route(auth()->user()->type.'.dashboard');
        }

        return redirect(session()->get('todo-prev-url'));
    }

    private function getTaskCompletionPointType(): int
    {
        if (date('Y-m-d', strtotime((string) $this->task->deadline)) === date('Y-m-d')) {
            return TaskCompletionPoint::TYPE_LAST_DAY;
        }
        if (strtotime((string) $this->task->deadline) < time()) {
            return TaskCompletionPoint::TYPE_OVER_DEADLINE;
        }

        return TaskCompletionPoint::TYPE_WITHIN_DEADLINE;
    }
}
