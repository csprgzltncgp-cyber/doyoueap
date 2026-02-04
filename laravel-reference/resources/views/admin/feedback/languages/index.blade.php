@extends('layout.master')

@section('title')
    Admin Dashboard
@endsection

@section('extra_css')
    <link rel="stylesheet" href="/assets/css/cases/view.css?t={{time()}}">
    <link rel="stylesheet" href="/assets/css/list.css?v={{time()}}">
    <link rel="stylesheet" href="/assets/css/workshops.css?v={{time()}}">
    <link rel="stylesheet" href="/assets/css/eap-online/master.css?v={{time()}}">
@endsection

@section('content')
    <div class="row">
        <div class="col-12 mb-5">
            {{ Breadcrumbs::render('feedback.languages') }}
            <h1>{{__('eap-online.actions.language')}}</h1>
            <a href="#" data-toggle="modal" data-target="#new_language">{{__('eap-online.languages.new')}}</a><br>
        </div>
        <div class="col-12">
            @foreach($languages as $language)
                <div class="list-element col-12">
                    <span class="data">
                     <span>{{$language->name}}</span> - <span>{{$language->code}}</span>
                    </span>
                </div>
            @endforeach
        </div>
        <div class="col-4 col-lg-2 back-button mb-5">
            <a href="{{ route('admin.feedback.actions') }}">{{__('common.back-to-list')}}</a>
        </div>
    </div>
@endsection

@section('modal')
    <div class="modal" tabindex="-1" id="new_language" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{__('eap-online.languages.new')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post" action="{{route('admin.feedback.languages.store')}}">
                        <select class="w-100" name="language">
                            @foreach($eap_online_languages as $language)
                                <option value="{{$language->id}}">{{$language->name}}</option>
                            @endforeach
                        </select>
                        {{csrf_field()}}
                        <button class="btn-radius" style="float: right; --btn-margin-right: 0px;">
                            <img class="mr-1" src="{{asset('assets/img/save.svg')}}" style="height: 20px; width: 20px" alt="">
                            <span class="mt-1">{{__('common.save')}}</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
