@extends('layout.master')

@section('title')
    Admin Dashboard
@endsection

@section('extra_css')
    <link rel="stylesheet" href="/assets/css/list.css?v={{time()}}">
    <link rel="stylesheet" href="/assets/css/crisis.css?v={{time()}}">
@endsection

@section('content')
    <div class="row m-0 w-100">
        {{ Breadcrumbs::render('crisis.filtered') }}
        <h1 class="col-12 pl-0">{{__('crisis.filter_result')}}</h1>
        <ul id="crisis-submenus" class="w-100">
            <li><a class="col-12 pl-0 d-block add-new-crisis" href="{{route('admin.crisis.filter')}}"><svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="height:20px; width:20px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
</svg>{{__('crisis.apply_filter')}}</a></li>
            <li><a class="col-12 pl-0 d-block add-new-crisis" href="{{route('admin.crisis.list')}}"><svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="width:20px; height:20px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
</svg>{{__('crisis.crisis_list')}}</a></li>
        </ul>

        @if(!$crisis_cases->count())
            <p>{{__('crisis.no_filter_result')}}</p>
        @endif
        @foreach($crisis_cases as $crisis_case)
            @component('components.crisises.crisis_case_component',
            [
              'crisis_case' => $crisis_case,
            ])@endcomponent
        @endforeach
    </div>
@endsection
