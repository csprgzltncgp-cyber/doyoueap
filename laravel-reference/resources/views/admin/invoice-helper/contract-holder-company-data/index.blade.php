@extends('layout.master')

@section('title', 'Admin Dashboard')

@section('extra_js')
    <script>
        function addNewDirectInvoice() {
            Livewire.emit('admin.direct-invoicing.container','createList');
        }
    </script>
@endsection

@section('extra_css')
    <link rel="stylesheet" href="{{ asset('/assets/css/invoice-helper/invoicing.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/invoice-helper/cgp-card.css') }}">
    <link rel="stylesheet" href="{{asset('assets/css/list.css')}}?v={{time()}}">
    <link rel="stylesheet" href="{{asset('assets/css/invoices.css')}}?v={{time()}}">
    <link rel="stylesheet" href="/assets/css/form.css?v={{time()}}">
    <link rel="stylesheet" href="/assets/css/perfix-input.css?v={{time()}}">
    <link rel="stylesheet" href="{{asset('assets/css/bordered-checkbox.css')}}?v={{time()}}">


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

        .chosen-container{
            width: auto !important;
            flex: 1 0 auto !important;
        }

        .form-group input {
            color: black !important;
        }

        .form-group select {
            color: black !important;
        }

        .chosen-container.chosen-container-multi{
            width: min-content !important;
        }
    </style>
@endsection

@section('content')
<div>
    {{ Breadcrumbs::render('invoices.direct-invoices') }}

    <h1>
        {{__('invoice-helper.invoice-helper')}}
    </h1>

    <x-invoice-helper.menu :contractHolderCompany="$contract_holder_company" />

    <form class="mt-0" style="max-width: 1000px !important;" autocomplete="off" novalidate>
        <livewire:admin.direct-invoicing.container
            :company="$company"
            :countryDifferentiates="$countryDifferentiates"
            :includeSaveButtonOnInvoiceData="true"
        >
    </form>

</div>
@endsection
