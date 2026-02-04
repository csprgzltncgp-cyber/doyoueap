@extends('layout.guest')

@section('extra_css')
    <link rel="stylesheet" href="/assets/css/form.css?v={{time()}}">
@endsection

@section('content')
    <div class="container">
        <div class="row justify-content-center align-items-center text-center" style="height: 70vh;">
            <div class="col-md-6 col-md-offset-2">
                <h1>Case #{{$code->case->case_identifier}}</h1>
                <hr>
                <form method="POST" class="d-flex flex-column" style="max-width: 100%" action="{{ route('telus-case.download', ['code' => $code]) }}">
                    {{ csrf_field() }}

                    @if($errors->any())
                        <p style="color: red">The selected code is invalid.</p>
                    @else
                        @if(session()->has('success'))
                            <p style="color: green">{{session()->get('success')}}</p>
                        @else
                            <p>Please enter the download code!</p>
                        @endif
                    @endif



                    <label>
                        <input id="code" type="text" name="code" placeholder="Enter Download Code" required autofocus>
                    </label>

                    <div class="d-flex justify-content-center">
                        <button type="submit" class="text-center btn-radius" style="--btn-margin-right: 0px;">
                            Download
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
