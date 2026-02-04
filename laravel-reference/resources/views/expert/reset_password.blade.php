@extends('layout.master_login')

@section('title')
    Expert Dashboard
@endsection

@section('extra_css')
    <link rel="stylesheet" href="/assets/css/reset_password.css">
@endsection

@section('content')
    <form method="post" action="{{route('expert.reset-password-process')}}">
        @if($errors->get('email'))
            <span class="validation-error"><svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="width:20px; height:20px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
</svg>{{__('common.password-reset-error')}}</span>
        @endif

        @if(session()->has('message'))
            <span class="validation-success"><svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="width:20px; height:20px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
</svg>{{__('common.password-reset-success')}}</span>
        @endif
        {{csrf_field()}}
        <input type="email" name="email" value="" placeholder="{{__('common.email')}}" required>
        <div class="w-100 d-flex justify-content-center">
            <button class="d-flex justify-content-center text-uppercase btn-radius">
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="width: 20px; height: 20px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z" />
                </svg>
                {{__('common.password-reset-button')}}
            </button>
        </div>
    </form>
@endsection
