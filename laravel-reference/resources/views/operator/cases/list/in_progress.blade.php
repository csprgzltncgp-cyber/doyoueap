@extends('layout.master')

@section('title')
Operator Dashboard
@endsection

@section('extra_css')
<link rel="stylesheet" href="/assets/css/cases/list_in_progress.css?t={{time()}}">
@endsection

@section('content')
<div class="row mt-5">
  <div class="col-12">
    <h1>{{__('common.cases-in-progress')}}</h1>
  </div>
  <div class="col-12 case-list-holder">
      @foreach($cases as $case)
        @component('components.cases.list_in_progress',['case' => $case])@endcomponent
      @endforeach
  </div>
</div>
@endsection
