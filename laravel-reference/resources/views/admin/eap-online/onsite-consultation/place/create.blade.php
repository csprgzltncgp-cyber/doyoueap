@extends('layout.master')

@section('title')
    Admin Dashboard
@endsection

@section('extra_css')
    <link rel="stylesheet" href="/assets/css/list.css?v={{time()}}">
    <link rel="stylesheet" href="/assets/css/form.css?v={{time()}}">
    <link rel="stylesheet" href="/assets/css/cases/view.css?t=<?php echo e(time()); ?>">
    <link rel="stylesheet" href="/assets/css/eap-online/articles.css?v={{time()}}">

    <style>
        .list-elem {
            background: rgb(222, 240, 241);
            color: black;
            text-transform: uppercase;
            margin-right: 10px;
            min-width: 200px;
        }

        .list-elem:hover {
            color: black;
        }

        .list-element button, .list-element a {
            margin-right: 10px;
            display: inline-block;
        }

        .list-element button.delete-button {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background: transparent;
            border: 0px solid black;
            color: #007bff;
            outline: none;
        }

        .list-element {
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

    </style>
@endsection

@section('extra_js')
    
@endsection

@section('content')
    <div class="row">
        <div class="row col-12">
            <div class="col-12">
                {{ Breadcrumbs::render('eap-online.onsite-consultation.place.create') }}
                <h1>{{ __('eap-online.onsite_consultation.new_place') }}</h1>

                <div>
                    <form method="POST" action="{{route('admin.eap-online.onsite-consultation.place.store')}}">
                        {{csrf_field()}}
                        <div class="mb-1 mt-3" style="border: 2px solid rgb(89,198,198) !important;">
                            <div class="d-flex flex-row align-items-center pl-2">
                                <span class="mr-1" style="color: rgb(89, 198, 198);">{{ __('eap-online.onsite_consultation.place_name') }}:</span>
                                <input type="text" name="name" style="margin:0px!important; padding: 10px 0px 10px 0px !important; border:0px!important; color:black!important; background-color:transparent">                
                            </div>
                        </div>
                        <div class="mb-1 mt-3" style="border: 2px solid rgb(89,198,198) !important;">
                            <div class="d-flex flex-row align-items-center pl-2">
                                <span class="mr-1" style="color: rgb(89, 198, 198);">{{ __('eap-online.onsite_consultation.place_address') }}:</span>
                                <input type="text" name="address" style="margin:0px!important; padding: 10px 0px 10px 0px !important; border:0px!important; color:black!important; background-color:transparent">               
                            </div>
                        </div>
                        <button type="submit"
                            style="padding-bottom: 14px; padding-left:10px; text-transform: uppercase;"
                            class="text-center btn-radius mt-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-1"
                                style="height: 20px; width:20px;" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4">
                                </path>
                            </svg>
                            <span class="mt-1">
                                {{ __('common.save') }}
                            </span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
