@extends('layout.master')

@section('title')
    Admin Dashboard
@endsection

@section('extra_css')
    <link rel="stylesheet" href="{{asset('assets/css/cases/list_in_progress.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/tasks.css?v=').time()}}">
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        {{Breadcrumbs::render('affiliate-search-workflow')}}
        <h1>{{__('affiliate-search-workflow.menu')}}</h1>

        <x-affiliate-search.menu/>

        <div class="mb-5"></div>

        @livewire('admin.affiliate-search.over-deadline')
        @livewire('admin.affiliate-search.today')
        @livewire('admin.affiliate-search.this-week')
        @livewire('admin.affiliate-search.upcomming')
        @livewire('admin.affiliate-search.completed')
    </div>
</div>
@endsection
