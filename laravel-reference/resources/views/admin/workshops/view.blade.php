@extends('layout.master')

@section('title')
    Admin Dashboard
@endsection

@section('extra_js')
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
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

            $('.invoiceable_after').datepicker({
                viewMode: "months",
                minViewMode: "months",
                format: 'yyyy-mm',
                startDate: '-0d',
            });

            $('.timepicker').datetimepicker({
                format: 'HH:mm',
            });

            @if(session()->has('expert-outsourced'))
            Swal.fire({
                title: "{{__('workshop.outsourced_success')}}",
                icon: 'info'
            });
            @endif

            @if(session()->has('invalid_file_extension'))
            Swal.fire({
                title: "{{__('workshop.invalid_file_extension')}}",
                icon: 'error'
            });
            @endif

            @if(session()->has('data_error'))
            Swal.fire({
                title: "{{__('workshop.data_error')}}",
                icon: 'error'
            });
            @endif
        })

        $("#closeModal").click(function () {
            jQuery("#alert").removeClass('active-alert')
        });

        function showAlert(message) {
            Swal.fire({
                title: message,
                icon: 'info'
            });
        }

        function deleteWorkshop(){
            Swal.fire({
                title: "{{__('common.are-you-sure-to-delete')}}",
                text: "{{__('common.operation-cannot-undone')}}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: "{{__('common.yes-delete-it')}}",
                cancelButtonText: "{{__('common.cancel')}}"
            }).then((result) => {
                if (result.value) {
                    window.location.href = "{{route('admin.workshops.delete', $workshop_case->id)}}";
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
            {{ Breadcrumbs::render('workshops.view', $workshop_case->id) }}
            <h1>{{__('workshop.view_workshop')}}</h1>
        </div>
        <div class="col-12 case-title">
            <p>{{__('workshop.created_at')}} {{$workshop_case->created_at}} - {{$workshop_case->company->name}}
                - {{optional($workshop_case->country)->name}}</p>
        </div>
        <div class="col-12 case-details">
            <ul>
                <li>
                    <button data-toggle="modal" data-target="#activity_id">
                        <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                             style="height:20px; margin-bottom: 3px" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg> {{__('workshop.activity_id')}}:
                        <span id="case-status"> {{ $workshop_case->activity_id }}</span></button>
                </li>
                <li>
                    <button data-toggle="modal" data-target="#workshop_status">
                        <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                             style="height:20px; margin-bottom: 3px" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg> {{__('workshop.status')}}:
                        <span id="case-status">
                            @if($workshop_case->status == \App\Enums\WorkshopCaseStatus::OUTSOURCED)
                                {{__('workshop.under_agreement')}}
                            @elseif($workshop_case->status == \App\Enums\WorkshopCaseStatus::PRICE_ACCEPTED)
                                {{__('workshop.active')}}
                            @else
                                {{__('workshop.closed')}}
                            @endif
                        </span>
                    </button>
                </li>
                <li>
                    <button data-toggle="modal" data-target="#company_name">
                        <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                             style="height:20px; margin-bottom: 3px" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        {{__('workshop.company_name')}}:
                        <span id="case-status"> {{ $workshop_case->company->name }}</span>
                    </button>
                </li>
                <li>
                    <div> {{__('workshop.contract_holder')}}: <span
                                id="case-status"> {{ $workshop_case->org_data()->contract_holder->name }}</span>
                    </div>
                </li>
                <li>
                    <div> {{__('workshop.contract_date')}}: <span
                                id="case-status"> {{ $workshop_case->org_data()->contract_date}}</span>
                    </div>
                </li>
                @if($workshop_case->workshop->free == 1)
                    <li>
                        <button data-toggle="modal" data-target="#contract_price">
                            <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                                 style="height:20px; margin-bottom: 3px" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            {{__('workshop.contract_price')}}: <span
                                    id="case-status"> {{__('workshop.free')}}</span></button>
                    </li>
                @else
                    <li>
                        <button data-toggle="modal" data-target="#contract_price">
                            <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                                 style="height:20px; margin-bottom: 2px" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            {{__('workshop.contract_price')}}:
                            <span
                                    id="case-in-price">
                                @if($workshop_case->workshop->workshop_price)
                                    {{ $workshop_case->workshop->workshop_price }}
                                @endif <span
                                        style="text-transform: uppercase"> {{$workshop_case->workshop->valuta}}</span></span>
                        </button>
                    </li>
                @endif
                @if($workshop_case->expert_status == \App\Enums\WorkshopCaseExpertStatus::ADMIN_PRICE_CHANGE)
                    <li class="warning">
                        <button data-toggle="modal" data-target="#case-out-price">
                            <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                                 style="height:20px; margin-bottom: 3px" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg> {{__('workshop.expert_out_price')}}: <span
                                    id="case-out-price_s">@if($workshop_case->expert_status == \App\Enums\WorkshopCaseExpertStatus::EXPERT_PRICE_CHANGE) @if($workshop_case->expert_price)
                                    {{ $workshop_case->expert_price}}
                                @endif
                                <span
                                        style="text-transform: uppercase"> {{$expert_currency}} @else @if($workshop_case->expert_price)
                                        {{ $workshop_case->expert_price }}
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
                            </svg>
                            {{__('workshop.expert_out_price')}}: <span
                                    id="case-out-price_s">@if($workshop_case->expert_status == \App\Enums\WorkshopCaseExpertStatus::EXPERT_PRICE_CHANGE)  @if($workshop_case->expert_price)
                                    {{ $workshop_case->expert_price }}
                                @endif
                                <span
                                        style="text-transform: uppercase"> {{$expert_currency}} @else @if($workshop_case->expert_price)
                                        {{ $workshop_case->expert_price }}
                                    @endif
                                    <span
                                            style="text-transform: uppercase"> {{$expert_currency}} @endif </span></span></span>
                        </button>
                    </li>
                @endif

                @if($workshop_case->expert_status == \App\Enums\WorkshopCaseExpertStatus::EXPERT_PRICE_CHANGE)
                    <li class="warning">
                        <div> {{__('crisis.expert_in_price')}}: <span
                                    id="case-out-price_s"> @if($workshop_case->expert_price)
                                    {{ $workshop_case->expert_price }}
                                @endif <span
                                        style="text-transform: uppercase"> {{$expert_currency}}</span></span>
                        </div>
                    </li>
                @else
                    <li>
                        <div> {{__('crisis.expert_in_price')}}: <span
                                    id="case-out-price_s"> @if($workshop_case->expert_price)
                                    {{ $workshop_case->expert_price }}
                                @endif <span
                                        style="text-transform: uppercase"> {{$expert_currency}}</span></span>
                        </div>
                    </li>

                @endif

                <li class="@if(is_null($workshop_case->company_contact_email)) danger @endif">
                    <button data-toggle="modal" data-target="#company_mail">
                        <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                             style="height:20px; margin-bottom: 3px" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg> {{__('workshop.company_email')}}:
                        <span id="case-status"> {{ $workshop_case->company_contact_email }}</span></button>
                </li>
                <li class="@if(is_null($workshop_case->company_contact_phone)) danger @endif">
                    <button data-toggle="modal" data-target="#company_phone">
                        <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                             style="height:20px; margin-bottom: 3px" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg> {{__('workshop.company_phone')}}: <span
                                id="case-status"> {{ $workshop_case->company_contact_phone }}</span>
                    </button>
                </li>
                <li class="@if(is_null($workshop_case->country_id)) danger @endif">
                    <button data-toggle="modal" data-target="#country">
                        <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                             style="height:20px; margin-bottom: 3px" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg> {{__('workshop.country')}}: <span
                                id="case-status"> {{ optional($workshop_case->country)->name }}</span></button>
                </li>
                <li class="@if(is_null(optional($workshop_case->city)->name)) danger @endif">
                    <button data-toggle="modal" data-target="#city">
                        <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                             style="height:20px; margin-bottom: 3px" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg> {{__('workshop.city')}}:
                        <span
                                id="case-status"> {{ optional($workshop_case->city)->name }}</span></button>
                </li>
                <li class="@if(is_null($workshop_case->user)) danger @endif">
                    <button @if($is_outsorceable) data-toggle="modal" data-target="#expert"
                            @else onclick="showAlert('{{__('workshop.outsource_error')}}')" @endif>
                        <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                             style="height:20px; margin-bottom: 3px" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        {{__('workshop.expert')}}: <span
                                id="case-status"> {{ !empty($workshop_case->user) ? $workshop_case->user->name : '' }}</span>
                    </button>
                </li>
                <li>
                    <div> {{__('workshop.expert_email')}}: <span
                                id="case-status"> {{ !empty($workshop_case->user) ? $workshop_case->user->email : '' }}</span>
                    </div>
                </li>
                <li>
                    <button data-toggle="modal" data-target="#expert_phone">
                        <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                             style="height:20px; margin-bottom: 3px" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg> {{__('workshop.expert_phone')}}: <span
                                id="case-status"> {{ $workshop_case->expert_phone }}</span></button>
                </li>
                <li class="@if(is_null($workshop_case->date)) danger @endif">
                    <button data-toggle="modal" data-target="#date">
                        <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                             style="height:20px; margin-bottom: 3px" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>{{__('workshop.date')}}:
                        <span
                                id="case-status"> {{ $workshop_case->date }}</span></button>
                </li>

                <li>
                    <button data-toggle="modal" data-target="#invoiceable_after">
                        <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                             style="height:20px; margin-bottom: 3px" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>{{__('workshop.invoiceable_after')}}:
                        <span
                                id="case-status"> {{ optional($workshop_case->invoiceable_after)->format('Y-m') }}</span></button>
                </li>

                <li class="@if(is_null($workshop_case->start_time)) danger @endif">
                    <button data-toggle="modal" data-target="#start_time">
                        <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                             style="height:20px; margin-bottom: 3px" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg> {{__('workshop.start_time')}}: <span
                                id="case-status"> {{ $workshop_case->start_time }}</span></button>
                </li>
                <li class="@if(is_null($workshop_case->end_time)) danger @endif">
                    <button data-toggle="modal" data-target="#end_time">
                        <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                             style="height:20px; margin-bottom: 3px" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>{{__('workshop.end_time')}}:
                        <span id="case-status"> {{ $workshop_case->end_time }}</span></button>
                </li>
                <li>
                    <div> {{__('workshop.full_time')}}: <span
                                id="case-status"> {{ $workshop_case->full_time }}</span>
                    </div>
                </li>
                <li class="@if(is_null($workshop_case->topic)) danger @endif">
                    <button data-toggle="modal" data-target="#topic">
                        <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                             style="height:20px; margin-bottom: 3px" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg> {{__('workshop.workshop_theme')}}: <span
                                id="case-status"> {{ $workshop_case->topic }}</span></button>
                </li>
                <li class="@if(is_null($workshop_case->language)) danger @endif">
                    <button data-toggle="modal" data-target="#language">
                        <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                             style="height:20px; margin-bottom: 3px" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg> {{__('workshop.language')}}: <span
                                id="case-status"> {{ $workshop_case->language }}</span></button>
                </li>
                @if(optional(optional($workshop_case->org_data())->contract_holder)->id == 2)
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
                                    @if($workshop_case->number_of_participants)
                                        {{ $workshop_case->number_of_participants }}
                                    @else
                                        {{__('workshop.not_specified')}}
                                    @endif
                                </span>
                        </button>
                    </li>
                @endif

                @if(!empty($overall_feedback))
                <li>
                    <div>
                        @if($overall_feedback < 3)
                        <svg xmlns="http://www.w3.org/2000/svg" style="height: 20px; width: 20px; margin-bottom: 1px;   color: rgb(219,11,32);" class="mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        @endif
                        {{__('workshop.overall_feedback')}}: <span id="case-status"> {{ $overall_feedback }}</span>
                    </div>
                </li>
                @endif

                @if($workshop_case->invoice_data()->exists())
                    <li>

                        <div> {{__('common.closed_at')}}: <span id="case-status"> {{ Carbon\Carbon::parse($workshop_case->invoice_data->created_at)->format('Y-m-d') }}</span>
                        </div>
                    </li>
                @endif

                <li>
                    <div>
                        {{__('workshop.created_by')}}: {{optional($workshop_case->creator)->name}}
                    </div>
                </li>
            </ul>
        </div>
        <div class="col-4 col-lg-2 back-button mb-5">
            <a href="{{ session()->get('list_url') }}">{{__('common.back-to-list')}}</a>
        </div>
        <div class="col-8 col-lg-10 button-holder d-flex flex-column flex-lg-row align-items-start justify-content-end"
             style="text-align: right">
            @if($workshop_case->expert_status == \App\Enums\WorkshopCaseExpertStatus::EXPERT_PRICE_CHANGE && $workshop_case->status != \App\Enums\WorkshopCaseStatus::PRICE_ACCEPTED)
                <a href="{{route('admin.workshops.accept_expert_price', $workshop_case->id)}}"
                   class="button btn-radius d-flex" style="--btn-min-width: auto; ">{{__('workshop.accept_expert_offer')}}</a>
            @endif
            <div class="d-flex my-3 justify-content-between my-lg-0 justify-content-lg-end">
                @if(\Auth::user()->type == 'admin' || Auth::user()->type == 'account_admin')
                    <button onclick="deleteWorkshop()" style="background-color: #7c2469; display: flex!important; position: relative!important;"
                       class="button btn-radius mr-3">
                        <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                             style="height:20px; margin-bottom: 3px" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        {{__('common.delete')}}</button>
                @endif
                @if($workshop_case->status != \App\Enums\WorkshopCaseStatus::CLOSED)
                    <a @if(!empty($workshop_case->number_of_participants) ||optional(optional($workshop_case->org_data())->contract_holder)->id != 2) href="{{route('admin.workshops.close', $workshop_case->id)}}"
                       @else onclick="showAlert('{{__('workshop.number_of_participants')}} {{__('eap-online.required')}}!')"
                       @endif
                       class="button btn-radius position-relative" style="display: flex!important;">
                        <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                             style="height:20px; margin-bottom: 3px" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                        {{__('workshop.close_workshop')}}</a>
                @endif
            </div>
        </div>
    </div>

@endsection

@section('modal')
    <div class="modal" tabindex="-1" id="contract_price" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{__('workshop.contract_price')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post" action="{{route('admin.workshops.update', $workshop_case->id)}}">
                        @csrf
                        <div x-data="{free: {{(int)$workshop_case->workshop->free}}}">
                            <span>{{__('workshop.free')}}</span>
                            <select name="is_free" x-model="free" class="w-100">
                                <option value="1">{{__('common.yes')}}</option>
                                <option value="0">{{__('common.no')}}</option>
                            </select>
                            <div x-show="free == 0">
                                <input name="price" class="w-100 mt-3" type="text"
                                       value="{{$workshop_case->workshop->workshop_price}}"
                                       placeholder="{{__('other-activity.company_price')}}">
                                <select name="valuta" class="w-100 mr-0"
                                        style="float: right">
                                    <option @if(!$workshop_case->workshop->valuta) selected
                                            @endif value="">{{__('common.currency')}}</option>
                                    <option @if($workshop_case->workshop->valuta == "chf") selected
                                            @endif value="chf">
                                        CHF
                                    </option>
                                    <option @if($workshop_case->workshop->valuta == "czk") selected
                                            @endif value="czk">
                                        CZK
                                    </option>
                                    <option @if($workshop_case->workshop->valuta == "eur") selected
                                            @endif value="eur">
                                        EUR
                                    </option>
                                    <option @if($workshop_case->workshop->valuta == "huf") selected
                                            @endif value="huf">
                                        HUF
                                    </option>
                                    <option @if($workshop_case->workshop->valuta == "mdl") selected
                                            @endif value="mdl">
                                        MDL
                                    </option>
                                    <option @if($workshop_case->workshop->valuta == "oal") selected
                                            @endif value="oal">
                                        OAL
                                    </option>
                                    <option @if($workshop_case->workshop->valuta == "PLN") selected
                                            @endif value="pln">
                                        PLN
                                    </option>
                                    <option @if($workshop_case->workshop->valuta == "RON") selected
                                            @endif value="ron">
                                        RON
                                    </option>
                                    <option @if($workshop_case->workshop->valuta == "RSD") selected
                                            @endif value="rsd">
                                        RSD
                                    </option>
                                    <option @if($workshop_case->workshop->valuta == "USD") selected
                                            @endif value="usd">
                                        USD
                                    </option>
                                </select>
                            </div>
                        </div>
                        <input type="hidden" name="input" value="contract_price">
                        <button class="btn-radius" style="float: right; --btn-margin-right: 0px;">
                            <img class="mr-1" src="{{asset('assets/img/save.svg')}}" class="mr-1" style="height: 20px; width: 20px" alt="">
                            <span class="mt-1">{{__('common.save')}}</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" tabindex="-1" id="activity_id" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{__('workshop.activity_id')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post" action="{{route('admin.workshops.update', $workshop_case->id)}}">
                        <input type="text" value="{{ $workshop_case->activity_id }}"
                               name="activity_id">
                        <input type="hidden" name="input" value="activity_id">
                        {{csrf_field()}}
                        <button class="btn-radius" style="float: right; --btn-margin-right: 0px;">
                            <img class="mr-1" src="{{asset('assets/img/save.svg')}}" class="mr-1" style="height: 20px; width: 20px" alt="">
                            <span class="mt-1">{{__('common.save')}}</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" tabindex="-1" id="workshop_status" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{__('workshop.workshop_status_change_title')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="select_status" method="post"
                          action="{{route('admin.workshops.update', $workshop_case->id)}}">
                          <div class="w-100">
                            <select name="status" class="w-100 mr-0"
                          style="float: right">
                            <option @if($workshop_case->status == \App\Enums\WorkshopCaseStatus::OUTSOURCED) selected @endif value="1">
                                {{__('workshop.under_agreement')}}
                            </option>
                            <option @if($workshop_case->status == \App\Enums\WorkshopCaseStatus::PRICE_ACCEPTED ) selected @endif value="2">
                                {{__('workshop.active')}}
                            </option>
                            <option @if($workshop_case->status == \App\Enums\WorkshopCaseStatus::CLOSED) selected @endif value="3">
                                {{__('workshop.closed')}}
                            </option>
                        </select>
                        <input type="hidden" name="input" value="status">
                        {{csrf_field()}}
                        <button class="btn-radius" style="float: right; --btn-margin-right: 0px;">
                            <img class="mr-1" src="{{asset('assets/img/save.svg')}}" class="mr-1" style="height: 20px; width: 20px" alt="">
                            <span class="mt-1">{{__('common.save')}}</span>
                        </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" tabindex="-1" id="case-out-price" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{__('workshop.expert_out_price')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post" action="{{route('admin.workshops.update', $workshop_case->id)}}">
                        <div class="d-flex flex-column align-items-start">
                            <input placeholder="{{$expert_currency}}" type="text" value="{{ $workshop_case->expert_price }}" name="expert_price">
                            <input type="hidden" name="expert_currency" value="{{$expert_currency}}">
                        </div>
                        <input type="hidden" name="input" value="select_out_price">
                        {{csrf_field()}}
                        <button class="btn-radius" style="float: right; --btn-margin-right: 0px;">
                            <img class="mr-1" src="{{asset('assets/img/save.svg')}}" class="mr-1" style="height: 20px; width: 20px" alt="">
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
                    <h5 class="modal-title">{{__('workshop.company_email')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post" action="{{route('admin.workshops.update', $workshop_case->id)}}">
                        <input type="text" value="{{ $workshop_case->company_contact_email }}"
                               name="company_contact_email">
                        <input type="hidden" name="input" value="company_contact_email">
                        {{csrf_field()}}
                        <button class="btn-radius" style="float: right; --btn-margin-right: 0px;">
                            <img class="mr-1" src="{{asset('assets/img/save.svg')}}" class="mr-1" style="height: 20px; width: 20px" alt="">
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
                    <h5 class="modal-title">{{__('workshop.company_name')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post" action="{{route('admin.workshops.update', $workshop_case->id)}}">
                        <select name="company_id" id="company_id" class="w-100">
                            @foreach($companies as $company)
                                <option value="{{$company->id}}">{{$company->name}}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="input" value="company_id">
                        {{csrf_field()}}
                        <button class="btn-radius" style="float: right; --btn-margin-right: 0px;">
                            <img class="mr-1" src="{{asset('assets/img/save.svg')}}" class="mr-1" style="height: 20px; width: 20px" alt="">
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
                    <h5 class="modal-title">{{__('workshop.company_phone')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post" action="{{route('admin.workshops.update', $workshop_case->id)}}">
                        <input type="text" value="{{ $workshop_case->company_contact_phone }}"
                               name="company_contact_phone">
                        <input type="hidden" name="input" value="company_contact_phone">
                        {{csrf_field()}}
                        <button class="btn-radius" style="float: right; --btn-margin-right: 0px;">
                            <img class="mr-1" src="{{asset('assets/img/save.svg')}}" class="mr-1" style="height: 20px; width: 20px" alt="">
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
                    <h5 class="modal-title">{{__('workshop.country')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post" action="{{route('admin.workshops.update', $workshop_case->id)}}">
                        <select name="country_id">
                            <option value="">{{ __('workshop.select_country_pls') }}</option>
                            @foreach($countries as $country)
                                <option value="{{ $country->id }}"
                                        @if(optional($workshop_case->country)->id == $country->id) selected @endif>{{ $country->name }}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="input" value="country_id">
                        {{csrf_field()}}
                        <button class="btn-radius" style="float: right; --btn-margin-right: 0px;">
                            <img class="mr-1" src="{{asset('assets/img/save.svg')}}" class="mr-1" style="height: 20px; width: 20px" alt="">
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
                    <h5 class="modal-title">{{__('workshop.city')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post" action="{{route('admin.workshops.update', $workshop_case->id)}}">
                        <select name="city_id">
                            @foreach($cities as $city)
                                <option value="{{ $city->id }}"
                                        @if(optional($workshop_case->city)->id == $city->id) selected @endif>{{ $city->name }}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="input" value="city_id">
                        {{csrf_field()}}
                        <button class="btn-radius" style="float: right; --btn-margin-right: 0px;">
                            <img class="mr-1" src="{{asset('assets/img/save.svg')}}" class="mr-1" style="height: 20px; width: 20px" alt="">
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
                    <h5 class="modal-title">{{__('workshop.expert')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post" action="{{route('admin.workshops.update', $workshop_case->id)}}">
                        <select name="expert" class="w-100 mr-0">
                            @foreach($experts as $expert)
                                <option value="{{ $expert->id }}"
                                        @if(!empty($workshop_case->user) ? $workshop_case->user->id : '' == $expert->id) selected @endif>{{ $expert->name }}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="input" value="expert">
                        {{csrf_field()}}
                        <button class="btn-radius" style="float: right; --btn-margin-right: 0px;">
                            <img class="mr-1" src="{{asset('assets/img/save.svg')}}" class="mr-1" style="height: 20px; width: 20px" alt="">
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
                    <h5 class="modal-title">{{__('workshop.expert_phone')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post" action="{{route('admin.workshops.update', $workshop_case->id)}}">
                        <input type="text" value="{{ $workshop_case->expert_phone }}" name="expert_phone">
                        <input type="hidden" name="input" value="expert_phone">
                        {{csrf_field()}}
                        <button class="btn-radius" style="float: right; --btn-margin-right: 0px;">
                            <img class="mr-1" src="{{asset('assets/img/save.svg')}}" class="mr-1" style="height: 20px; width: 20px" alt="">
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
                    <h5 class="modal-title">{{__('workshop.date')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post" action="{{route('admin.workshops.update', $workshop_case->id)}}">
                        <input type="text" class="datepicker" value="{{ $workshop_case->date }}"
                               name="date">
                        <input type="hidden" name="input" value="date">
                        {{csrf_field()}}
                        <button class="btn-radius" style="float: right; --btn-margin-right: 0px;">
                            <img class="mr-1" src="{{asset('assets/img/save.svg')}}" class="mr-1" style="height: 20px; width: 20px" alt="">
                            <span class="mt-1">{{__('common.save')}}</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <div class="modal" tabindex="-1" id="invoiceable_after" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{__('workshop.invoiceable_after')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post" action="{{route('admin.workshops.update', $workshop_case->id)}}">
                        <input type="text" class="invoiceable_after" value="{{ optional($workshop_case->invoiceable_after)->format('Y-m') }}"
                               name="invoiceable_after">
                        <input type="hidden" name="input" value="invoiceable_after">
                        {{csrf_field()}}
                        <button class="btn-radius" style="float: right; --btn-margin-right: 0px;">
                            <img class="mr-1" src="{{asset('assets/img/save.svg')}}" class="mr-1" style="height: 20px; width: 20px" alt="">
                            <span class="mt-1">{{__('common.save')}}</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" tabindex="-1" id="start_time" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{__('workshop.start_time')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post" action="{{route('admin.workshops.update', $workshop_case->id)}}">
                        <input type="text" name="start_time" class="timepicker"
                               value="{{ $workshop_case->start_time }}">
                        <input type="hidden" name="end_time" value="{{ $workshop_case->end_time }}">
                        <input type="hidden" name="input" value="start_time">
                        {{csrf_field()}}
                        <button class="btn-radius" style="float: right; --btn-margin-right: 0px;">
                            <img class="mr-1" src="{{asset('assets/img/save.svg')}}" class="mr-1" style="height: 20px; width: 20px" alt="">
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
                    <h5 class="modal-title">{{__('workshop.end_time')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post" action="{{route('admin.workshops.update', $workshop_case->id)}}">
                        <input type="text" name="end_time" class="timepicker"
                               value="{{ $workshop_case->end_time }}">
                        <input type="hidden" name="start_time" value="{{ $workshop_case->start_time }}">
                        <input type="hidden" name="input" value="end_time">
                        {{csrf_field()}}
                        <button class="btn-radius" style="float: right; --btn-margin-right: 0px;">
                            <img class="mr-1" src="{{asset('assets/img/save.svg')}}" class="mr-1" style="height: 20px; width: 20px" alt="">
                            <span class="mt-1">{{__('common.save')}}</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" tabindex="-1" id="topic" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{__('workshop.workshop_theme')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post" action="{{route('admin.workshops.update', $workshop_case->id)}}">
                        <textarea name="topic" cols="30" rows="10">{{ $workshop_case->topic }}</textarea>
                        <input type="hidden" name="input" value="topic">
                        {{csrf_field()}}
                        <button class="btn-radius" style="float: right; --btn-margin-right: 0px;">
                            <img class="mr-1" src="{{asset('assets/img/save.svg')}}" class="mr-1" style="height: 20px; width: 20px" alt="">
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
                    <h5 class="modal-title">{{__('workshop.language')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post" action="{{route('admin.workshops.update', $workshop_case->id)}}">
                        <input type="text" name="language"
                               placeholder="{{ __('workshop.select_language') }}" required>
                        <input type="hidden" name="input" value="language">
                        {{csrf_field()}}
                        <button class="btn-radius" style="float: right; --btn-margin-right: 0px;">
                            <img class="mr-1" src="{{asset('assets/img/save.svg')}}" class="mr-1" style="height: 20px; width: 20px" alt="">
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
                    <form method="post" action="{{route('admin.workshops.update', $workshop_case->id)}}">
                        <input type="text" value="{{ $workshop_case->number_of_participants }}"
                               name="number_of_participants">
                        <input type="hidden" name="input" value="number_of_participants">
                        {{csrf_field()}}
                        <button class="btn-radius" style="float: right; --btn-margin-right: 0px;">
                            <img class="mr-1" src="{{asset('assets/img/save.svg')}}" class="mr-1" style="height: 20px; width: 20px" alt="">
                            <span class="mt-1">{{__('common.save')}}</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
