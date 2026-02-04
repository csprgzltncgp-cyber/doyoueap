@extends('layout.master_login')

@section('title')
Operator Dashboard
@endsection

@section('extra_css')
<link rel="stylesheet" href="/assets/css/login.css">
@endsection

@section('content')
<form id="login" method="post">
  {{csrf_field()}}
  <input type="password" name="password" value="" placeholder="{{__('common.password')}}" required>
  <input type="password" name="password_confirmation" value="" placeholder="{{__('common.password-again')}}" required>
  <button>{{__('common.set-password')}}</button>
</form>
@endsection
