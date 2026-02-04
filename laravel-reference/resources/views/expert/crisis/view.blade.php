@extends('layout.master')

@section('title')
    Expert Dashboard
@endsection

@section('extra_js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.js" type="text/javascript"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    <script src="{{asset('assets/js/datetime.js')}}" charset="utf-8"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9.7.1/dist/sweetalert2.all.min.js"></script>

    <script>
        $(function () {
            $('.datepicker').datepicker({
                format: 'yyyy-mm-dd',
                startDate: '0d'
            });

            $('.timepicker').datetimepicker({
                format: 'HH:mm',
            });
        })
    </script>
@endsection

@section('extra_css')

    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.css"
          rel="stylesheet"/>
    <link rel="stylesheet" href="{{asset('assets/css/cases/view.css')}}?t={{time()}}">
    <link rel="stylesheet" href="{{asset('assets/css/cases/datetime.css')}}?t={{time()}}">

    <style>
        #content .button-holder .button {
            float: right;
            margin-right: 10px;
        }

        #content .button-holder .button.denie {
            float: right;
            margin-right: 10px;
            background-color: #7c2469;
        }

        li.danger {
            background-color: rgb(219, 11, 32) !important;
            color: #fff !important;
        }

        li.warning {
            background-color: #f2da2f !important;
            color: #fff !important;
        }

        li.danger button {
            color: #fff;
        }
    </style>
@endsection

@section('content')
    @csrf
    <div class="row">
        <div class="col-12">
            <h1>{{__('crisis.view_crisis')}}</h1>
        </div>
        <div class="col-12 case-title">
            <p>{{$crisis_case->created_at}} - {{optional($crisis_case->company)->name}}
                - {{optional($crisis_case->country)->name}}</p>
        </div>
        <div class="col-12 case-details">
            <ul>
                <li>
                    <button> {{__('crisis.activity_id')}}: <span
                                id="case-status"> {{ $crisis_case->activity_id }}</span></button>
                </li>
                <li>
                    <button> {{__('crisis.company_name')}}: <span
                                id="case-status"> {{ optional($crisis_case->company)->name }}</span></button>
                </li>
                <li>
                    <button> {{__('crisis.contract_holder')}}: <span
                                id="case-status"> {{ $crisis_case->org_data()->contract_holder->name  }}</span></button>
                </li>
                @if(Auth::user()->type == 'admin' || Auth::user()->type == 'account_admin')
                    @if($crisis_case->crisis_intervention->free == 1)
                        <li>
                            <button> {{__('crisis.contract_price')}}: <span
                                        id="case-status"> {{__('crisis.free')}}</span></button>
                        </li>
                    @else
                        <li>
                            <button> {{__('crisis.contract_price')}}: <span
                                        id="case-in-price"> @if($crisis_case->crisis_intervention->price)
                                        {{ $crisis_case->crisis_intervention->price }}
                                    @endif <span
                                            style="text-transform: uppercase"> {{$crisis_case->crisis_intervention->valuta}}</span></span>
                            </button>
                        </li>
                    @endif
                @endif
                @if($crisis_case->status == \App\Enums\CrisisCaseStatus::OUTSOURCED)
                    @if($crisis_case->expert_status == \App\Enums\CrisisCaseExpertStatus::ADMIN_PRICE_CHANGE)
                        <li class="warning">
                            <button data-toggle="modal" data-target="#case-out-price">
                                <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                                     style="height:20px; margin-bottom: 3px" fill="none"
                                     viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg> {{__('crisis.expert_out_price')}}: <span
                                        id="case-out-price_s">@if($crisis_case->expert_status == 2) @if($crisis_case->expert_price)
                                        {{ $crisis_case->expert_price }}
                                    @endif
                                    <span
                                            style="text-transform: uppercase"> {{$crisis_case->expert_currency}} @else @if($crisis_case->expert_price)
                                            {{$crisis_case->expert_price}}
                                        @endif
                                        <span
                                                style="text-transform: uppercase"> {{$crisis_case->expert_currency}} @endif </span></span></span>
                            </button>
                        </li>
                    @elseif($crisis_case->expert_status == \App\Enums\CrisisCaseExpertStatus::EXPERT_PRICE_CHANGE)
                        <li class="warning">
                            <button data-toggle="modal" data-target="#case-out-price">
                                <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                                     style="height:20px; margin-bottom: 3px" fill="none"
                                     viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg> {{__('crisis.expert_out_price')}}: <span
                                        id="case-out-price_s">@if($crisis_case->expert_status == \App\Enums\CrisisCaseExpertStatus::EXPERT_PRICE_CHANGE) @if($crisis_case->expert_price)
                                        {{ $crisis_case->expert_price}}
                                    @endif
                                    <span
                                            style="text-transform: uppercase"> {{$crisis_case->expert_currency}} @else @if($crisis_case->expert_price)
                                            {{ $crisis_case->expert_price }}
                                        @endif
                                        <span
                                                style="text-transform: uppercase"> {{$crisis_case->expert_currency}} @endif </span></span></span>
                            </button>
                        </li>
                    @else
                        <li>
                            <button data-toggle="modal" data-target="#case-out-price">
                                <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                                     style="height:20px; margin-bottom: 3px" fill="none"
                                     viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg> {{__('crisis.expert_out_price')}}: <span
                                        id="case-out-price_s">@if($crisis_case->expert_status == 2) @if($crisis_case->expert_price)
                                        {{ $crisis_case->expert_price }}
                                    @endif
                                    <span
                                            style="text-transform: uppercase"> {{$crisis_case->expert_currency}} @else @if($crisis_case->expert_price)
                                            {{ $crisis_case->expert_price }}
                                        @endif
                                        <span
                                                style="text-transform: uppercase"> {{$crisis_case->expert_currency}} @endif </span></span></span>
                            </button>
                        </li>
                    @endif
                @else
                    <li>
                        {{__('crisis.expert_out_price')}}: 
                        <span id="case-out-price_s">
                            @if($crisis_case->expert_price)
                                {{ $crisis_case->expert_price}}
                            @endif
                            <span style="text-transform: uppercase"> 
                                {{$crisis_case->expert_currency}}
                            </span>
                        </span>
                    </li>
                @endif

                <li>
                    <button> {{__('crisis.company_email')}}: <span
                                id="case-status"> {{ $crisis_case->company_contact_email }}</span></button>
                </li>
                <li>
                    <button> {{__('crisis.company_phone')}}: <span
                                id="case-status"> {{ $crisis_case->company_contact_phone }}</span></button>
                </li>
                <li>
                    <button> {{__('crisis.country')}}: <span
                                id="case-status"> {{ optional($crisis_case->country)->name }}</span></button>
                </li>
                <li>
                    <button> {{__('crisis.city')}}: <span id="case-status"> {{ optional($crisis_case->city)->name }}</span>
                    </button>
                </li>
                <li>
                    <button> {{__('crisis.expert')}}: <span id="case-status"> {{ $crisis_case->user->name }}</span>
                    </button>
                </li>
                <li>
                    <button> {{__('crisis.expert_email')}}: <span
                                id="case-status"> {{ $crisis_case->user->email }}</span></button>
                </li>
                <li>
                    <button> {{__('crisis.expert_phone')}}: <span
                                id="case-status"> {{ $crisis_case->expert_phone }}</span></button>
                </li>
                <li>
                    <button> {{__('crisis.date')}}: <span id="case-status"> {{ $crisis_case->date }}</span>
                    </button>
                </li>
                <li>
                    <button> {{__('crisis.start_time')}}: <span
                                id="case-status"> {{ $crisis_case->start_time }}</span></button>
                </li>
                <li>
                    <button> {{__('crisis.end_time')}}: <span
                                id="case-status"> {{ $crisis_case->end_time }}</span></button>
                </li>
                <li>
                    <button> {{__('crisis.full_time')}}: <span
                                id="case-status"> {{ $crisis_case->full_time }}</span></button>
                </li>
                <li>
                    <button> {{__('crisis.language')}}: <span
                                id="case-status"> {{ $crisis_case->language }}</span></button>
                </li>
                <li>
                    <button data-toggle="modal" data-target="#number-of-participants">
                        <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                             style="height:20px; margin-bottom: 3px" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        {{__('workshop.number_of_participants')}}: <span id="case-status">
                    @if($crisis_case->number_of_participants)
                                {{ $crisis_case->number_of_participants }}
                            @else
                                {{__('workshop.not_specified')}}
                            @endif
                </span></button>
                </li>
            </ul>
        </div>
        <div class="col-6 back-button mb-5">
            <a href="{{ session()->get('list_url') }}">{{__('common.back-to-list')}}</a>
        </div>
        <div class="col-6 button-holder">
            @if(($crisis_case->expert_status != \App\Enums\CrisisCaseExpertStatus::ACCEPTED && $crisis_case->expert_status != \App\Enums\CrisisCaseExpertStatus::EXPERT_PRICE_CHANGE) && $crisis_case->status == \App\Enums\CrisisCaseStatus::OUTSOURCED)
                <input type="hidden" name="final_price" value="{{ $crisis_case->expert_price }}">
                <input type="hidden" name="final_valuta" value="{{ $crisis_case->expert_currency }}">
                <div class="d-flex justify-content-end">
                    <a href="{{route('expert.crisis.accept', $crisis_case->id)}}"
                       class="button">{{__('crisis.accept')}}</a>
                    <a href="{{route('expert.crisis.denie', $crisis_case->id)}}"
                       class="button denie">{{__('crisis.denied')}}</a>
                </div>
            @elseif($crisis_case->status == \App\Enums\CrisisCaseStatus::PRICE_ACCEPTED)
                <button style="background-color: rgb(0,87,95);" disabled
                        class="button btn-radius">{{__('crisis.accept')}}</button>
            @endif
        </div>
    </div>

@endsection

@section('modal')
<div class="modal" tabindex="-1" id="number-of-participants" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{__('workshop.number_of_participants')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{route('expert.crisis.update', $crisis_case->id)}}" method="post">
                    @csrf
                    <input type="number" name="number-of-participants"
                           placeholder="{{__('workshop.number_of_participants')}}">
                    <button class="btn-radius" style="float: right; --btn-margin-right: 0px;">
                        <img class="mr-1" src="{{asset('assets/img/save.svg')}}" class="mr-1" style="height: 20px; width: 20px" alt="">
                        <span class="mt-1">{{__('common.save')}}</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
    <div class="modal" tabindex="-1" id="case-out-price" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{__('crisis.expert_out_price')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post"
                          action="{{route('expert.crisis.update', $crisis_case->id)}}">
                        @if($crisis_case->expert_status == 2)
                            <input type="text" value="{{ $crisis_case->crisis_intervention->price }}" name="expert_price">
                            <select name="expert_currency" class="valuta" style="float: right">
                                <option @if(!$crisis_case->crisis_intervention->valuta) selected @endif value="">Valuta</option>
                                <option @if($crisis_case->crisis_intervention->valuta == "chf") selected @endif value="chf">CHF
                                </option>
                                <option @if($crisis_case->crisis_intervention->valuta == "czk") selected @endif value="czk">CZK
                                </option>
                                <option @if($crisis_case->crisis_intervention->valuta == "eur") selected @endif value="eur">EUR
                                </option>
                                <option @if($crisis_case->crisis_intervention->valuta == "huf") selected @endif value="huf">HUF
                                </option>
                                <option @if($crisis_case->crisis_intervention->valuta == "mdl") selected @endif value="mdl">MDL
                                </option>
                                <option @if($crisis_case->crisis_intervention->valuta == "oal") selected @endif value="oal">OAL
                                </option>
                                <option @if($crisis_case->crisis_intervention->valuta == "PLN") selected @endif value="pln">PLN
                                </option>
                                <option @if($crisis_case->crisis_intervention->valuta == "RON") selected @endif value="ron">RON
                                </option>
                                <option @if($crisis_case->crisis_intervention->valuta == "RSD") selected @endif value="rsd">RSD
                                </option>
                                <option @if($crisis_case->crisis_intervention->valuta == "USD") selected @endif value="usd">USD
                                </option>
                            </select>
                        @else
                            <input type="text" value="{{ $crisis_case->expert_price }}" name="expert_price">
                            <select name="expert_currency" class="valuta" style="float: right">
                                <option @if(!$crisis_case->expert_currency) selected @endif value="">Valuta
                                </option>
                                <option @if($crisis_case->expert_currency == "chf") selected @endif value="chf">
                                    CHF
                                </option>
                                <option @if($crisis_case->expert_currency == "czk") selected @endif value="czk">
                                    CZK
                                </option>
                                <option @if($crisis_case->expert_currency == "eur") selected @endif value="eur">
                                    EUR
                                </option>
                                <option @if($crisis_case->expert_currency == "huf") selected @endif value="huf">
                                    HUF
                                </option>
                                <option @if($crisis_case->expert_currency == "mdl") selected @endif value="mdl">
                                    MDL
                                </option>
                                <option @if($crisis_case->expert_currency == "oal") selected @endif value="oal">
                                    OAL
                                </option>
                                <option @if($crisis_case->expert_currency == "PLN") selected @endif value="pln">
                                    PLN
                                </option>
                                <option @if($crisis_case->expert_currency == "RON") selected @endif value="ron">
                                    RON
                                </option>
                                <option @if($crisis_case->expert_currency == "RSD") selected @endif value="rsd">
                                    RSD
                                </option>
                                <option @if($crisis_case->expert_currency == "USD") selected @endif value="usd">
                                    USD
                                </option>
                            </select>
                        @endif
                        <input type="hidden" name="input" value="select_out_price">
                        {{csrf_field()}}
                        <button class="btn-radius" style="float: right">
                            <img class="mr-1" src="{{asset('assets/img/save.svg')}}" class="mr-1" style="height: 20px; width: 20px" alt="">
                            <span class="mt-1">{{__('common.save')}}</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
