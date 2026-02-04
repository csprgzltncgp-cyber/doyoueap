@extends('layout.master_login')

@section('title')
Expert Dashboard
@endsection

@section('extra_css')
<link rel="stylesheet" href="/assets/css/login.css">
@endsection

@section('content')
<form id="login" method="post">
  {{csrf_field()}}
  <input type="password" name="password" value="" placeholder="Jelszó" required>
  <input type="password" name="password_confirmation" value="" placeholder="Jelszó mégegyszer" required>
  <button>Jelszó beállítása</button>
</form>
@endsection
