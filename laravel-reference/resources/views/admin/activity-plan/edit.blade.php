@extends('layout.master')

@section('title')
    Admin Dashboard
@endsection

@section('extra_js')
    <script src="{{asset('js/client/master.js')}}?v={{time()}}"></script>
@endsection

@section('extra_css')
    <link rel="stylesheet" href="{{asset('assets/css/form.css')}}?v={{time()}}">
    <link rel="stylesheet" href="{{ asset('/assets/css/activity-plan.css') }}?v={{time()}}">
    <link rel="stylesheet" href="{{asset('assets/css/bordered-checkbox.css')}}?v={{time()}}">
@endsection

@section('content')
    {{ Breadcrumbs::render('activity-plan.edit',  $activity_plan) }}

    @livewire('admin.activity-plan.edit', ['activity_plan' => $activity_plan])
@endsection
