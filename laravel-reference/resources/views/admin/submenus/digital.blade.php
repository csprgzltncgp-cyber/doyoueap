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
            {{ Breadcrumbs::render('submenu.digital') }}
            <h1>{{__('common.submenu.digital')}}</h1>
        </div>
        <div class="col-12 mb-5 pr-0">
            <div class="d-flex flex-column">
                <div class="list-holder">
                    @if(Auth::user()->type == 'admin')
                        <a class="list-elem" href="{{route('admin.business-breakfast.index')}}">Business Breakfast</a>
                        <a class="list-elem" href="{{route('admin.company-website.actions')}}">{{__('company-website.menu')}}</a>
                        <a class="list-elem" href="{{route('admin.eap-online.actions')}}">EAP online</a>
                        <a class="list-elem" href="{{route('admin.prizegame.actions')}}">{{__('prizegame.menu')}}</a>
                        <a class="list-elem" href="{{route('admin.psychosocial-risk-assessment.list')}}">{{__('common.psychosocial_risk_assessment')}}</a>
                        {{-- @if(auth()->user()->super_user) --}}
                            <a class="list-elem" href="{{route('admin.data.index')}}">{{__('data.menu')}}</a>
                        {{-- @endif --}}
                    @endif

                    @if(Auth::user()->type == 'eap_admin')
                        <a class="list-elem" href="{{route('eap_admin.eap-online.actions')}}">EAP online</a>
                    @endif

                    @if(Auth::user()->type == 'account_admin')
                        <a class="list-elem" href="{{route('account_admin.business-breakfast.index')}}">Business Breakfast</a>
                        <a class="list-elem" href="{{route('account_admin.eap-online.actions')}}">EAP online</a>
                        <a class="list-elem" href="{{route('account_admin.prizegame.actions')}}">{{__('prizegame.menu')}}</a>
                        <a class="list-elem" href="{{route('admin.data.index')}}">{{__('data.menu')}}</a>
                    @endif

                    @if(Auth::user()->type == 'production_admin')
                        <a class="list-elem" href="{{route('production_admin.eap-online.actions')}}">EAP online</a>
                        <a class="list-elem" href="{{route('production_admin.company-website.actions')}}">{{__('company-website.menu')}}</a>
                    @endif

                    @if(Auth::user()->type == 'production_translating_admin')
                        <a class="list-elem" href="{{route('production_translating_admin.eap-online.actions')}}">EAP online</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
