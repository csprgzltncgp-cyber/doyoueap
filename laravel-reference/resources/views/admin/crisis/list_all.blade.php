@extends('layout.master')

@section('title')
    Admin Dashboard
@endsection

@section('extra_css')
    <link rel="stylesheet" href="{{asset('assets/css/cases/list.css')}}?v={{time()}}">
    <link rel="stylesheet" href="{{asset('assets/css/crisis.css')}}?v={{time()}}">
@endsection

@section('extra_js')
    <script src="{{asset('assets/js/crisis.js')}}"></script>
@endsection

@php
    session(['list_url' => \Illuminate\Support\Facades\Request::url()])
@endphp

@section('content')
    <div class="row">
        <div class="col-12">
            {{ Breadcrumbs::render('crisis') }}
            <h1>{{ __('crisis.crisis') }}</h1>
            <a href="{{route('admin.crisis.new')}}">{{ __('crisis.new_crisis') }}</a><br>
            <a href="{{route('admin.crisis.filter')}}" id="filter" class="mb-4 mt-3 btn-radius btn-max-width">
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="height:20px; width:20px" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>{{__('crisis.apply_filter')}}</a>
        </div>
        <div class="col-12 case-list-holder">
            @if($saved_crisis_cases->count() > 0)
                <div class="case-list-in col-12 group" onclick="yearOpen('saved_crisis_cases')">
                    {{__('crisis.saved_crisis_cases')}}
                    <button class="caret-left float-right">
                        <svg id="ysaved_crisis_cases" xmlns="http://www.w3.org/2000/svg"
                             style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                             stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                </div>
                <div class="lis-element-div" id="saved_crisis_cases" style="display: none">
                    @foreach($saved_crisis_cases as $crisis_case)
                        @component('components.crisises.crisis_case_component',
                                                          [
                                                            'crisis_case' => $crisis_case,
                                                          ])@endcomponent
                    @endforeach
                </div>
            @endif
            @foreach($filtered_years as $filtered_year)
                <div class="case-list-in col-12 group" onclick="yearOpen({{$filtered_year}})">
                    {{$filtered_year}}
                    <button class="caret-left float-right">
                        <svg id="y{{$filtered_year}}" xmlns="http://www.w3.org/2000/svg"
                             style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                             stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                </div>
                <div class="lis-element-div" id="{{$filtered_year}}">
                    @foreach($filtered_months as $filtered_month)
                        @php $test_year = substr($filtered_month,0,-3); @endphp
                        @if($test_year == $filtered_year)
                            @php $month_id = str_replace("-","_", $filtered_month); @endphp
                            <div class="crisis-list-holder">
                                <div class="case-list-in col-12 group" onclick="monthOpen('{{$month_id}}')">
                                    {{$filtered_month}}
                                    <button class="caret-left float-right">
                                        <svg id="m{{$month_id}}" xmlns="http://www.w3.org/2000/svg"
                                             style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24"
                                             stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </button>
                                </div>
                                <div class="crisis-list" id="{{$month_id}}">
                                </div>
                                <img id="m_{{$month_id}}" class="d-none spinner"
                                     src="{{asset('assets/img/spinner.svg')}}"
                                     alt="spinner">
                                <div class="d-flex justify-content-center">
                                    <button class="load-more-cases d-none" id="m_{{$month_id}}"
                                            onclick="loadMore('{{$month_id}}', this, false)">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-1"
                                             style="width: 20px; height: 20px"
                                             fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                        {{__('common.load-more')}}</button>
                                    <button class="load-all-cases load-more-cases d-none" id="m_{{$month_id}}"
                                            onclick="loadMore('{{$month_id}}', this, true)">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-1"
                                             style="width: 20px; height: 20px"
                                             fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M19 13l-7 7-7-7m14-8l-7 7-7-7"/>
                                        </svg>
                                        {{__('common.load-all')}}</button>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>
@endsection
