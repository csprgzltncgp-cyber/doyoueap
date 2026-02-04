@extends('layout.master')

@section('title')
Admin Dashboard
@endsection

@section('extra_css')
<link rel="stylesheet" href="/assets/css/cases/list_in_progress.css?t={{time()}}">
<style>
  .case-list-in-progress-country{
      padding: 20px 20px;
      background:rgb(222,240,241);
      margin-bottom:10px;
      cursor: pointer;;
  }

  .case-list-in-progress-country button{
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    border: 0px solid black;
    border-radius: 0px;
    background: transparent;
    outline: none !important;
  }
</style>
@endsection

@section('extra_js')
<script>
  const no_cases_text = "{{__('common.no-cases')}}";
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9.7.1/dist/sweetalert2.all.min.js"></script>
<script src="/assets/js/cases_list_in_progress.js?t={{time()}}" charset="utf-8"></script>
@endsection

@section('content')
<div class="row">
  <div class="col-12">
    <h1>{{ __('common.cases-in-progress') }}</h1>
  </div>
  <div class="col-12 case-list-holder">
    @foreach($countries as $country)
      <div class="case-list-in-progress-country col-12 group @if(request()->country_id == $country->id) active @endif" onClick="toggleCases({{$country->id}}, this)">
        {{$country->code}}
        @if(request()->country_id == $country->id)
          <button  class="caret-left float-right">
              <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7" />
              </svg>
          </button>
        @else
          <button  class="caret-left float-right">
              <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
              </svg>
          </button>
        @endif
      </div>
      <div class="cases-list" id="country_{{$country->id}}"></div>
      <button class="load-more-cases d-none" id="country_{{$country->id}}" onClick="loadMore({{$country->id}}, this)">{{ __('common.load-more') }}</button>
    @endforeach
  </div>
</div>
@endsection
