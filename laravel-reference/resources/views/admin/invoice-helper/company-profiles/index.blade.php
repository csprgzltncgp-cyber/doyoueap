@extends('layout.master')

@section('title', 'Admin Dashboard')

@section('extra_css')
    <link rel="stylesheet" href="{{ asset('/assets/css/invoice-helper/invoicing.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/invoice-helper/cgp-card.css') }}">
    <link rel="stylesheet" href="{{asset('assets/css/list.css')}}?v={{time()}}">
    <link rel="stylesheet" href="{{asset('assets/css/invoices.css')}}?v={{time()}}">
    <link rel="stylesheet" href="/assets/css/form.css?v={{time()}}">
    <link rel="stylesheet" href="/assets/css/perfix-input.css?v={{time()}}">
    <style>
        .lis-element-div {
            width: 100%;
            display: none;
        }

        .lis-element-div.active {
            display: block !important;
        }

        .lis-element-div-c {
            display: none;
        }

        .lis-element-div-c.active {
            display: block !important;
        }

        .list-element.col-12 {
            display: inline-block !important;
        }
    </style>
@endsection

@section('content')
<div>
    <h1>
        {{__('invoice-helper.invoice-helper')}}
    </h1>

    <x-invoice-helper.menu />

    @livewire('admin.invoice-helper.company-profiles.index')
</div>
@endsection
