@extends('layout.master')

@section('title')
Expert Dashboard
@endsection

@section('extra_js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9.7.1/dist/sweetalert2.all.min.js"></script>
  @if(session('status') === 0)
    <script>
      $(function(){
        Swal.fire({
          title: '{{__('common.new-password-success')}}',
          icon: 'success',
          showCancelButton: false,
          confirmButtonText: 'OK'
        }).then((result) => {
          if (result.value) {
            window.location.replace("/expert/dashboard");
          }
        })
      });
    </script>
  @endif
@endsection

@section('extra_css')
<link rel="stylesheet" href="/assets/css/form.css?v={{time()}}">
@endsection

@section('content')
<div class="row">
  <div class="col-12">
    <h1>{{__('common.change-password')}}</h1>
    <form method="POST" autocomplete="off" >
      <div class="">
          @if($errors->has('password'))
            <span class="validation-error">
                <div class="d-flex flex-column mb-1">
                  {!! trans('common.force-change-password.validation') !!}
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
          @endif
      </div>
      {{csrf_field()}}
      <input type="password" name="password" required placeholder="{{__('common.password')}}" autocomplete="off"/>
      <input type="password" name="password_confirmation" required placeholder="{{__('common.password-again')}}" autocomplete="off"/>
      <button type="submit" class="button btn-radius float-right d-flex align-items-center"
        style="--btn-margin-right: 0px;">
        <img src="{{asset('assets/img/save.svg')}}" class="mr-1" style="height: 20px; width: 20px" alt="">
        {{__('common.save')}}
    </button>
    </form>
  </div>
</div>
@endsection
