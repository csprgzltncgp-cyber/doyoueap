@extends('layout.master')

@section('extra_css')
    <link rel="stylesheet" href="/assets/css/form.css?v={{time()}}">
@endsection

@section('content')
    <div class="container">
        <div class="row justify-content-center align-items-center text-center" style="height: 70vh;">
            <div class="col-md-6 col-md-offset-2">
                <h1>{{__('common.google2fa.setup_title')}}</h1>
                <hr>

                <div style="margin-bottom: 30px;">
                    <p>{{__('common.google2fa.setup.step_1')}}</p>
                    <img src="{{asset('assets/img/google2fa_icon.svg')}}" style="height: 75px; width: 75px;"
                         alt="google 2fa icon">
                </div>

                <div style="margin-bottom: 30px;">
                    <p style="margin-bottom: 0">{{__('common.google2fa.setup.step_2')}}</p>
                    <p style="font-family: CalibriI; font-weight: normal;">{{__('common.google2fa.setup.step_2_desc')}}</p>
                </div>
                <div style="margin-bottom: 30px;">
                    <h4> {{ $secret }}</h4>
                    <div>
                        <img style="width: 250px; height: 250px;"
                             src="data:image/png;base64, <?php echo $qr_image; ?> "/>
                    </div>
                </div>

                <p>{{__('common.google2fa.setup.step_3')}}</p>

                <form action="{{route(auth()->user()->type . '.google2fa.create')}}" method="post"
                      style="max-width: 100%" class="d-flex justify-content-center">
                    @csrf
                    <input type="hidden" value="{{$secret}}" name="secret">
                    <button type="submit" class="text-center btn-radius">{{__('expert-data.next')}}</button>
                </form>
            </div>
        </div>
@endsection
