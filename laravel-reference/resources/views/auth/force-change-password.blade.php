@extends('layout.master_login')

@section('title')
    @if(Auth::user()->type == 'expert')
        Expert Dashboard
    @else
        Operator Dashboard
    @endif
@endsection

@section('extra_css')
    <link rel="stylesheet" href="/assets/css/reset_password.css">
    <link rel="stylesheet" href="/assets/css/cases/list.css?v={{time()}}">
    <style>
        .not-accepted {
            background-color: #E9811B !important;
            padding: 20px 20px;
            font-weight: bold;
            color: white;
        }
    </style>
@endsection

@section('content')
    <div class="d-flex flex-column w-100 align-items-center">
        <form method="post" action="{{route(auth()->user()->type . '.force-change-password-process')}}">
            <div class="">
                @if($errors->has('password'))
                    <span class="validation-error">
                        <div class="d-flex flex-column mb-1">
                            {{ trans('common.force-change-password.validation') }}
                        </div>
                    </span>
                @elseif($errors->has('password_mismatch'))
                    <span class="validation-error">
                        <div class="d-flex flex-column mb-1">
                            {{ $errors->first() }}
                        </div>
                    </span>
                @elseif($errors->has('old_password'))
                    <span class="validation-error">
                        <div class="d-flex flex-column mb-1">
                            {{ $errors->first() }}
                        </div>
                    </span>
                @else
                    <p class="not-accepted">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="height:20px; width:20px" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg> {{__('common.force-change-password.title')}}
                    </p>
                @endif
            </div>
            {{csrf_field()}}
            <input type="password" name="password" style="margin-bottom: 12px;" placeholder="{{trans('common.force-change-password.password')}}">
            <input type="password" name="password_confirmation"
                   placeholder="{{trans('common.force-change-password.password-confirmation')}}">
            <input type="hidden" name="redirect_url" value="{{url()->previous()}}">
            <div class="d-flex justify-content-center">
                <button class="mb-3 text-uppercase d-flex align-items-center btn-radius">
                    <div class="d-flex justify-content-center align-items-center w-100 text-uppercase">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="width: 20px; height: 20px" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                        </svg>
                        {{__('common.save')}}
                    </div>
                </button>
            </div>
        </form>
    </div>
@endsection
