@extends('layout.master')

@section('title')
    Admin Dashboard
@endsection

@section('extra_css')
    <link rel="stylesheet" href="/assets/css/form.css?v={{time()}}">
    <link rel="stylesheet" href="/assets/css/cases/view.css?t={{time()}}">
    <link rel="stylesheet" href="/assets/css/list.css?v={{time()}}">
    <link rel="stylesheet" href="/assets/css/workshops.css?v={{time()}}">
    <link rel="stylesheet" href="/assets/css/eap-online/master.css?v={{time()}}">
    <link href="/assets/css/chosen.css" rel="stylesheet" type="text/css">
@endsection

@section('extra_js')
    <script src="/assets/js/chosen.js" type="text/javascript" charset="utf-8"></script>
    <script>
        $(document).ready(function () {
            $(".countries").chosen();
        });
    </script>
@endsection

@section('content')
    <div class="col-12">
        {{ Breadcrumbs::render('eap-online.video-therapy.connect_countries_to_languages') }}
        <h1>{{__('eap-online.video_therapy.video_chat_experts')}}</h1>
        <form class="col-12 row d-flex flex-column"
              action="{{route('admin.eap-online.connect_countries_to_languages.store')}}"
              method="post">
            {{csrf_field()}}
            @foreach($eap_languages as $language)
                <div class="form-group @if(!$loop->last) mb-5 @endif">
                    <p class="col-3 row">{{$language->name}}:</p>
                    <select data-placeholder="{{__('eap-online.video_therapy.choose-a-country')}}" name="countries[{{$language->id}}][]" class="col-6 chosen-select countries" multiple>
                        @foreach($countries as $country)
                            <option @if($language->countries()->where('country_id', $country->id)->exists()) selected
                                    @endif value="{{$country->id}}">{{$country->name}}</option>
                        @endforeach
                    </select>
                    <div class="col-2 row">
                        <button class="text-center btn-radius" type="submit">
                            <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px" class="mr-1 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                            </svg>
                            {{__('common.save')}}</button>
                    </div>
                </div>
            @endforeach
        </form>
    </div>
    <div class="row col-4 col-lg-2 back-button my-5">
        <a href="{{ route('admin.eap-online.video_therapy.actions') }}">{{__('common.back-to-list')}}</a>
    </div>
@endsection
