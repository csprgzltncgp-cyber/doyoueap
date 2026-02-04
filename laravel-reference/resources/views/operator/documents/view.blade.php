@extends('layout.master')

@section('title')
    Operator Dashboard
@endsection

@section('content')
    <div class="d-flex flex-column">
        <h1 style="margin-top:60px">{{$document->name}}</h1>
        {!! $document->text !!}
    </div>
@endsection
