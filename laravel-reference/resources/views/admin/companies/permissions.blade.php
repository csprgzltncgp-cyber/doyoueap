@extends('layout.master')

@section('extra_css')
    <link rel="stylesheet" href="/assets/css/list.css?v={{time()}}">
    <link rel="stylesheet" href="{{ asset('/assets/css/invoice-helper/invoicing.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/invoice-helper/cgp-card.css') }}">
@endsection

@section('title')
    Admin Dashboard
@endsection

@section('content')
    <div class="row m-0">
        {{ Breadcrumbs::render('permissions') }}
        <h1 class="col-12 pl-0">{{__('common.list_of_permissions')}}</h1>
        @livewire('admin.permission.index')
    </div>
@endsection
