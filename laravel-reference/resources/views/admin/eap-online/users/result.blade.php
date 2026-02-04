@extends('layout.master')

@section('title')
    Admin Dashboard
@endsection

@section('extra_css')
    <link rel="stylesheet" href="/assets/css/list.css?v={{time()}}">
    <link rel="stylesheet" href="/assets/css/workshops.css?v={{time()}}">
    <link rel="stylesheet" href="/assets/css/eap-online/master.css?v={{time()}}">
    <style>
        .list-elem {
            padding: 20px 40px;
            background: rgb(222, 240, 241);
            color: black;
            text-transform: uppercase;
            margin-right: 10px;
            min-width: 200px;
        }

        .list-elem:hover {
            color: black;
        }

        .button {
            padding: 20px 40px;
            background: rgb(0, 87, 95);
            border: none;
            color: white;
            text-transform: uppercase;
        }
    </style>
@endsection

@section('content')
    <div class="row m-0 w-100">
        {{ Breadcrumbs::render('eap-online.users.filtered') }}
        <h1 class="col-12 pl-0">{{__('workshop.filter_result')}}</h1>
        <ul id="workshop-submenus" class="w-100">
            <li class="mr-0">
                <a class="col-12 pl-0 d-block add-new-workshop btn-radius" href="{{route('admin.eap-online.users.filter.result')}}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="height:20px; width:20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                   {{__('workshop.apply_filter')}}
                </a>
            </li>
            <li>
                <a class="col-12 pl-0 d-block add-new-workshop btn-radius" style="--btn-margin-left:var(--btn-margin-x)" href="{{route('admin.eap-online.users.list')}}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="height:20px; width:20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                    </svg>
                    {{__('eap-online.articles.list')}}
                </a>
            </li>
        </ul>

        @if(!$users->count())
            <p>{{__('workshop.no_filter_result')}}</p>
        @endif
        <div class="row col-12 d-flex flex-column">
            @foreach($users as $user)
                @component('components.eap-online.user_line_component',['user' => $user,])@endcomponent
            @endforeach
        </div>
    </div>
@endsection
