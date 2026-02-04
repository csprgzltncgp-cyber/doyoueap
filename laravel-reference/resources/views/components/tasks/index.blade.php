@props([
    'lineColor' => 'rgba(226,239,241,1)',
    'onlyShowDays' => false,
    'showToUser' => false,
    'showDelete' => false,
    'task'
])

<style>
    .closed-task-line{
    opacity: 0.2 !important;
    }

    .closed-task-line:hover{
        opacity: 1 !important;
        transition: all;
        transition-duration: 0.5s;
    }

    .delete-button{
        background: rgb(89, 198, 198) !important;
        color:white !important;
        margin-left: 0 !important;
        opacity: 0.3;
    }

    .delete-button:hover{
        opacity: 1;
        transition: all;
        transition-duration: 0.5s;
    }
</style>

<div class="row col-12 case-list-in-progress task-admin-component mb-0
    {{
        ($task->status == \App\Models\Task::STATUS_COMPLETED && $task->confirmed) ||
        ($task->status == \App\Models\Task::STATUS_COMPLETED && !$showDelete && !$task->has_new_comments())
        ? 'closed-task-line' : ''
    }}"

    @if($task->has_new_comments()) style="opacity: 1 !important;" @endif
style="min-hegiht: 30px;">
    <p
            @if(($task->status == \App\Models\Task::STATUS_COMPLETED && $task->confirmed) || ($task->status == \App\Models\Task::STATUS_COMPLETED && !$showDelete))
                style="background: rgb(127, 64, 116) !important; color: white;"
            @elseif($task->status == \App\Models\Task::STATUS_COMPLETED && $showDelete)
                style="
                background: -o-linear-gradient(left, rgba(195,203,207,0.7) 0%, rgba(195,202,207,0.7) 100%,{{$lineColor}} 100%, {{$lineColor}} 100%);
                background: -webkit-linear-gradient(left, rgba(195,203,207,0.7) 0%, rgba(195,202,207,0.7) 100%, {{$lineColor}} 100%, {{$lineColor}} 100%);
                background: -o-linear-gradient(left, rgba(195,203,207,0.7) 0%, rgba(195,202,207,0.7) 100%, {{$lineColor}} 100%, {{$lineColor}} 100%);
                background: -ms-linear-gradient(left, rgba(195,203,207,0.7) 0%, rgba(195,202,207,0.7) 100%, {{$lineColor}} 100%, {{$lineColor}} 100%);
                background: linear-gradient(to right, rgba(195,203,207,0.7) 0%, rgba(195,202,207,0.7) 100%, {{$lineColor}}) 100%, {{$lineColor}} 100%);
                "
            @elseif($task->status == \App\Models\Task::STATUS_OPENED)
                style="
                background: -o-linear-gradient(left, rgba(195,203,207,0.7) 0%, rgba(195,202,207,0.7) 50%,{{$lineColor}} 50%, {{$lineColor}} 100%);
                background: -webkit-linear-gradient(left, rgba(195,203,207,0.7) 0%, rgba(195,202,207,0.7) 50%, {{$lineColor}} 50%, {{$lineColor}} 100%);
                background: -o-linear-gradient(left, rgba(195,203,207,0.7) 0%, rgba(195,202,207,0.7) 50%, {{$lineColor}} 50%, {{$lineColor}} 100%);
                background: -ms-linear-gradient(left, rgba(195,203,207,0.7) 0%, rgba(195,202,207,0.7) 50%, {{$lineColor}} 50%, {{$lineColor}} 100%);
                background: linear-gradient(to right, rgba(195,203,207,0.7) 0%, rgba(195,202,207,0.7) 50%, {{$lineColor}}) 50%, {{$lineColor}} 100%);
                "
            @else
                style="background: {{$lineColor}};"
            @endif
    >
        #TD{{$task->id}} -

        @if($onlyShowDays)
            {{Str::title(\Carbon\Carbon::parse($task->deadline)->translatedFormat('l'))}} -
        @else
            {{\Carbon\Carbon::parse($task->deadline)->format('Y-m-d')}} -
        @endif

        @if($showToUser)
            {{optional($task->to)->name}} -
        @else
            {{optional($task->from)->name}} -
        @endif

        {{\Illuminate\Support\Str::limit(strip_tags(html_entity_decode($task->title)), 55)}}
    </p>

    @if(!$showDelete)
        <a class="btn-radius" href="{{route(auth()->user()->type . '.todo.show', ['task' => $task])}}">
            <div class="row m-0 justify-content-center align-items-center">
                <img src="{{asset('assets/img/select.svg')}}" class="mr-1" style="height: 20px; width: 20px" alt="">
                {{__('common.select')}}
            </div>

        </a>
    @else
        <a class="btn-radius" href="{{route(auth()->user()->type . '.todo.edit', ['task' => $task])}}">
            <img src="{{asset('assets/img/select.svg')}}" class="mr-1" style="height: 20px; width: 20px" alt="">
            {{__('common.select')}}
        </a>
    @endif

    @if($showDelete)
        <a href="#" onclick="deleteTask({{$task->id}})" class="delete-button btn-radius" style="--btn-min-width:var(--btn-func-width);">
            <svg xmlns="http://www.w3.org/2000/svg" style="height:20px; width:20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
        </a>
    @endif

    @if($task->is_new())
        <p class="closeable">
            {{__('common.new')}}
        </p>
    @endif

    @if($task->has_new_comments())
        <p class="_2month" style="background-color: #007bff !important">
            {{__('task.new_comment')}}!
        </p>
    @endif

    @if($task->is_over_deadline())
        <p class="_3month">
            <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="height:20px; width:20px" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            {{__('task.over_deadline')}}
            : {{\Carbon\Carbon::parse($task->deadline)->diffInDays(\Carbon\Carbon::now())}} {{__('task.day')}}!
        </p>
    @endif

    @if($task->is_last_day())
        <p class="_2month">
            <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="height:20px; width:20px" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            {{__('task.last_day')}}
        </p>
    @endif

    @if($showDelete)
        @if($task->status == \App\Models\Task::STATUS_COMPLETED && !$task->confirmed)
            <p class="closeable" style="background: rgb(127, 64, 116) !important">
                {{__('task.completed')}}
            </p>
        @endif
    @endif
</div>
