@extends('layout.master')

@section('title')
    Admin Dashboard
@endsection

@section('extra_css')
    <link rel="stylesheet" href="/assets/css/form.css?v={{time()}}">
    <style>
        .error {
            padding: 10px 15px;
            background: rgb(219, 11, 32) !important;
            color: white;
        }
    </style>
@endsection

@section('extra_js')
    <script>
        let upload_alt = '{{__('eap-online.lead_page.image_upload_text')}}';
    </script>
    <script src="/assets/js/eap-online/lead_page.js?v={{time()}}" charset="utf-8"></script>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            {{ Breadcrumbs::render('eap-online.theme-of-the-month') }}
            <h1>{{__('eap-online.theme_of_the_month.menu')}}</h1>
        </div>
        <div class="col-12">
            <form action="{{route('admin.eap-online.theme-of-the-month.store')}}" method="post" class="row col-8"
                  enctype="multipart/form-data">
                @csrf
                <div class="row col-12 d-flex flex-column">
                    <div class="d-flex">
                        <select name="theme_of_the_month_language" class="w-25 mr-3">
                            @foreach($languages as $language)
                                <option
                                        value="{{$language->id}}"
                                        @if($theme_of_the_month_language && $language->id == $theme_of_the_month_language->value) selected @endif
                                >{{$language->name}}</option>
                            @endforeach
                        </select>
                        <input type="text" placeholder="{{__('eap-online.theme_of_the_month.theme_of_the_month_text')}}"
                               name="theme_of_the_month_text"
                               value="{{$theme_of_the_month_text ? $theme_of_the_month_text->value: ''}}">
                    </div>
                </div>
                <div class="row col-12 mt-4 mb-5">
                    <div>
                        <button class="text-center btn-radius" type="submit">
                            <img class="mr-1" style="width:20px;" src="{{asset('assets/img/save.svg')}}" style="height: 20px; width: 20px" alt="">
                            <span class="mt-1">{{__('common.save')}}</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
