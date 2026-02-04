@extends('layout.master_login')

@section('title')
    Admin Dashboard
@endsection

@section('extra_css')
    <link rel="stylesheet" href="/assets/css/login.css?v={{time()}}">
@endsection

@section('content')
    <div>
        @if($errors->any())
            <p class="not-accepted">
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="height:20px; width:20px" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg> {{$errors->first()}}
            </p>
        @endif
        <form id="login" method="post">
            {{csrf_field()}}
            <input type="text" name="username" value="" style="margin-bottom: 12px;" placeholder="{{__('common.username')}}" required>
            <input type="password" name="password" value="" placeholder="{{__('common.password')}}" required>
            <div class="d-flex justify-content-center">
                <button class="mb-3 text-uppercase d-flex align-items-center">
                    <div class="d-flex justify-content-center align-items-center w-100 text-uppercase">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="width: 20px; height: 20px" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                        </svg>
                        <span class="mt-1">{{__('common.login')}}</span>
                    </div>
                </button>
            </div>
        </form>
        <div class="d-flex align-items-center env-text-cont" style="margin-top: 20px">
            <img src="{{asset('assets/img/white_logo.svg')}}" style="height: 56px;" alt="">
            <p class="text-uppercase"
               style="font-family: CalibriI; font-weight: normal; color: rgb(0,87,95); font-size: 10px;
               margin-left: 7px; margin-top: 17px!important; margin-bottom: 6px;">
               {{__('common.green_text')}}
            </p>
        </div>
    </div>
@endsection
