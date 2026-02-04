@extends('layout.master')

@section('title')
    Admin Dashboard
@endsection

@section('extra_css')
    <link rel="stylesheet" href="{{asset('assets/css/cases/list_in_progress.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/tasks.css?v=').time()}}">
@endsection

@section('extra_js')
    <script>
        if (String(window.performance.getEntriesByType("navigation")[0].type) === "back_forward") {
            location.reload();
        }
    </script>
@endsection

@section('variables')
    @php
        $display = 'block';
    @endphp
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            {{ Breadcrumbs::render('dashboard') }}

            @if(!Auth::user()->connected_account && Auth::user()->type != 'supervisor_admin')
                <h1>TODO</h1>


                <x-tasks.menu/>

                <div class="mb-5"></div>

                @livewire('admin.todo.over-deadline-tasks')
                @livewire('admin.todo.today-tasks')
                @livewire('admin.todo.this-week-tasks')
                @livewire('admin.todo.upcomming-tasks')
                @livewire('admin.todo.completed-tasks')
           @endif
        </div>
    </div>
@endsection
