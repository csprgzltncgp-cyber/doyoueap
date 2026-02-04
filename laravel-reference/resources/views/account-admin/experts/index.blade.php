@extends('layout.master')

@section('extra_css')
    <link rel="stylesheet" href="{{asset('assets/css/list.css')}}?v={{time()}}">
    <style>
        .list-element {
            cursor: pointer;;
        }

        .button{
            background: rgb(89, 198, 198);
            padding: 20px 20px !important;
            color: white !important;
        }
    </style>
@endsection

@section('title')
    Admin Dashboard
@endsection

@section('content')
    <div class="row m-0">

        <h1 class="col-12 pl-0">{{__('common.list-of-experts')}}</h1>
        <div>
            <a class="col-12 pl-0 d-block button btn-radius" href="{{route('account_admin.experts.filter')}}">
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="height: 20px; width: 20px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                {{__("common.search")}}
            </a>
        </div>
        @foreach($experts as $expert)
            <div class="list-element col-12 ">
                <span>{{$expert->name}}</span>
                <a class="float-right" href="{{route('account_admin.experts.show', ['user' => $expert])}}">
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
