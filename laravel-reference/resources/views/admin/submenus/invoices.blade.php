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
            {{ Breadcrumbs::render('submenu.invoices') }}
            <h1>{{__('common.submenu.invoices')}}</h1>
        </div>
        <div class="col-12 mb-5">
            <div class="d-flex flex-column">
                <div class="list-holder">
                    @if(Auth::user()->type == 'admin')
                        <a class="list-elem" href="{{route('admin.invoices.index')}}">{{__('common.incoming-invoices')}}</a>
                        <a class="list-elem" href="{{route('admin.invoice-helper.direct-invoicing.index')}}">{{__('common.direct-invoices')}}</a>
                    @endif

                    @if(Auth::user()->type == 'financial_admin')
                        <a class="list-elem" href="{{route('financial_admin.invoices.index')}}">{{__('common.incoming-invoices')}}</a>
                        <a class="list-elem" href="{{route('financial_admin.invoice-helper.direct-invoicing.index')}}">{{__('common.direct-invoices')}}</a>
                    @endif

                    @if(Auth::user()->type == 'account_admin')
                        <a class="list-elem" href="{{route('account_admin.invoice-helper.direct-invoicing.index')}}">{{__('common.direct-invoices')}}</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
