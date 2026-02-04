@extends('layout.master')

@section('title')
Expert Dashboard
@endsection

@section('extra_js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9.7.1/dist/sweetalert2.all.min.js"></script>
<script src="/assets/js/invoice/master.js" charset="utf-8"></script>
@endsection

@section('extra_css')
<link rel="stylesheet" href="/assets/css/list.css?v={{time()}}">
<link rel="stylesheet" href="/assets/css/invoices.css?v={{time()}}">
@endsection

@section('content')
<div class="row m-0 w-100">
  <h1 class="col-12 pl-0">{{__('common.invoices')}}</h1>
  <x-invoices.submenu />
</div>
@endsection
