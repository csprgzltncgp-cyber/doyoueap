@extends('layout.master')

@section('title')
    Expert Dashboard
@endsection

@section('extra_css')
    <link rel="stylesheet" href="{{asset('assets/css/cases/list.css')}}?v={{time()}}">
    <link rel="stylesheet" href="{{asset('assets/css/crisis.css')}}?v={{time()}}">
@endsection
@section('content')
    <div class="row">
        <div class="col-12 case-list-holder">
            @foreach($live_webinars as $live_webinar)
                <div class="list-element col-12 crisis-admin-component">
                    <span class="data mr-0">
                        #{{ $live_webinar->activity_id }} - 
                        {{ \App\Models\EapOnline\EapLanguage::query()->find($live_webinar->language_id)?->name }} - 
                        {{ $live_webinar->permission->translation->value }} - 
                        {{ $live_webinar->expert->name }} -
                        {{ $live_webinar->from->format('Y-m-d H:i') }}
                    </span>
                    <a class="edit-crisis btn-radius" style="--btn-margin-left: var(--btn-margin-x)"
                        href="{{route('expert.live-webinar.show', $live_webinar)}}">
                        <img src="{{asset('assets/img/select.svg')}}" class="mr-1" style="height: 20px; width: 20px" alt="">
                        {{__('crisis.select_button')}}
                    </a>
                </div>
            @endforeach
        </div>
    </div>
@endsection