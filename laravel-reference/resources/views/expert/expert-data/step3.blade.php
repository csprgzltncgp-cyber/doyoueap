@extends('layout.master')

@section('extra_css')
<style>
    .button {
        padding: 10px 30px 10px 40px;
        background: rgb(89, 198, 198);
        color: white;
        text-transform: uppercase;
        border: none;
        margin-top: 10px;
        margin-bottom: 20px;
        margin-left: 20px;
    }

    .button:hover{
        color: white;
    }

    #menu, #logged-in-as{
        display: none !important;
    }

    #logo{
        filter: brightness(0) saturate(100%) invert(20%) sepia(46%) saturate(2675%) hue-rotate(159deg) brightness(99%) contrast(103%);
    }

    header{
        background-color: white;
        pointer-events: none;
        cursor: default;
        text-decoration: none;
    }

    header p.text-uppercase{
        pointer-events: none;
        cursor: default;
        text-decoration: none;
        color: #00575f !important;
    }
</style>
@endsection

@section('title') Expert Dashboard @endsection

@section('content')
    <h1>{{__('expert-data.thank-you-for-your-cooperation')}}</h1>

    <div class="w-full d-flex justify-content-center mt-5">
        <a class="button" href="{{route('expert.dashboard')}}" style="">
            {{__('expert-data.contionue-to-the-dashboard')}}
            <svg xmlns="http://www.w3.org/2000/svg" class="ml-1" style="width: 20px; height:20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" />
            </svg>
        </a>
    </div>
@endsection
