<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TaskComment;

class TaskCommentController extends Controller
{
    public function store(Task $task)
    {
        $validated = request()->validate([
            'value' => ['string', 'required'],
        ]);

        $task->comments()->create($validated);

        return redirect(session()->get('todo-prev-url'));
    }

    public function update(TaskComment $taskComment)
    {
        $validated = request()->validate([
            'value' => ['string', 'required'],
        ]);

        $taskComment->update($validated);

        return redirect(session()->get('todo-prev-url'));
    }
}
