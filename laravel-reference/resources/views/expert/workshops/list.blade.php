@extends('layout.master')

@section('title')
    Expert Dashboard
@endsection

@section('extra_css')
    <link rel="stylesheet" href="{{asset('assets/js/cases_list_in_progress.js')}}?v={{time()}}">
    <link rel="stylesheet" href="{{asset('assets/css/workshops.css')}}?v={{time()}}">
@endsection

@php
    session(['list_url' => \Illuminate\Support\Facades\Request::url()])
@endphp

@section('content')
    <div class="row">
        <div class="col-12">
            <h1>{{__('common.all_workshop')}}</h1>
            <a href="{{route('expert.workshops.list_closed')}}">{{__('workshop.closed_workshops')}}</a>
        </div>
        <div class="col-12 case-list-holder">
            @foreach($workshop_cases as $workshop)
                @component('components.workshops.workshop_case_component',
                 [
                   'workshop_case' => $workshop,
                 ])@endcomponent
            @endforeach
        </div>
    </div>
@endsection

