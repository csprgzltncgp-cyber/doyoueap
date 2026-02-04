@extends('layout.master')

@section('title')
    Admin Dashboard
@endsection

@section('extra_js')
    <script>
        const from_date = "{{\Carbon\Carbon::parse($from)->format('Y-m-d')}}";
        const to_date = "{{\Carbon\Carbon::parse($to)->format('Y-m-d')}}";
    </script>
    <script src="/assets/js/riports/master.js?t={{time()}}" charset="utf-8"></script>
@endsection

@section('extra_css')
    <link rel="stylesheet" href="/assets/css/riports/riports.css?v={{time()}}">
@endsection

@section('content')
    <div class="row m-0">
        {{ Breadcrumbs::render('client-riports.create') }}
        <h1 class="col-12 pl-0">{{__('common.riport-data')}} {{\Carbon\Carbon::parse($from)->format('Y')}}
            . {{__('common.month-name_'.\Carbon\Carbon::parse($from)->format('m'))}} hónapra
            vonatkozóan</h1>
        @if(\Carbon\Carbon::now()->gt(\Carbon\Carbon::parse($to)))
            <div class="riports-actions w-100">
                <button id="activate-riports" class="mr-1" disabled onClick="activateSelectedRiports()">Riportok
                    aktiválása
                </button>
                <button id="deactivate-riports" disabled onClick="deactivateSelectedRiports()">Riportok deaktiválása
                </button>
                <button id="select-riports" class="float-right ml-2">Kijelölés</button>
                <button id="select-all-riports" class="float-right">Összes kijelölése</button>
            </div>
        @endif
        <div class="company-riports-holder">
            @foreach($companies as $company)
                <div class="company-riport d-flex flex-column" id="company_{{$company->id}}" data-id="{{$company->id}}">
                    <div class="company-riport-head">
                        <h2>
                            @if(optional($company->riports->where('from' , $from)->first())->is_active ?? false)
                                <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px" fill="none"
                                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            @endif
                            {{$company->name}}
                        </h2>
                    </div>

                    @foreach($company->countries as $country)
                        <p><span style="text-decoration: underline">{{$country->code}}:</span>
                            @if(count($company->case_numbers) > 0)
                                <span class="count">
                                <br>
                                <span>{{__('common.ongoing_cases')}}: {{$company->case_numbers[$country->id]['case_numbers']['in_progress']}}</span>
                                <br>
                                <span>{{__('common.closed-cases')}}: {{$company->case_numbers[$country->id]['case_numbers']['closed']}}</span>
                                <br>
                                <span>{{__('common.interrupted_cases')}}: {{$company->case_numbers[$country->id]['case_numbers']['interrupted']}}</span>
                                <br>
                                <span>{{__('common.client_unreachable_cases')}}: {{$company->case_numbers[$country->id]['case_numbers']['client_unreachable']}}</span>
                            </span>
                            @endif
                        </p>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>
@endsection
