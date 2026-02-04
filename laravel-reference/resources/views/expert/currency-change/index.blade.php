@extends('layout.master')

@section('title')
Expert Dashboard
@endsection

@section('extra_css')
<link rel="stylesheet" href="{{asset('assets/css/cases/list.css')}}?v={{time()}}">
<link rel="stylesheet" href="{{asset('assets/css/workshops.css')}}?v={{time()}}">
<link rel="stylesheet" href="{{asset('assets/css/cases/pulse.css')}}?v={{time()}}">
<style>
    .row-button {
        background-color: rgb(0,87,95);
        color: white;
        font-weight: bold;
        display: block;
        appearance: none;
        -moz-appearance: none;
        -webkit-appearance: none;
        padding: 20px 32px;
        width: 192px;
        height: 44px;
        border-radius: 10px;
        border: 0px solid black;
        text-align: left;
        outline: none !important;
        font-family: CalibriB;
        font-size: 16px;
    }
</style>
@endsection

@section('extra_js')
<script>
    Livewire.on('errorEvent', function(error){
        Swal.fire({
            title: error,
            text: '',
            icon: 'error',
            confirmButtonText: 'Ok'
        });
    });

    Livewire.on('successEvent', function(message){
        Swal.fire({
            title: message,
            text: '',
            icon: 'success',
            confirmButtonText: 'Ok'
        }).then(function(){
            location.replace("{{route('expert.invoices.main')}}");
        });
    })
</script>
@endsection

@section('content')
<div class="row">
  <div class="col-12">
    <h1>{{__('currency-change.menu')}}</h1>
    @livewire('expert.currency-change.index')

  </div>
</div>
@endsection
