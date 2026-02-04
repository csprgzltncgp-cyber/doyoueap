@extends('layout.master')

@section('title')
    Expert Dashboard
@endsection

@section('extra_css')
    <link rel="stylesheet" href="/assets/css/cases/list.css?v={{time()}}">
    <link rel="stylesheet" href="/assets/css/workshops.css?v={{time()}}">
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <h1>{{__('workshop.closed_workshops')}}</h1>
            <a href="{{route('expert.workshops.list')}}">{{__('common.back-to-list')}}</a>
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
