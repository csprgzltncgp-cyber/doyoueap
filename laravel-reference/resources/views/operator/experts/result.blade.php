@extends('layout.master')

@section('title')
    Operator Dashboard
@endsection

@section('extra_css')
    <link rel="stylesheet" href="{{asset('assets/css/workshops.css')}}?v={{time()}}">
    <link rel="stylesheet" href="{{asset('assets/css/list.css')}}?v={{time()}}">
@endsection

@section('content')
    <div class="row m-0 w-100">
        <h1 class="col-12 pl-0">{{__("common.search")}}</h1>
        <ul id="workshop-submenus" class="row ml-0 w-100">
            <li class="mr-0"><a class="col-12 pl-0 d-block add-new-workshop btn-radius" href="{{route('operator.experts.filter')}}">
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="height: 20px; width: 20px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>{{__("common.search")}}</a></li>
            <li><a class="col-12 pl-0 d-block add-new-workshop btn-radius" href="{{route('operator.experts.index')}}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="width:20px; height:20px" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                    </svg>{{__('common.list-of-experts')}}</a></li>
        </ul>

        @if(!$users->count())
            <p>{{__('workshop.no_filter_result')}}</p>
        @endif
        @foreach($users as $expert)
            <div class="list-element col-12">
                <span style="padding: 10px 10px; margin-bottom:0;">{{$expert->name}}</span>
                <a style="padding: 10px 10px; margin-bottom:0;" class="float-right" href="{{route('operator.experts.show', ['user' => $expert])}}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 mb-1" style="height: 20px; width:20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    {{__('common.show-expert-data')}}
                </a>
            </div>
        @endforeach
    </div>
@endsection
