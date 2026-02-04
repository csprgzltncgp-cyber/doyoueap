@extends('layout.master')

@section('title')
    Expert Dashboard
@endsection

@section('extra_css')

    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.css"
          rel="stylesheet"/>
    <link rel="stylesheet" href="{{asset('assets/css/cases/view.css')}}?t={{time()}}">
    <link rel="stylesheet" href="{{asset('assets/css/cases/datetime.css')}}?t={{time()}}">

    <style>
        #content .button-holder .button {
            float: right;
            margin-right: 10px;
        }

        #content .button-holder .button.denie {
            float: right;
            margin-right: 10px;
            background-color: #7c2469;
        }

        li.danger {
            background-color: rgb(219, 11, 32) !important;
            color: #fff !important;
        }

        li.warning {
            background-color: #f2da2f !important;
            color: #fff !important;
        }

        li.danger button {
            color: #fff;
        }
    </style>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <h1>{{__('eap-online.live-webinars.view')}}</h1>
    </div>
    <div class="col-12 case-title">
        <p>{{$live_webinar->from}} - {{$live_webinar->permission->translation->value }}</p>
    </div>
    <div class="col-12 case-details">
        <ul>
            <li>
                <button> {{__('eap-online.live-webinars.activity_id')}}: <span
                            id="case-status"> #{{ $live_webinar->activity_id }}</span></button>
            </li>
            <li>
                <button> {{__('eap-online.live-webinars.companies')}}: <span
                            id="case-status"> {{ implode(', ', $live_webinar->companies->pluck('name')->toArray()) }}</span></button>
            </li>
            <li>
                <button> {{__('eap-online.live-webinars.countries')}}: <span
                            id="case-status"> {{ implode(', ', $live_webinar->countries()->withoutGlobalScopes()->get()->pluck('name')->toArray()) }}</span></button>
            </li>
            <li>
                <button> {{__('eap-online.live-webinars.language')}}: <span
                            id="case-status"> {{ $eap_language->name }}</span></button>
            </li>
            <li>
                <button> {{__('eap-online.live-webinars.topic')}}: <span
                            id="case-status"> {{ $live_webinar->topic }}</span></button>
            </li>
            <li>
                <button> {{__('eap-online.live-webinars.starts_at')}}: <span
                            id="case-status"> {{ $live_webinar->from->format('Y.m.d H:i') }}</span></button>
            </li>
            <li>
                <button> {{__('eap-online.live-webinars.duration')}}: <span
                            id="case-status"> {{ $live_webinar->duration }}</span></button>
            </li>
            <li>
                <button> {{__('eap-online.live-webinars.description')}}: <span
                            id="case-status"> {{ $live_webinar->description }}</span></button>
            </li>
        </ul>
    </div>

    <div class="col-12 button-holder">
        @if($start_url)
            <a href="{{ $start_url }}"
               class="button btn-radius float-right purple-button d-flex align-items-center"
               target="_blank"
               rel="noopener">
                {{ __('eap-online.live-webinars.start_webinar') }}
            </a>
        @else
            <div class="alert alert-warning">
                {{ __('eap-online.live-webinars.zoom_not_ready') }}
            </div>
        @endif
    </div>
    <div class="col-6 back-button mb-5">
        <a href="{{ route('expert.live-webinar.index') }}">{{__('common.back-to-list')}}</a>
    </div>
</div>
@endsection
