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
            {{ Breadcrumbs::render('submenu.outsources') }}
            <h1>{{__('common.submenu.outsources')}}</h1>
        </div>
        <div class="col-12 mb-5 pr-0">
            <div class="d-flex flex-column">
                <div class="list-holder">
                    @if(Auth::user()->type == 'admin')
                        <a class="list-elem" href="{{route('admin.workshops.list')}}">{{__('common.workshop_mediation')}}</a>
                        <a class="list-elem" href="{{route('admin.crisis.list')}}">{{__('common.crisis_mediation')}}</a>
                        <a class="list-elem" href="{{route('admin.other-activities.index')}}">{{__('other-activity.menu')}}</a>
                        <a class="list-elem" href="{{route('admin.worksop-feedback.index')}}">{{__('common.workshop_feedback')}}</a>
                    @endif

                    @if(Auth::user()->type == 'account_admin')
                        <a class="list-elem" href="{{route('account_admin.workshops.list')}}">{{__('common.workshop_mediation')}}</a>
                        <a class="list-elem" href="{{route('account_admin.crisis.list')}}">{{__('common.crisis_mediation')}}</a>
                        <a class="list-elem" href="{{route('account_admin.other-activities.index')}}">{{__('other-activity.menu')}}</a>
                        <a class="list-elem" href="{{route('account_admin.worksop-feedback.index')}}">{{__('common.workshop_feedback')}}</a>
                    @endif

                    @if(Auth::user()->type == 'financial_admin')
                        <a class="list-elem" href="{{route('financial_admin.workshops.list')}}">{{__('common.workshop_mediation')}}</a>
                        <a class="list-elem" href="{{route('financial_admin.crisis.list')}}">{{__('common.crisis_mediation')}}</a>
                        <a class="list-elem" href="{{route('financial_admin.other-activities.index')}}">{{__('other-activity.menu')}}</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
