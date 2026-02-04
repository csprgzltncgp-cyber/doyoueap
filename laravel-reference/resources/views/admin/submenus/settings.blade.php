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
            {{ Breadcrumbs::render('submenu.settings') }}
            <h1>{{__('eap-online.actions.settings')}}</h1>
        </div>
        <div class="col-12 mb-5">
            <div class="d-flex flex-column">
                <div class="list-holder">
                    @if(Auth::user()->type == 'admin')
                        <a class="list-elem" href="{{route('admin.companies.list')}}">{{__('common.list_of_companies')}}</a>
                        <a class="list-elem" href="{{route('admin.countries.index')}}">{{__('common.list_of_countries')}}</a>
                        <a class="list-elem" href="{{route('admin.cities.list')}}">{{__('common.list_of_cities')}}</a>
                        <a class="list-elem" href="{{route('admin.companies.permissions.list')}}">{{__('common.list_of_permissions')}}</a>
                        <a class="list-elem" href="{{route('admin.admins.list')}}">{{__('common.list_of_admins')}}</a>
                        <a class="list-elem" href="{{route('admin.experts.list')}}">{{__('common.list_of_experts')}}</a>
                        <a class="list-elem" href="{{route('admin.operators.list')}}">{{__('common.list_of_operators')}}</a>
                        <a class="list-elem" href="{{route('admin.documents.list')}}">{{__('common.list_of_documents')}}</a>
                        <a class="list-elem" href="{{route('admin.training-dashboard.index')}}">Training Dashboard</a>
                    @endif

                    @if(Auth::user()->type == 'account_admin')
                        <a class="list-elem" href="{{route('account_admin.companies.list')}}">{{__('common.list_of_companies')}}</a>
                        <a class="list-elem" href="{{route('account_admin.companies.permissions.list')}}">{{__('common.list_of_permissions')}}</a>
                        <a class="list-elem" href="{{route('account_admin.experts.index')}}">{{__('common.list_of_experts')}}</a>
                    @endif

                    @if(Auth::user()->type == 'eap_admin')
                        <a class="list-elem" href="{{route('eap_admin.documents.list')}}">{{__('common.list_of_documents')}}</a>
                        <a class="list-elem" href="{{route('admin.experts.list')}}">{{__('common.list_of_experts')}}</a>
                        <a class="list-elem" href="{{route('admin.operators.list')}}">{{__('common.list_of_operators')}}</a>
                        <a class="list-elem" href="{{route('admin.countries.index')}}">{{__('common.list_of_countries')}}</a>
                        <a class="list-elem" href="{{route('admin.cities.list')}}">{{__('common.list_of_cities')}}</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
