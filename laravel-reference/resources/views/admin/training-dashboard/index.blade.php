@extends('layout.master')

@section('title')
    {{ucwords(auth()->user()->type)}} Dashboard
@endsection

@section('extra_css')
    <link rel="stylesheet" href="{{asset('assets/css/form.css')}}?v={{time()}}">
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <h1>Trainig Dashboard {{__('common.submenu.settings')}}</h1>
            <form method="POST" action="{{route('admin.training-dashboard.generate_new_password')}}">
                @csrf
                <button type="submit" class="btn-radius" style="--btn-max-width: 200px;">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 mb-1" style="height:20px; width:20px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    {{__("common.password-reset-button")}}
                </button>
            </form>
            @if(session()->has('training_dashboard_password_generated'))
                <div class="mt-3">
                    {{__('common.password')}}: {{session()->get('training_dashboard_password_generated')['new_password']}}
                    <br>
                    {{__('common.expires_at')}}: {{session()->get('training_dashboard_password_generated')['expires_at']}}
                </div>
            @endif
        </div>
    </div>
@endsection
