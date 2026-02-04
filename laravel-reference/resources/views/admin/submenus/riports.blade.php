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
            {{ Breadcrumbs::render('submenu.riports') }}
            <h1>{{__('common.submenu.riports')}}</h1>
        </div>
        <div class="col-12 mb-5">
            <div class="d-flex flex-column">
                <h1></h1>
                <div class="list-holder">
                    @if(Auth::user()->type == 'admin')
                        <a class="list-elem" href="{{route('admin.riports.index')}}">{{__('common.report_generation')}}</a>
                        <a class="list-elem" href="{{route('admin.customer_satisfaction.index')}}">{{__('common.satisfaction_indices')}}</a>
                        <a class="list-elem" href="{{route('admin.eap-online.riports.create')}}">EAP online riport</a>
                    @endif

                    @if(Auth::user()->type == 'account_admin')
                        <a class="list-elem" href="{{route('account_admin.riports.index')}}">{{__('common.report_generation')}}</a>
                        <a class="list-elem" href="{{route('account_admin.eap-online.riports.create')}}">EAP online riport</a>
                        <a class="list-elem" href="{{route('account_admin.customer_satisfaction.index')}}">{{__('common.satisfaction_indices')}}</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
