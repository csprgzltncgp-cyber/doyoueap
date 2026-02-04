@extends('layout.master')

@section('title')
    Admin Dashboard
@endsection

@section('extra_css')
    <style>
        .list-elem {
            padding: 20px 40px;
            background: rgb(0, 87, 95);
            color: white;
            text-transform: uppercase;
        }

        .list-elem:hover {
            color: white;
        }

        .list-holder {
            display: grid;
            grid-template-columns: 2fr 2fr 2fr;
            grid-gap: 20px;
        }

        .list-elem {
            text-align: center;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-12 mb-5">
            {{ Breadcrumbs::render('prizegame') }}
            <h1>{{__('prizegame.actions.title')}}</h1>
        </div>
        <div class="col-12 mb-5">
            <div class="d-flex flex-column">
                <h1>{{__('eap-online.actions.settings')}}</h1>
                <div class="list-holder">
                    <a class="list-elem"
                       href="{{route('admin.prizegame.languages.index')}}">{{__('eap-online.actions.language')}}</a>
                    <a class="list-elem"
                       href="{{route('admin.prizegame.types.index')}}">{{__('prizegame.types.menu')}}</a>
                    <a class="list-elem"
                       href="{{route('admin.prizegame.pages.index')}}">{{__('prizegame.pages.menu')}}</a>
                    <a class="list-elem"
                       href="{{route('admin.prizegame.games.index')}}">{{__('prizegame.games.running_menu')}}</a>
                    <a class="list-elem"
                       href="{{route('admin.prizegame.games.archived')}}">{{__('prizegame.games.archived_menu')}}</a>
                </div>
            </div>
            <div class="d-flex flex-column">
                <h1>{{__('eap-online.actions.translation')}}</h1>
                <div class="list-holder">
                    <a class="list-elem"
                       href="{{route('admin.prizegame.translation.system.index')}}">{{__('eap-online.actions.system')}}</a>
                    <a class="list-elem"
                       href="{{route('admin.prizegame.translation.pages.index')}}">{{__('prizegame.pages.menu')}}</a>
                </div>
            </div>
        </div>
    </div>
@endsection
