@extends('errors::layout')

@section('title', __('Not Found'))
@section('message')
    <img style="width: 500px; height: 500px;" src="{{asset('assets/img/errors/419.svg')}}" alt="404">
@endsection
@section('message', __('Not Found'))
