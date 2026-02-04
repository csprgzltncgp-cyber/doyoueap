@extends('layout.master')

@section('title')
Expert Dashboard
@endsection

@section('content')
<h1>{{$document->name}}</h1>
{!! $document->text !!}
@endsection
