@extends('layout.master')

@section('extra_css')
    <link rel="stylesheet" href="{{asset('assets/css/list.css')}}?v={{time()}}">
    <style>
        .list-element button, .list-element a {
            margin-right: 10px;
        }

        button.submit {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background: transparent;
            border: 0 solid black;
            color: #007bff;
            outline: none;
        }

        button.submit:hover {
            color: #0056b3;
            text-decoration: underline;
        }

        .list-element {
            cursor: pointer;
        }

        .company {
            padding-left: 25px;
        }

        select {
            width: 80px;
            outline: none !important;


            background: transparent;
            color: #007bff;
            border: none;
            text-align: center;
        }
    </style>
@endsection

@section('extra_js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9.7.1/dist/sweetalert2.all.min.js"></script>
    <script>
        $(function () {
            $('.contractHolder').on('click', function (e) {
                if (!$(e.target).is('a')) {
                    toggleCompanies($(this).data('country'), $(this));
                }
            });

            @if(session()->has('file-not-found'))
            Swal.fire(
                '{{session()->get('file-not-found')}} {{__('common.not-found')}}!',
                '',
                'error'
            );
            @endif

            @if(session()->has('riport-export-started'))
            Swal.fire(
                '{{session()->get('riport-export-started')}}',
                '',
                'success'
            );
            @endif
        })

        function toggleCompanies(countryId, element) {
            if ($(element).hasClass('active')) {
                $(element).removeClass('active');
                $('.list-element .caret-left').html(`<svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                                    </svg>`);
                $('.list-element').each(function () {
                    if ($(this).data('country') && $(this).data('country') == countryId && !$(this).hasClass('group')) {
                        $(this).addClass('d-none');
                    }
                });
            } else {
                $(element).addClass('active');
                $(element).find('button.caret-left').html(`<svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px; color:white;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                      <path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7" />
                                                    </svg>`);
                $('.list-element').each(function () {
                    if ($(this).data('country') && $(this).data('country') == countryId) {
                        $(this).removeClass('d-none');
                    } else if (!$(this).hasClass('group')) {
                        $(this).addClass('d-none');
                    }
                });
            }
        }
    </script>
@endsection

@section('title')
    Admin Dashboard
@endsection

@section('content')
    <div class="row m-0">
        {{ Breadcrumbs::render('client-riports') }}
        <h1 class="col-12 pl-0">{{__('common.report_generation')}}</h1>
        @foreach($contract_holders as $contract_holder)
            <div class="  @if($contract_holder->id == 2) case-list-in @endif list-element col-12 group contractHolder my-1"
                 data-country= {{$contract_holder->id}}>
                {{$contract_holder->name}}
                @if($contract_holder->id == 2)
                    @if(\Auth::user()->type == 'admin' || \Auth::user()->type == 'account_admin')
                        <a href="{{route('admin.riports.create')}}"
                           class="ml-3">{{__('common.riport-data')}}</a>
                    @endif
                    <button class="caret-left float-right">
                        <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px;" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                @else
                    <form action="{{route('admin.riports.export')}}" method="post" class="float-right">
                        @csrf
                        <select name="year" style="margin-right: 13px">xxx
                            @foreach($contract_holder_years as $date)
                                <option {{$date->year == now()->year ? 'selected' : ''}}
                                        value="{{$date->year}}">{{$date->year}}</option>
                            @endforeach
                        </select>
                        <select name="month">
                            @foreach($contract_holder_months as $date)
                                <option {{$date->month == now()->subMonthWithNoOverflow()->month ? 'selected' : ''}}
                                        value="{{$date->month}}">{{$date->translatedFormat('F')}}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="submit">
                            <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                                 style="height:20px; margin-bottom: 3px" fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" web
                                      d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            {{__('common.actual-riport-download')}}
                        </button>
                        <input type="hidden" name="contract_holder_id" value="{{$contract_holder->id}}">
                    </form>
                @endif
            </div>
            @if($contract_holder->id == 2)
                @foreach($contract_holder->companies()->sortBy('name') as $company)
                    <div style="display: flex; justify-content: space-between;" class="list-element col-12 d-none align-items-center"
                         data-country="{{$contract_holder->id}}">
                        <span class="company">{{$company->name}}</span>
                        @if($company->clientUsers->count())
                            <form action="{{route('admin.riports.show')}}" method="post">
                                @csrf
                                <input type="hidden" name="user_id" value="{{$company->clientUsers->first()->id}}"/>
                                @if ($company->get_connected_companies()->count() > 1)
                                    <input type="hidden" name="url" value="{{route('client.riport.show',  ['totalView' => 1])}}"/>
                                @else
                                    <input type="hidden" name="url" value="{{route('client.riport.show')}}">
                                @endif
                                <div class="d-flex flex-row justify-content-center align-items-center">
                                    <div class="mr-3">
                                        @foreach ([1,2,3,4] as $quarter)
                                            @if($loop->iteration > 1)
                                                -
                                            @endif
                                            Q{{$quarter}}
                                            @if (company_quarter_riport_active($company, $quarter))
                                                <svg xmlns="http://www.w3.org/2000/svg" style="width:20px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                                </svg>
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg" style="width:20px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            @endif
                                        @endforeach
                                    </div>
                                    <button type="submit" class="float-right submit">
                                        <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                                             style="height:20px; margin-bottom: 3px" fill="none" viewBox="0 0 24 24"
                                             stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        Riport megtekintése
                                    </button>
                                </div>
                            </form>
                        @else
                            <span class="text-danger" style="margin-right: 17px;">{{__('riport.not_registered')}}</span>
                        @endif
                    </div>
                @endforeach
            @endif
        @endforeach
        <div class=" my-1 list-element col-12 group contractHolder">
            Colep
            <form action="{{route('admin.riports.download_custom_company_riport')}}" method="post" class="float-right">
                @csrf
                <select name="year" style="margin-right: 13px">
                    @foreach($contract_holder_years as $date)
                        <option {{$date->year == now()->year ? 'selected' : ''}}
                                value="{{$date->year}}">{{$date->year}}</option>
                    @endforeach
                </select>
                <select name="month">
                    @foreach($contract_holder_months as $date)
                        <option {{$date->month == now()->subMonthWithNoOverflow()->month ? 'selected' : ''}}
                                value="{{$date->month}}">{{$date->translatedFormat('F')}}</option>
                    @endforeach
                </select>
                <button type="submit" class="submit">
                    <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                         style="height:20px; margin-bottom: 3px" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" web
                              d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    {{__('common.actual-riport-download')}}
                </button>
                <input type="hidden" name="company_id" value="613">
            </form>
        </div>
        <div class=" my-1 list-element col-12 group contractHolder">
            Deloitte
            <form action="{{route('admin.riports.company-summarize')}}" method="post" class="float-right">
                @csrf
                <select name="quarter" style="margin-right: 13px">
                    @for($i = 1; $i <= get_last_quarter(); $i++)
                        <option value="{{$i}}">Q{{$i}}</option>
                    @endfor
                    <option value="cumulated" selected>{{implode('+',array_map(function($q){return 'Q'. $q;}, range(1, get_last_quarter())))}}</option>
                </select>
                <button type="submit" class="submit">
                    <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                         style="height:20px; margin-bottom: 3px" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" web
                              d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    {{__('common.actual-riport-download')}}
                </button>
                <input type="hidden" value="deloitte" name="company_prefix">
            </form>
        </div>
        <div class=" my-1 list-element col-12 group contractHolder">
            Pepco
            <form action="{{route('admin.riports.company-summarize')}}" method="post" class="float-right">
                @csrf
                <select name="quarter" style="margin-right: 13px">
                    @for($i = 1; $i <= get_last_quarter(); $i++)
                        <option value="{{$i}}">Q{{$i}}</option>
                    @endfor
                    <option value="cumulated" selected>{{implode('+',array_map(function($q){return 'Q'. $q;}, range(1, get_last_quarter())))}}</option>
                </select>
                <button type="submit" class="submit">
                    <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                         style="height:20px; margin-bottom: 3px" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" web
                              d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    {{__('common.actual-riport-download')}}
                </button>
                <input type="hidden" value="pepco" name="company_prefix">
            </form>
        </div>
    </div>
@endsection
