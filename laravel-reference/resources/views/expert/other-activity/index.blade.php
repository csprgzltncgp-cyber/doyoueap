@extends('layout.master')

@section('title')
    Expert Dashboard
@endsection

@section('extra_css')
    <link rel="stylesheet" href="{{asset('assets/js/cases_list_in_progress.js')}}?v={{time()}}">
    <link rel="stylesheet" href="{{asset('assets/css/workshops.css')}}?v={{time()}}">
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <h1>{{__('other-activity.all')}}</h1>
            <a href="{{route('expert.other-activities.index_closed')}}">{{__('other-activity.closed')}}</a>
        </div>
        <div class="col-12 case-list-holder">
            @foreach($other_activities as $other_activity)
                <x-other-activity.other_activity_case_component
                        :other_activity="$other_activity"
                />
            @endforeach
        </div>
    </div>
@endsection

