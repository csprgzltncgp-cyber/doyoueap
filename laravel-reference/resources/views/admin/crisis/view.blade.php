@extends('layout.master')

@section('title')
    Admin Dashboard
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
            });

            $('.timepicker').datetimepicker({
                format: 'HH:mm',
            });
        });

        $("#closeModal").click(function () {
            jQuery("#alert").removeClass('active-alert')
        });


        function showAlert(message) {
            Swal.fire({
                title: message,
                icon: 'info',
            });
        }

        @if(session()->has('missing_crisis_data'))
            Swal.fire(
                '{{session()->get('missing_crisis_data')}}',
                '',
                'error'
            );
        @endif

        function delete_crisis_interventions() {
            Swal.fire({
                title: "{{__('common.are-you-sure-to-delete')}}",
                text: "{{__('common.operation-cannot-undone')}}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: "{{__('common.yes-delete-it')}}",
                cancelButtonText: "{{__('common.cancel')}}"
            }).then((result) => {
                if (result.value) {
                    window.location.href = "{{route('admin.crisis.delete', $crisis_case->id)}}";
                }
            })
        }
    </script>
@endsection

@section('extra_css')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.css"
          rel="stylesheet"/>
    <link rel="stylesheet" href="{{asset('assets/css/cases/view.css')}}?t={{time()}}">
    <link rel="stylesheet" href="{{asset('assets/css/cases/datetime.css')}}?t={{time()}}">

    <style>
        #content .button-holder .button:last-child, #content .button-holder .myBtn:last-child {
            float: right !important;
            position: absolute;

        }

        a.button {
            margin-right: 16px;
        }

        #content .button-holder .button:first-child, #content .button-holder .myBtn:first-child {
            margin-right: 16px;
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

        .swal2-icon.swal2-warning {
            border-color: #facea8;
            color: #f8bb86;
        }

        .select_status {
            display: flex;
            align-items: center;
        }

        .select_status select {
            flex: 1;
        }
    </style>
@endsection

@section('content')
    @csrf
    <div class="row">
        <div class="col-12">
            {{ Breadcrumbs::render('crisis.view', $crisis_case->id) }}
            <h1>{{__('crisis.view_crisis')}}</h1>
        </div>
        <div class="col-12 case-title">
            <p>{{__('workshop.created_at')}} {{$crisis_case->created_at}} - {{$crisis_case->company->name}}
                - {{optional($crisis_case->country)->name}}</p>
        </div>
        <div class="col-12 case-details">
            <ul>
                <li>
                    <div> {{__('crisis.activity_id')}}: <span
                                id="case-status"> {{ $crisis_case->activity_id }}</span>
                    </div>
                </li>
                <li>
                    <button data-toggle="modal" data-target="#crisis_status">
                        <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                             style="height:20px; margin-bottom: 3px" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg> {{__('crisis.status')}}:
                        <span id="case-status">
                            @if($crisis_case->status == \App\Enums\CrisisCaseStatus::OUTSOURCED)
                                {{__('crisis.under_agreement')}}
                            @elseif($crisis_case->status == \App\Enums\CrisisCaseStatus::PRICE_ACCEPTED)
                                {{__('crisis.active')}}
                            @else
                                {{__('crisis.closed')}}
                            @endif
                        </span>
                    </button>
                </li>
                <li>
                    <div> {{__('crisis.company_name')}}: <span
                                id="case-status"> {{ $crisis_case->company->name }}</span></div>
                </li>
                <li>
                    <div> {{__('crisis.contract_holder')}}: <span
                                id="case-status"> {{ $crisis_case->org_data()->contract_holder->name }}</span>
                    </div>
                </li>
                <li>
                    <div> {{__('crisis.contract_date')}}: <span
                                id="case-status"> {{ $crisis_case->org_data()->contract_date }}</span></div>
                </li>
                @if($crisis_case->crisis_intervention->free == 1)
                    <li>
                        <button> {{__('crisis.contract_price')}}: <span
                                    id="case-status"> {{__('crisis.free')}}</span></button>
                    </li>
                @else
                    <li>
                        <div> {{__('crisis.contract_price')}}: <span
                                    id="case-in-price"> @if($crisis_case->crisis_intervention->crisis_price)
                                    {{ $crisis_case->crisis_intervention->crisis_price }}
                                @endif <span
                                        style="text-transform: uppercase"> {{$crisis_case->crisis_intervention->valuta}}</span></span>
                        </div>
                    </li>
                @endif
                @if($crisis_case->expert_status == 3)
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
                                        style="text-transform: uppercase"> {{$expert_currency}} @else @if($crisis_case->expert_price)
                                        {{ $crisis_case->expert_price }}
                                    @endif
                                    <span
                                            style="text-transform: uppercase"> {{$expert_currency}} @endif </span></span></span>
                        </button>
                    </li>
                @else
                    <li>
                        <button @if($is_outsourced)data-toggle="modal" data-target="#case-out-price"
                                @else onclick="showAlert('{{__('workshop.expert_price_error')}}')" @endif>
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
                                        style="text-transform: uppercase"> {{$expert_currency}} @else @if($crisis_case->expert_price)
                                        {{ $crisis_case->expert_price }}
                                    @endif
                                    <span
                                            style="text-transform: uppercase"> {{$expert_currency}} @endif </span></span></span>
                        </button>
                    </li>
                @endif

                @if($crisis_case->expert_status == 2)
                    <li class="warning">
                        <div> {{__('crisis.expert_in_price')}}: <span
                                    id="case-out-price_s"> @if($crisis_case->expert_price)
                                    {{ $crisis_case->expert_price }}
                                @endif <span
                                        style="text-transform: uppercase"> {{$expert_currency}}</span></span>
                        </div>
                    </li>
                @else
                    <li>
                        <div> {{__('crisis.expert_in_price')}}: <span
                                    id="case-out-price_s"> @if($crisis_case->expert_price)
                                    {{ $crisis_case->expert_price }}
                                @endif <span
                                        style="text-transform: uppercase"> {{$expert_currency}}</span></span>
                        </div>
                    </li>

                @endif
                <li class="@if(is_null($crisis_case->company_contact_email)) danger @endif">
                    <button data-toggle="modal" data-target="#company_mail">
                        <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                             style="height:20px; margin-bottom: 3px" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg> {{__('crisis.company_email')}}:
                        <span id="case-status"> {{ $crisis_case->company_contact_email }}</span></button>
                </li>
                <li class="@if(is_null($crisis_case->company_contact_phone)) danger @endif">
                    <button data-toggle="modal" data-target="#company_phone">
                        <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                             style="height:20px; margin-bottom: 3px" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg> {{__('crisis.company_phone')}}: <span
                                id="case-status"> {{ $crisis_case->company_contact_phone }}</span>
                    </button>
                </li>
                <li class="@if(is_null($crisis_case->country_id)) danger @endif">
                    <button data-toggle="modal" data-target="#country">
                        <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                             style="height:20px; margin-bottom: 3px" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg> {{__('crisis.country')}}: <span
                                id="case-status"> {{ optional($crisis_case->country)->name }}</span></button>
                </li>
                <li class="@if(is_null(optional($crisis_case->city)->name)) danger @endif">
                    <button data-toggle="modal" data-target="#city">
                        <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                             style="height:20px; margin-bottom: 3px" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg> {{__('crisis.city')}}:
                        <span
                                id="case-status"> {{ optional($crisis_case->city)->name }}</span></button>
                </li>
                <li class="@if(is_null($crisis_case->user)) danger @endif">
                    <button @if($is_outsorceable) data-toggle="modal" data-target="#expert"
                            @else onclick="showAlert('{{__('workshop.outsource_error')}}')" @endif>
                        <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                             style="height:20px; margin-bottom: 3px" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg> {{__('crisis.expert')}}: <span
                                id="case-status"> {{ !empty($crisis_case->user) ? $crisis_case->user->name: '' }}</span>
                    </button>
                </li>
                <li>
                    <div> {{__('crisis.expert_email')}}: <span
                                id="case-status"> {{ !empty($crisis_case->user) ? $crisis_case->user->email: '' }}</span>
                    </div>
                </li>
                <li>
                    <button data-toggle="modal" data-target="#expert_phone">
                        <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                             style="height:20px; margin-bottom: 3px" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg> {{__('crisis.expert_phone')}}: <span
                                id="case-status"> {{ $crisis_case->expert_phone }}</span></button>
                </li>
                <li class="@if(is_null($crisis_case->date)) danger @endif">
                    <button data-toggle="modal" data-target="#date">
                        <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                             style="height:20px; margin-bottom: 3px" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        {{__('crisis.date')}}:
                        <span
                                id="case-status"> {{ $crisis_case->date }}</span></button>
                </li>
                <li class="@if(is_null($crisis_case->start_time)) danger @endif">
                    <button data-toggle="modal" data-target="#start_time">
                        <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                             style="height:20px; margin-bottom: 3px" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg> {{__('crisis.start_time')}}: <span
                                id="case-status"> {{ $crisis_case->start_time }}</span></button>
                </li>
                <li class="@if(is_null($crisis_case->end_time)) danger @endif">
                    <button data-toggle="modal" data-target="#end_time">
                        <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                             style="height:20px; margin-bottom: 3px" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg> {{__('crisis.end_time')}}:
                        <span id="case-status"> {{ $crisis_case->end_time }}</span></button>
                </li>
                <li>
                    <div> {{__('crisis.full_time')}}: <span
                                id="case-status"> {{ $crisis_case->full_time }}</span>
                    </div>
                </li>
                <li class="@if(is_null($crisis_case->language)) danger @endif">
                    <button data-toggle="modal" data-target="#language">
                        <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                             style="height:20px; margin-bottom: 3px" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>{{__('crisis.language')}}: <span
                                id="case-status"> {{ $crisis_case->language }}</span></button>
                </li>
                @if(optional(optional($crisis_case->org_data())->contract_holder)->id == 2)
                    <li>
                        <button data-toggle="modal" data-target="#number_of_participants">
                                <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                                    style="height:20px; margin-bottom: 3px" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                {{__('workshop.number_of_participants')}}:
                                <span id="case-status">
                                    @if($crisis_case->number_of_participants)
                                        {{ $crisis_case->number_of_participants }}
                                    @else
                                        {{__('workshop.not_specified')}}
                                    @endif
                                </span>
                        </button>
                    </li>
                @endif

                @if($crisis_case->invoice_data()->exists())
                <li>
                    <div> {{__('common.closed_at')}}: <span id="case-status"> {{ Carbon\Carbon::parse($crisis_case->invoice_data->created_at)->format('Y-m-d') }}</span>
                    </div>
                </li>
            @endif
            </ul>
        </div>
        <div class="col-4 col-lg-2 back-button mb-5">
            <a href="{{ session()->get('list_url') }}">{{__('common.back-to-list')}}</a>
        </div>
        <div class="col-8 col-lg-10 button-holder d-flex flex-column flex-lg-row align-items-start justify-content-end"
             style="text-align: right">
            @if($crisis_case->expert_status == \App\Enums\CrisisCaseExpertStatus::EXPERT_PRICE_CHANGE && $crisis_case->status != \App\Enums\CrisisCaseStatus::PRICE_ACCEPTED)
                <a href="{{route('admin.crisis.accept_expert_price', $crisis_case->id)}}"
                   class="button">{{__('crisis.accept_expert_offer')}}</a>
            @endif
            <div class="d-flex my-3 justify-content-between my-lg-0 justify-content-lg-end">
                @if(\Auth::user()->type == 'admin' || Auth::user()->type == 'account_admin')
                    <button onclick="delete_crisis_interventions()" style="background-color: #7c2469"
                       class="button btn-radius d-flex">
                        <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                             style="height:20px; margin-bottom: 3px" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        {{__('common.delete')}}</button>
                @endif
                @if($crisis_case->status != \App\Enums\CrisisCaseStatus::CLOSED)
                    <a

                    @if(!empty($crisis_case->number_of_participants) ||optional(optional($crisis_case->org_data())->contract_holder)->id != 2)
                        href="{{route('admin.crisis.close', $crisis_case->id)}}"
                    @else
                        onclick="showAlert('{{__('workshop.number_of_participants')}} {{__('eap-online.required')}}!')"
                    @endif
                       class="button btn-radius position-relative d-flex">
                        <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                             style="height:20px; margin-bottom: 3px" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                        {{__('crisis.close_crisis')}}</a>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('modal')
    <div class="modal" tabindex="-1" id="activity_id" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{__('crisis.activity_id')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post" action="{{route('admin.crisis.update', $crisis_case->id)}}">
                        <input type="text" value="{{ $crisis_case->activity_id }}"
                               name="activity_id">
                        <input type="hidden" name="input" value="activity_id">
                        {{csrf_field()}}
                        <button class="btn-radius" style="float: right; --btn-margin-right: 0px;">
                            <img class="mr-1" src="{{asset('assets/img/save.svg')}}" style="height: 20px; width: 20px" alt="">
                            <span class="mt-1">{{__('common.save')}}</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" tabindex="-1" id="crisis_status" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{__('crisis.crisis_status_change_title')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="select_status d-block" method="post"
                          action="{{route('admin.crisis.update', $crisis_case->id)}}">
                        <select class="w-100" name="status">
                            <option @if($crisis_case->status == \App\Enums\CrisisCaseStatus::OUTSOURCED) selected @endif value="1">
                                {{__('crisis.under_agreement')}}
                            </option>
                            <option @if($crisis_case->status == \App\Enums\CrisisCaseStatus::PRICE_ACCEPTED ) selected @endif value="2">
                                {{__('crisis.active')}}
                            </option>
                            <option @if($crisis_case->status == \App\Enums\CrisisCaseStatus::CLOSED) selected @endif value="3">
                                {{__('crisis.closed')}}
                            </option>
                        </select>
                        <input type="hidden" name="input" value="status">
                        {{csrf_field()}}
                        <button class="btn-radius" style="float: right; --btn-margin-right: 0px;">
                            <img class="mr-1" src="{{asset('assets/img/save.svg')}}" style="height: 20px; width: 20px" alt="">
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
                    <form method="post" action="{{route('admin.crisis.update', $crisis_case->id)}}">
                        <input placeholder="{{$expert_currency}}" type="text" value="{{ $crisis_case->expert_price }}" name="expert_price">
                        <input type="hidden" name="expert_currency" value="{{$expert_currency}}">
                        <input type="hidden" name="input" value="select_out_price">
                        {{csrf_field()}}
                        <button class="btn-radius" style="float: right; --btn-margin-right: 0px;">
                            <img class="mr-1" src="{{asset('assets/img/save.svg')}}" style="height: 20px; width: 20px" alt="">
                            <span class="mt-1">{{__('common.save')}}</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" tabindex="-1" id="company_mail" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{__('crisis.company_email')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post" action="{{route('admin.crisis.update', $crisis_case->id)}}">
                        <input type="text" value="{{ $crisis_case->company_contact_email }}"
                               name="company_contact_email">
                        <input type="hidden" name="input" value="company_contact_email">
                        {{csrf_field()}}
                        <button class="btn-radius" style="float: right; --btn-margin-right: 0px;">
                            <img class="mr-1" src="{{asset('assets/img/save.svg')}}" style="height: 20px; width: 20px" alt="">
                            <span class="mt-1">{{__('common.save')}}</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" tabindex="-1" id="company_name" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{__('crisis.company_name')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post" action="{{route('admin.crisis.update', $crisis_case->id)}}">
                        <select name="company_id" id="company_id" class="w-100">
                            @foreach($companies as $company)
                                <option value="{{$company->id}}">{{$company->name}}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="input" value="company_id">
                        {{csrf_field()}}
                        <button class="btn-radius" style="float: right; --btn-margin-right: 0px;">
                            <img class="mr-1" src="{{asset('assets/img/save.svg')}}" style="height: 20px; width: 20px" alt="">
                            <span class="mt-1">{{__('common.save')}}</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" tabindex="-1" id="company_phone" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{__('crisis.company_phone')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post" action="{{route('admin.crisis.update', $crisis_case->id)}}">
                        <input type="text" value="{{ $crisis_case->company_contact_phone }}"
                               name="company_contact_phone">
                        <input type="hidden" name="input" value="company_contact_phone">
                        {{csrf_field()}}
                        <button class="btn-radius" style="float: right; --btn-margin-right: 0px;">
                            <img class="mr-1" src="{{asset('assets/img/save.svg')}}" style="height: 20px; width: 20px" alt="">
                            <span class="mt-1">{{__('common.save')}}</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" tabindex="-1" id="country" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{__('crisis.country')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post" action="{{route('admin.crisis.update', $crisis_case->id)}}">
                        <select class="w-100" name="country_id">
                            @foreach($countries as $country)
                                <option value="{{ $country->id }}"
                                        @if($crisis_case->countries_id == $country->id) selected @endif>{{ $country->name }}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="input" value="country_id">
                        {{csrf_field()}}
                        <button class="btn-radius" style="float: right; --btn-margin-right: 0px;">
                            <img class="mr-1" src="{{asset('assets/img/save.svg')}}" style="height: 20px; width: 20px;" alt="">
                            <span class="mt-1">{{__('common.save')}}</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" tabindex="-1" id="city" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{__('crisis.city')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post" action="{{route('admin.crisis.update', $crisis_case->id)}}">
                        <select name="city_id" style="width: 100%!important">
                            @foreach($cities as $city)
                                <option value="{{ $city->id }}"
                                        @if($crisis_case->city_id == $city->id) selected @endif>{{ $city->name }}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="input" value="city_id">
                        {{csrf_field()}}
                        <button class="btn-radius" style="float: right; --btn-margin-right: 0px">
                            <img class="mr-1" src="{{asset('assets/img/save.svg')}}" style="height: 20px; width: 20px" alt="">
                            <span class="mt-1">{{__('common.save')}}</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" tabindex="-1" id="expert" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{__('crisis.expert')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post" action="{{route('admin.crisis.update', $crisis_case->id)}}">
                        <select name="expert" class="w-100">
                            @foreach($experts as $expert)
                                <option value="{{ $expert->id }}"
                                        @if(!empty($crisis_case->user) ? $crisis_case->user->id : '' == $expert->id) selected @endif>{{ $expert->name }}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="input" value="expert">
                        {{csrf_field()}}
                        <button class="btn-radius" style="float: right; --btn-margin-right: 0px">
                            <img class="mr-1" src="{{asset('assets/img/save.svg')}}" style="height: 20px; width: 20px" alt="">
                            <span class="mt-1">{{__('common.save')}}</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" tabindex="-1" id="expert_phone" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{__('crisis.expert_phone')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post" action="{{route('admin.crisis.update', $crisis_case->id)}}">
                        <input type="text" value="{{ $crisis_case->expert_phone }}" name="expert_phone">
                        <input type="hidden" name="input" value="expert_phone">
                        {{csrf_field()}}
                        <button class="btn-radius" style="float: right; --btn-margin-right: 0px">
                            <img class="mr-1" src="{{asset('assets/img/save.svg')}}" style="height: 20px; width: 20px" alt="">
                            <span class="mt-1">{{__('common.save')}}</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" tabindex="-1" id="date" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{__('crisis.date')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post" action="{{route('admin.crisis.update', $crisis_case->id)}}">
                        <input type="text" class="datepicker" value="{{ $crisis_case->date }}"
                               name="date">
                        <input type="hidden" name="input" value="date">
                        {{csrf_field()}}
                        <button class="btn-radius" style="float: right; --btn-margin-right: 0px">{{__('common.save')}}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" tabindex="-1" id="start_time" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{__('crisis.start_time')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post" action="{{route('admin.crisis.update', $crisis_case->id)}}">
                        <input type="text" name="start_time" class="timepicker"
                               value="{{ $crisis_case->start_time }}">
                        <input type="hidden" name="end_time" value="{{ $crisis_case->end_time }}">
                        <input type="hidden" name="input" value="start_time">
                        {{csrf_field()}}
                        <button class="btn-radius" style="float: right; --btn-margin-right: 0px">
                            <img class="mr-1" src="{{asset('assets/img/save.svg')}}" style="height: 20px; width: 20px" alt="">
                            <span class="mt-1">{{__('common.save')}}</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" tabindex="-1" id="end_time" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{__('crisis.end_time')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post" action="{{route('admin.crisis.update', $crisis_case->id)}}">
                        <input type="text" name="end_time" class="timepicker"
                               value="{{ $crisis_case->end_time }}">
                        <input type="hidden" name="start_time" value="{{ $crisis_case->start_time }}">
                        <input type="hidden" name="input" value="end_time">
                        {{csrf_field()}}
                        <button class="btn-radius" style="float: right; --btn-margin-right: 0px">
                            <img class="mr-1" src="{{asset('assets/img/save.svg')}}" style="height: 20px; width: 20px" alt="">
                            <span class="mt-1">{{__('common.save')}}</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" tabindex="-1" id="language" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{__('crisis.language')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post" action="{{route('admin.crisis.update', $crisis_case->id)}}">
                        <input type="text" name="language"
                               placeholder="{{ __('crisis.select_language') }}" required>
                        <input type="hidden" name="input" value="language">
                        {{csrf_field()}}
                        <button class="btn-radius" style="float: right; --btn-margin-right: 0px">
                            <img class="mr-1" src="{{asset('assets/img/save.svg')}}" style="height: 20px; width: 20px" alt="">
                            <span class="mt-1">{{__('common.save')}}</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" tabindex="-1" id="number_of_participants" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{__('workshop.number_of_participants')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post" action="{{route('admin.crisis.update', $crisis_case->id)}}">
                        <input type="text" value="{{ $crisis_case->number_of_participants }}"
                               name="number_of_participants">
                        <input type="hidden" name="input" value="number_of_participants">
                        {{csrf_field()}}
                        <button class="btn-radius" style="float: right; --btn-margin-right: 0px">{{__('common.save')}}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
