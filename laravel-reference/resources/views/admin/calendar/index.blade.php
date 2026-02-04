@extends('layout.master')

@section('title')
    Admin Dashboard
@endsection

@section('extra_css')
    <link rel="stylesheet" href="{{asset('assets/css/cases/list_in_progress.css')}}?t={{time()}}">
    <link rel="stylesheet" href="{{asset('assets/css/tasks.css?v=').time()}}">
@endsection

@section('content')
<div>
    <div class="row">
        <div class="col-12">
            {{ Breadcrumbs::render('todo-calendar') }}
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <h1>TODO</h1>

            <x-tasks.menu/>

            <div class="mb-5"></div>

            <livewire:admin.calendar />
        </div>
    </div>
</div>
@endsection
