@extends('layout.master')

@section('title')
    Operator Dashboard
@endsection

@section('extra_css')
    <link rel="stylesheet" href="/assets/css/cases/list_in_progress.css?t={{time()}}">
    <style>
        a#filter {
            color: white;
            font-weight: bold;
            background-color: rgb(89, 198, 198);
            border-radius: 0px;
            text-transform: uppercase;
            padding: 10px 40px;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            border: 2px solid transparent;
            outline: none !important;
        }
    </style>
@endsection

@section('content')
    <div class="row mt-5">
        <div class="col-12 mb-3">
            <h1>{{__('eap-online.mails.menu')}}</h1>
            <a href="{{route('operator.eap-online.mails.filter.view')}}" id="filter" class="mb-4 mt-3 btn-radius"
            style="--btn-max-width: var(--btn-min-width)">
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="height:20px; width:20px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
                {{__('workshop.apply_filter')}}
            </a>
        </div>
        <div class="col-12 case-list-holder">
            @if(!empty($mails) && count($mails) > 0)
                @foreach($mails as $mail)
                    @component ('components.eap-online.mail_line_component',['mail' => $mail, 'page' => $mails->currentPage()]) @endcomponent
                @endforeach
            @else
                <p>{{__('eap-online.mails.no_mails')}}</p>
            @endif
        </div>
    </div>
    {{$mails->links()}}
@endsection
