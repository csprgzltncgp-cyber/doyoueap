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
            {{ Breadcrumbs::render('eap-online.video-therapy') }}
            <h1>{{__('eap-online.video_therapy.menu')}}</h1>
        </div>
        <div class="col-12 ">
            <div class="d-flex flex-column">
                <div class="list-holder">
                    <a class="list-elem"
                       href="{{route('admin.eap-online.video_therapy.actions.psychology.timetable')}}">{{__('eap-online.video_therapy.video_chat_appointments')}}</a>
                    <a class="list-elem"
                       href="{{route('admin.eap-online.connect_countries_to_languages.view')}}">{{__('eap-online.video_therapy.video_chat_experts')}}</a>
                    <a class="list-elem"
                       href="{{route('admin.eap-online.video_therapy.actions.permissions.view')}}">{{__('eap-online.video_therapy.permissions')}}</a>
                    <a class="list-elem"
                       href="{{route('admin.eap-online.video_therapy.actions.expert_day_off.timetable')}}">{{__('eap-online.video_therapy.expert_day_off')}}</a>
                </div>
            </div>
            <div class="row col-4 col-lg-2 back-button mt-5">
                <a href="{{ route('admin.eap-online.actions') }}">{{__('common.back-to-list')}}</a>
            </div>
        </div>
    </div>
@endsection
