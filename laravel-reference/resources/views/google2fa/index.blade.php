@extends('layout.master')

@section('extra_css')
    <link rel="stylesheet" href="/assets/css/form.css?v={{time()}}">
@endsection

@section('content')
    <div class="container">
        <div class="row justify-content-center align-items-center text-center" style="height: 70vh;">
            <div class="col-md-6 col-md-offset-2">
                <h1>{{__('common.google2fa.title')}}</h1>
                <hr>
                <form method="POST" class="d-flex flex-column" style="max-width: 100%"
                      action="{{ route( auth()->user()->type . '.google2fa.process') }}">
                    {{ csrf_field() }}
                    @if($errors->any())
                        <p style="color: red">{{__($errors->first())}}</p>
                    @else
                        <p>{{__('common.google2fa.index_title')}}</p>
                    @endif

                    <label>
                        <input id="one_time_password" type="number" name="one_time_password" required autofocus>
                    </label>

                    <div class="d-flex justify-content-center">
                        <button type="submit" class="text-center btn-radius" style="--btn-margin-right: 0px;">
                            {{__('common.login')}}
                        </button>
                    </div>

                    @if(session()->has('2fa_setup'))
                        <a class="mt-4"
                           href="{{route(auth()->user()->type . '.google2fa.back')}}">{{__('common.back')}}</a>
                    @endif
                </form>
            </div>
        </div>
    </div>
@endsection
