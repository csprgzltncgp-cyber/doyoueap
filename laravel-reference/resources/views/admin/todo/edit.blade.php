@extends('layout.master')

@section('title')
    Admin Dashboard
@endsection

@section('extra_css')
    <link rel="stylesheet" href="{{asset('assets/css/form.css')}}?v={{time()}}">
    <link rel="stylesheet" href="{{asset('assets/css/cases/datetime.css')}}">
    <link href="https://cdn.jsdelivr.net/npm/suneditor@latest/dist/css/suneditor.min.css" rel="stylesheet">
    <style media="screen">
        form {
            max-width: none;
        }
    </style>
@endsection

@section('extra_js')
    <script src="{{asset('assets/js/datetime.js')}}"></script>
    <script src="https://cdn.jsdelivr.net/npm/suneditor@latest/dist/suneditor.min.js"></script>
    <script>
       const editor = SUNEDITOR.create(document.getElementById('editor'), {
            height: 300,
       });

        $('#deadline').datepicker({
            format: 'yyyy-mm-dd',
        });
    </script>
@endsection

@section('content')
    <h1>{{__('task.edit')}}</h1>
    <form method="post" class="row">
        @csrf
        <div class="col-12 col-sm-12">
            <div class="form-group">
                <label for="editor">{{__('task.description')}}:</label>
                <textarea name="description" id="editor"
                          @if($task->status == \App\Models\Task::STATUS_COMPLETED) disabled @endif>{{$task->description}}</textarea>
            </div>

            @if($task->comment)
                <div class="form-group" id="comment-group">
                    <label for="comment">{{__('task.comment')}}</label>
                    <textarea name="value" id="comment" cols="30"
                              rows="10"
                              @if($task->status == \App\Models\Task::STATUS_COMPLETED) disabled @endif >{{$task->comment->value}}</textarea>
                </div>
            @endif

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="to_id">{{__('task.colleague')}}:</label>
                    <select name="to_id" id="to_id">
                        @foreach($admins as $admin)
                            <option value="{{$admin->id}}"
                                    @if($admin->id == $task->to_id) selected @endif>{{$admin->name}}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group col-md-6">
                    <label for="deadline">{{__('task.deadline')}}:</label>
                    <input type="text" name="deadline" id="deadline" placeholder="{{__('task.deadline')}}"
                           @if($task->status == \App\Models\Task::STATUS_COMPLETED) disabled @endif
                           value="{{\Carbon\Carbon::parse($task->deadline)->format('Y-m-d')}}">
                </div>
            </div>
        </div>
        @if($task->status == \App\Models\Task::STATUS_COMPLETED)
            <div class="col-12 d-flex">
                <div class="form-group">
                    <button type="button" style="background: rgb(127, 64, 116) !important;
    color: white;" disabled>
                        <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px" class="mr-1"
                             fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{__('task.completed')}}
                    </button>
                </div>
            </div>
        @else
            <div class="col-12">
                <div class="form-row d-flex">
                    <div class="form-group mr-3" style="padding-left: 5px">
                        <button type="submit">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                 style="width: 20px; height: 20px; margin-bottom: 1px;" class="mr-1" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                            </svg>
                            {{__('common.save')}}
                        </button>
                    </div>
                    <div class="form-group">
                        <button type="button" onclick="$('#complete').submit();">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                 style="width: 20px; height: 20px; margin-bottom: 1px;" class="mr-1" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                            {{__('task.completed')}}
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </form>
    <form id="complete" action="{{route('admin.todo.complete', ['task' => $task])}}" method="post">
        @csrf
    </form>
@endsection
