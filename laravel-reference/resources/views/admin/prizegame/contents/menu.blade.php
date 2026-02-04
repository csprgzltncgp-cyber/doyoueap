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
            {{ Breadcrumbs::render('prizegame.pages') }}
            <h1>{{__('prizegame.pages.menu')}}</h1>
        </div>
        <div class="col-12 mb-5">
            <div class="d-flex flex-column">
                <div class="list-holder">
                    <a class="list-elem" href="{{route('admin.prizegame.pages.list', ['list' => 'template'])}}">{{__('prizegame.pages.templates')}}</a>
                    <a class="list-elem" href="{{route('admin.prizegame.pages.list', ['list' => 'assigned'])}}">{{__('prizegame.pages.assigned')}}</a>
                </div>
            </div>
        </div>
    </div>
@endsection
