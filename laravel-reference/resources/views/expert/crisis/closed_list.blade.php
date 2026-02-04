@extends('layout.master')

@section('title')
    Expert Dashboard
@endsection

@section('extra_css')
    <link rel="stylesheet" href="{{asset('assets/css/cases/list.css')}}?v={{time()}}">
    <link rel="stylesheet" href="{{asset('assets/css/crisis.css')}}?v={{time()}}">
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <h1>{{__('crisis.closed_crisises')}}</h1>
            <a href="{{route('expert.crisis.list')}}">{{__('common.back-to-list')}}</a>
        </div>
        <div class="col-12 case-list-holder">
            @foreach($crisis_cases as $crisis)
                @component('components.crisises.crisis_case_component',
                 [
                   'crisis_case' => $crisis,
                 ])@endcomponent
            @endforeach
        </div>
    </div>
@endsection
