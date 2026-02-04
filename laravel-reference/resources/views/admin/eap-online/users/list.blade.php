@extends('layout.master')

@section('title')
    Admin Dashboard
@endsection

@section('extra_css')
    <link rel="stylesheet" href="/assets/css/cases/view.css?t={{time()}}">
    <link rel="stylesheet" href="/assets/css/workshops.css?v={{time()}}">
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
    <div class="row">
        <div class="col-12 mb-3">
            {{ Breadcrumbs::render('eap-online.users') }}
            <h1>{{__('eap-online.users.title')}}</h1>
            <a href="{{route('admin.eap-online.users.filter.view')}}" id="filter" class="mb-4 mt-3 btn-radius" style="--btn-max-width: var(--btn-min-width)"><svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="height:20px; width:20px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
</svg>{{__('workshop.apply_filter')}}</a>
        </div>
        <div class="col-12 d-flex flex-column">
                @forelse($users as $user)
                    @component('components.eap-online.user_line_component',['user' => $user,])@endcomponent
                @empty
                    <p>{{__('eap-online.users.no_users')}}</p>
                @endforelse
        </div>
        <div class="col-12">
            {{$users->links()}}
        </div>
        <div class="col-4 col-lg-2 back-button mb-5">
            <a href="{{ route('admin.eap-online.actions') }}">{{__('common.back-to-list')}}</a>
        </div>
    </div>
@endsection
