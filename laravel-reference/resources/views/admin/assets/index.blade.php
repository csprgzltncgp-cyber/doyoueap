
@extends('layout.master')

@section('title')
    Admin Dashboard
@endsection

@section('extra_css')
    <link rel="stylesheet" href="{{asset('assets/css/submenu.css')}}">
@endsection

@section('content')
    <div class="row">
        <div class="col-12 mb-5">
            {{ Breadcrumbs::render('assets') }}
            <h1>{{__('common.assets')}}</h1>
        </div>
        <div class="col-12 mb-5">
            <div class="d-flex flex-column">
                <div class="list-holder">
                    <a class="list-elem" href="{{route(auth()->user()->type . '.assets.index')}}">{{__('asset.equipments')}}</a>
                    <a class="list-elem" href="{{route(auth()->user()->type . '.assets.storage')}}">{{__('asset.storage')}}</a>
                    <a class="list-elem" href="{{route(auth()->user()->type . '.assets.waste')}}">{{__('asset.waste')}}</a>
                </div>
            </div>
        </div>
    </div>
@endsection
