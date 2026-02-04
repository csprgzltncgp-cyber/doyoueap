@extends('layout.master')

@section('title')
    Operator Dashboard
@endsection

@section('extra_css')
    <link rel="stylesheet" href="/assets/css/dashboard.css">
@endsection

@section('variables')
    @php
        $display = 'block';
    @endphp
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="row ml-0">
                <a href="{{route('operator.cases.new')}}" class="btn-radius d-block" style="width:auto;" id="add-new-case">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="height: 20px; width: 20px" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>{{__('common.new-case')}}</span>
                </a>
            </div>
        </div>
    </div>
@endsection
