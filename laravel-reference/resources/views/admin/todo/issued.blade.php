@extends('layout.master')

@section('title')
    Admin Dashboard
@endsection

@section('extra_css')
    <link rel="stylesheet" href="{{asset('assets/css/cases/list_in_progress.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/tasks.css?v=') . time()}}">
    <style>
        select{
            width: 204px;
            display: block;
            appearance:none !important;
            -moz-appearance:none !important;
            -webkit-appearance:none !important;
            border:2px solid rgb(89,198,198) !important;
            padding:10px 0px 10px 15px!important;
            margin-bottom:20px !important;
            border-radius: 0px !important;
            outline: none !important;
            color:rgb(89,198,198) !important;
        }
    </style>
@endsection

@section('extra_css')
    <link rel="stylesheet" href="{{asset('assets/css/cases/list_in_progress.css')}}?t={{time()}}">
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            {{ Breadcrumbs::render('todo.issued') }}
            <h1>TODO</h1>

            <x-tasks.menu/>

            @livewire('admin.todo.issued-tasks')
        </div>
    </div>
@endsection
