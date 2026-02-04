@extends('layout.master')

@section('title')
    Admin Dashboard
@endsection

@section('extra_css')
    <link rel="stylesheet" href="{{asset('assets/css/cases/list.css')}}?v={{time()}}">
    <link rel="stylesheet" href="{{asset('assets/css/workshops.css')}}?v={{time()}}">
@endsection

@section('extra_js')
    <script src="{{asset('assets/js/toggle_year_month_list.js')}}"></script>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            {{ Breadcrumbs::render('other-activities') }}
            <h1>{{__('other-activity.other-activities')}}</h1>
            <a href="{{route('admin.other-activities.create')}}">{{__('other-activity.new')}}</a><br>
            <div class="row ml-0">
                <a href="{{route('admin.other-activities.filter')}}" id="filter" class="mb-4 mt-3 btn-radius"
                style="">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="height:20px; width:20px" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>{{__('workshop.apply_filter')}}</a>
            </div>
        </div>
        <div class="col-12 case-list-holder">
            @if($saved_activities->count())
                <div class="case-list-in col-12 group" onclick="yearOpen('saved_other_activity')">
                    {{__('other-activity.saved_activities')}}
                    <button class="caret-left float-right">
                        <svg id="ysaved_other_activity" xmlns="http://www.w3.org/2000/svg"
                             style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                             stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                </div>
                <div class="lis-element-div" id="saved_other_activity" style="display: none">
                    @foreach($saved_activities as $other_activity)
                        <x-other-activity.other_activity_case_component
                                :other_activity="$other_activity"
                        />
                    @endforeach
                </div>
            @endif
            @foreach($years as $year)
                <div class="case-list-in col-12 group" onclick="yearOpen({{$year}})">
                    {{$year}}
                    <button class="caret-left float-right">
                        <svg id="y{{$year}}" xmlns="http://www.w3.org/2000/svg"
                             style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                             stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                </div>
                <div class="lis-element-div" id="{{$year}}">
                    @foreach($months as $month)
                        @php $month_id = uniqid() @endphp
                        @if((string)\Illuminate\Support\Str::of($month)->before('-') == (string)$year)
                            <div class="case-list-in col-12 group" onclick="monthOpen('{{$month_id}}')">
                                {{$month}}
                                <button class="caret-left float-right">
                                    <svg id="m{{$month_id}}" xmlns="http://www.w3.org/2000/svg"
                                         style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24"
                                         stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>
                            </div>
                            <div class="lis-element-div-c" id="{{$month_id}}">
                                @foreach($other_activities->whereNotNull('date')->sortByDesc('date') as $other_activity)
                                    @if((string)\Carbon\Carbon::parse($other_activity->date)->month == \Illuminate\Support\Str::of($month)->after('-')
                                    && (string)\Carbon\Carbon::parse($other_activity->date)->year == \Illuminate\Support\Str::of($year)->after('-'))
                                        <x-other-activity.other_activity_case_component
                                                :other_activity="$other_activity"
                                        />
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>
@endsection
