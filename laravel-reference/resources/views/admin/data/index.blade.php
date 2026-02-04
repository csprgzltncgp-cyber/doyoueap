@extends('layout.master')

@section('title')
    Admin Dashboard
@endsection

@section('extra_css')
    <link rel="stylesheet" href="{{asset('assets/css/cases/list.css')}}?v={{time()}}">
    <link rel="stylesheet" href="{{asset('assets/css/workshops.css')}}?v={{time()}}">
@endsection


@section('content')
    <div class="row">
        <div class="col-12 mb-5">
            {{ Breadcrumbs::render('data') }}
        </div>

        <livewire:admin.data.invoiced-consultation />

        <livewire:admin.data.case-data />

        <livewire:admin.data.incoming-outgoing-invoices />

        <!-- Incoming/outgoing invoices -->
        {{-- <div class="col-12 mb-5">
            <h1>
                {{__('data.direct_and_incoming_invoices')}} ( {{\Carbon\Carbon::parse('2023-02-01')->format('Y.m.d')}} - {{$date_intervals['to']}} )
            </h1>
        </div>
        <div class="col-12 mb-5">
            <div class="case-list-in col-12 group" onclick="case_interval('countries-invoices')">
                {{__('data.by_countries')}}
                <button class="caret-left float-right">
                    <svg id="acountries-invoices" xmlns="http://www.w3.org/2000/svg"
                        style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
            </div>
            <div class="lis-element-div" id="countries-invoices">
                @if ($country_case_data)
                    @foreach ($country_case_data as $country => $data)
                        <div class="case-list-in col-12 group" onclick="case_interval('country-invoice-{{Str::replace(' ', '',$country)}}')">
                            {{ ($country == 'all_country') ? 'TOTAL' : $country }}
                            <button class="caret-left float-right">
                                <svg id="acountry-invoice-{{Str::replace(' ', '',$country)}}" xmlns="http://www.w3.org/2000/svg"
                                    style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                        </div>
                        <div class="lis-element-div" id="country-invoice-{{Str::replace(' ', '',$country)}}">
                            @foreach ($data['invoices'] as $category => $values)
                                <div class="case-list-in col-12 group ml-4" onclick="case_interval('status-{{$category}}-{{Str::replace(' ', '',$country)}}')">
                                    {{__('data.'.$category)}}
                                    <button class="caret-left float-right">
                                        <svg id="astatus-{{$category}}-{{Str::replace(' ', '',$country)}}" xmlns="http://www.w3.org/2000/svg"
                                            style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                            stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </button>
                                </div>
                                <div class="lis-element-div" id="status-{{$category}}-{{Str::replace(' ', '',$country)}}">
                                    @foreach ($values as $name => $value)
                                        <div class="case-list-in group ml-5">
                                            {{$name}}: {{number_format(str_replace(' ', '', (string) $value), 0, ',', ' ')}}
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                @else
                    <p class="ml-2">{{__('riport.no_available_data')}}</p>
                @endif
            </div>
        </div> --}}
        <!-- Incoming/outgoing invoices -->

        <livewire:admin.data.consultation-usage />

        <livewire:admin.data.affiliate-numbers />
    </div>
@endsection
