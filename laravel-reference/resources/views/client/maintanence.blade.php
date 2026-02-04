@extends('layout.master_login')

@section('title')
    Client Dashboard
@endsection

@section('extra_css')
    <link rel="stylesheet" href="/assets/css/login.css">
@endsection

@section('content')
    <div class="d-flex justify-content-center align-items-center flex-column">
        <h1 style="font-size: 3em; color: rgb(89, 198, 198);">{{__('common.client-maintenance')}}</h1>
        <div class="d-flex align-items-center mt-5" style="max-width: 350px">
            <img src="{{asset('assets/img/green_logo.svg')}}" style="height: 70px;" alt="">
            <p class="text-white text-uppercase ml-3 mt-3" style="font-family: CalibriI; font-weight: normal">{{__('common.green_text')}}</p>
        </div>
    </div>
@endsection
