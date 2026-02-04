@extends('layout.master')

@section('title')
    Admin Dashboard
@endsection

@section('extra_css')
    <link rel="stylesheet" href="/assets/css/riports/riports.css?v={{time()}}">
@endsection

@section('extra_js')
    <script>
        const from = "{{$from}}";
        const to = "{{$to}}";
    </script>
    <script src="/assets/js/eap-online/riports.js?t={{time()}}" charset="utf-8"></script>
@endsection

@section('content')
    <div class="row m-0">
        {{ Breadcrumbs::render('eap-online-riports.create', $from, $to) }}
        <h1 class="col-12 pl-0">EAP Online riportok generálása {{$from}} - {{$to}} intervallumra vonatkozóan</h1>
        {{-- <div class="riports-actions w-100">
            <button id="activate-riports" class="mr-1" disabled onClick="activateSelectedRiports()">Eap riportok
                aktiválása
            </button>
            <button id="deactivate-riports" disabled onClick="deactivateSelectedRiports()">Eap riportok deaktiválása
            </button>
            <button id="select-riports" class="float-right ml-2">Kijelölés</button>
            <button id="select-all-riports" class="float-right">Összes kijelölése</button>
        </div> --}}
        <div class="company-riports-holder">
            @foreach($companies as $company)
                @php
                    $current_riport = $company->eap_riports()->where('from', $from)->where('to' , $to)->first();
                    $active = empty($current_riport) ? false : $current_riport->is_active;
                @endphp
                <div class="company-riport d-flex flex-column" id="company_{{$company->id}}" data-id="{{$company->id}}">
                    <div class="company-riport-head">
                        <h2>
                            @if($active)
                                <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px" fill="none"
                                     viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            @endif
                            {{$company->name}}
                        </h2>
                    </div>
                    @foreach($company->countries as $country)
                        <p>{{$country->code}}:
                            <span class="count">{{$country->login_number}}</span>
                        </p>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>
@endsection
