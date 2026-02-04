@extends('layout.master')

@section('title')
    Admin Dashboard
@endsection

@section('extra_js')
    <script src="{{asset('assets/js/task/index.js')}}?v={{time()}}"></script>
@endsection

@section('extra_css')
    <link rel="stylesheet" href="{{asset('assets/css/cases/list_in_progress.css')}}?t={{time()}}">
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <h1>TODO</h1>

            <x-tasks.menu/>

            @forelse($tasks as $task)
                @component('components.tasks.index',[
                    'task' => $task
                ])@endcomponent
            @empty
                <p>{{__('task.no_tasks')}}!</p>
            @endforelse
        </div>
    </div>
@endsection
