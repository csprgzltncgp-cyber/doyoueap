<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\TaskIssued;
use App\Models\Task;
use App\Models\TaskAttachment;
use App\Models\TaskCompletionPoint;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::query()
            ->with(['from', 'to'])
            ->orderBy('created_at')
            ->get();

        $admins = User::query()
            // Peti team
            ->when(Auth::user()->id == 506, fn ($query) => $query->whereIn('id', [706, 1087, 1088, 1113, 167, 1093]))
            ->where('type', 'like', '%admin%')
            ->has('assigned_task')
            ->orderBy('name')
            ->get();

        return view('admin.todo.index', ['tasks' => $tasks, 'admins' => $admins]);
    }

    public function issued()
    {
        return view('admin.todo.issued');
    }

    public function completed()
    {
        $tasks = Task::query()
            ->where('to_id', Auth::id())
            ->where('status', Task::STATUS_COMPLETED)
            ->orderBy('deadline')->get()->filter(fn ($task): bool => ! Carbon::parse($task->deadline)->isCurrentWeek());

        return view('admin.todo.completed', ['tasks' => $tasks]);
    }

    public function create()
    {
        $admins = User::query()
            ->where('type', 'like', '%admin%')
            ->where('type', '!=', 'production_translating_admin')
            ->where('connected_account', null)
            ->orderBy('name')
            ->get();

        session()->put('todo-prev-url', url()->previous());

        $forwarded_title = session()->get('forwarded-task-title');
        $forwarded_description = session()->get('forwarded-task-description');
        $connectable_users = User::query()
            ->where('type', 'like', '%admin%')
            ->where('type', '!=', 'production_translating_admin')
            ->where('connected_account', null)
            ->whereNotIn('id', [auth()->id()])
            ->get();

        return view('admin.todo.create', ['admins' => $admins, 'forwarded_title' => $forwarded_title, 'forwarded_description' => $forwarded_description, 'connectable_users' => $connectable_users]);
    }

    public function store()
    {
        $validated = request()->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required'],
            'to_id' => ['exists:users,id', 'required'],
            'deadline' => ['date', 'required'],
            'attachments' => ['nullable'],
            'attahcments.*' => ['file', 'mimes:jpeg,png,jpg,gif,svg,pdf,doc,docx,xls,xlsx,ppt,pptx,zip,rar'],
        ]);

        $task = Task::query()->create(array_merge(
            collect($validated)->except('attachments')->toArray(),
            ['from_id' => auth()->id(), 'status' => Task::STATUS_CREATED]
        ));

        if (request()->has('connected_users')) {
            $connectable_user_ids = array_unique(request()->get('connected_users'));
            $task->connected_users()->attach($connectable_user_ids);
        }

        if (request()->has('attachments')) {
            foreach (request()->file('attachments') as $attachment) {
                $task->attachments()->create([
                    'filename' => $attachment->getClientOriginalName(),
                    'path' => $attachment->store('task-attachments/'.$task->id, 'private'),
                ]);
            }
        }

        $task->load(['from', 'to']);

        if ((config('app.env') === 'production' || config('app.env') === 'local') && $task->to_id != auth()->id()) {
            Mail::to($task->to)->send(new TaskIssued($task));
        }

        if (session()->get('todo-prev-url') == route(auth()->user()->type.'.todo.create')) {
            return redirect()->route(auth()->user()->type.'.dashboard');
        }

        return redirect(session()->get('todo-prev-url'));
    }

    public function delete($id)
    {
        if (! $task = Task::query()->find($id)) {
            return response()->json(['error' => 'Task not found']);
        }

        $task->delete();

        if (session()->get('todo-prev-url') == route(auth()->user()->type.'.todo.create')) {
            return redirect()->route(auth()->user()->type.'.dashboard');
        }

        return response()->json('ok');
    }

    public function statistics()
    {
        $over_deadline = TaskCompletionPoint::query()
            ->where('type', TaskCompletionPoint::TYPE_OVER_DEADLINE)
            ->get()
            ->groupBy('user_id')
            ->map(function ($points, $user_id) {
                $user = User::query()->with('task_completion_points')->find($user_id);
                if ($user) {
                    $all_tasks = $user->task_completion_points->count();

                    return [
                        'user' => $user->name,
                        'points' => count($points),
                        'all_tasks' => $all_tasks,
                        'ratio' => count($points) / $all_tasks,
                    ];
                }
            })->sortByDesc('ratio')->filter(fn ($value): bool => $value != null);

        $last_day = TaskCompletionPoint::query()
            ->where('type', TaskCompletionPoint::TYPE_LAST_DAY)
            ->get()
            ->groupBy('user_id')
            ->map(function ($points, $user_id) {
                $user = User::query()->with('task_completion_points')->find($user_id);
                if ($user) {
                    $all_tasks = $user->task_completion_points->count();

                    return [
                        'user' => $user->name,
                        'points' => count($points),
                        'all_tasks' => $all_tasks,
                        'ratio' => count($points) / $all_tasks,
                    ];
                }
            })->sortByDesc('ratio')->filter(fn ($value): bool => $value != null);

        $within_deadline = TaskCompletionPoint::query()
            ->where('type', TaskCompletionPoint::TYPE_WITHIN_DEADLINE)
            ->get()
            ->groupBy('user_id')
            ->map(function ($points, $user_id) {
                $user = User::query()->with('task_completion_points')->find($user_id);
                if ($user) {
                    $all_tasks = $user->task_completion_points->count();

                    return [
                        'user' => $user->name,
                        'points' => count($points),
                        'all_tasks' => $all_tasks,
                        'ratio' => count($points) / $all_tasks,
                    ];
                }
            })->sortByDesc('ratio')->filter(fn ($value): bool => $value != null);

        return view('admin.todo.statistics', ['over_deadline' => $over_deadline, 'last_day' => $last_day, 'within_deadline' => $within_deadline]);
    }

    public function filter()
    {
        $admins = User::query()
            ->where('type', 'like', '%admin%')
            ->where('connected_account', null)
            ->orderBy('name')
            ->get();

        return view('admin.todo.filter', ['admins' => $admins]);
    }

    public function filter_result()
    {
        $filters = array_filter(request()->all());

        $incomming_builder = Task::query()
            ->where('to_id', auth()->id());

        foreach ($filters as $key => $value) {
            $builder = $incomming_builder->where($key, $value);
        }

        $incomming_tasks = $incomming_builder->get();

        $issued_builder = Task::query()
            ->where('from_id', auth()->id());

        foreach ($filters as $key => $value) {
            if ($key == 'from_id') {
                $key = 'to_id';
            }

            $builder = $issued_builder->where($key, $value);
        }

        $issued_tasks = $issued_builder->get();

        $tasks = $incomming_tasks->merge($issued_tasks);

        return view('admin.todo.result', ['tasks' => $tasks]);
    }

    public function download_attachment($id)
    {
        if (! $attachment = TaskAttachment::query()->find($id)) {
            return response()->json(['error' => 'Attachment not found']);
        }

        return response()->download(storage_path('app/'.$attachment->path), $attachment->filename);
    }
}
