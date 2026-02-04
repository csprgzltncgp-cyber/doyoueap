@push('livewire_js')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        $(function () {
            Livewire.on('expertOutsourced', function () {
                Swal.fire({
                    title: "{{__('other-activity.outsourced_success')}}",
                    icon: 'info'
                });
            });

            Livewire.on('invalid_file_extension', function () {
                Swal.fire({
                    title: "{{__('other-activity.invalid_file_extension')}}",
                    icon: 'error'
                });
            });

            Livewire.on('expert_currency_missing', function () {
                Swal.fire({
                    title: "{{__('other-activity.missing_currency')}}",
                    icon: 'error'
                });
            });

            Livewire.on('expert_payment_missing', function () {
                Swal.fire({
                    title: "{{__('other-activity.missing_payment_data')}}",
                    icon: 'error'
                });
            });
        });

        function deleteOtherActivity() {
            Swal.fire({
                title: '{{__('common.are-you-sure-to-delete')}}',
                text: '{{__('common.operation-cannot-undone')}}',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '{{__('common.yes-delete-it')}}'
            }).then((result) => {
                if (result.value) {
                    Livewire.emit('deleteOtherActivity')
                }
            });
        }

        function showAlert(message) {
            Swal.fire({
                title: message,
                icon: 'info'
            });
        }

        function showModal(id) {
            setTimeout(function () {
                $('#' + id).modal('show');
            }, 200)
        }
    </script>
@endpush



<div>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="{{asset('assets/css/cases/view.css')}}?t={{time()}}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
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

        .select_status select {
            flex: 1;
        }
    </style>


    <div class="row">
        <div class="col-12">
            {{ Breadcrumbs::render('other-activities.show', $otherActivity) }}
            <h1>{{__('other-activity.view')}}</h1>
        </div>
        <div class="col-12 case-title">
            <p>
                {{__('workshop.created_at')}} {{$otherActivity->created_at}} -
                {{$otherActivity->company->name}} -
                {{$otherActivity->country->name}}
            </p>
        </div>
        <div class="col-12 case-details">
            <ul>
                <li>
                    <button onclick="showModal('activity_id')" wire:click="edit('activity_id')">
                        <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                             style="height:20px; margin-bottom: 3px" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg> {{__('other-activity.activity_id')}}:
                        <span id="case-status"> {{ $otherActivity->activity_id }}</span></button>
                </li>
                <li>
                    <button onclick="showModal('type')" wire:click="edit('type')">
                        <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                             style="height:20px; margin-bottom: 3px" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg> {{__('other-activity.type')}}:
                        <span id="case-status"> {{ __('other-activity.types.'.$otherActivity->type->value) }}</span></button>
                </li>
                <li>
                    <button onclick="showModal('permission_id')" wire:click="edit('permission_id')">
                        <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                             style="height:20px; margin-bottom: 3px" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg> {{__('other-activity.permission_id')}}:
                        <span id="case-status"> {{ optional(optional($permissions->find($otherActivity->permission_id))->translation)->value }}</span></button>
                </li>
                <li>
                    <div>
                        {{__('workshop.status')}}:
                        <span id="case-status">
                            @if($otherActivity->status == \App\Enums\OtherActivityStatus::STATUS_OUTSOURCED)
                                {{__('workshop.under_agreement')}}
                            @elseif($otherActivity->status == \App\Enums\OtherActivityStatus::STATUS_IN_PROGRESS)
                                {{__('workshop.active')}}
                            @else
                                {{__('workshop.closed')}}
                            @endif
                        </span>
                    </div>
                </li>
                <li>
                    <button onclick="showModal('company_id')" wire:click="edit('company_id')">
                        <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                             style="height:20px; margin-bottom: 3px" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        {{__('other-activity.company_id')}}:
                        <span id="case-status"> {{ $otherActivity->company->name }}</span>
                    </button>
                </li>
                <li>
                    <button> {{__('other-activity.contract_holder_id')}}: <span
                                id="case-status"> {{ optional(optional($otherActivity->org_data())->contract_holder)->name }}</span>
                    </button>
                </li>
                <li>
                    <button> {{__('workshop.contract_date')}}: <span
                                id="case-status"> {{ optional($otherActivity->org_data())->contract_date}}</span>
                    </button>
                </li>

                <li>
                    <button onclick="showModal('company_price')"
                            wire:click="edit('company_price')">
                        <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                             style="height:20px; margin-bottom: 3px" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        {{__('workshop.contract_price')}}:
                        @if(!empty($otherActivity->company_price))
                            <span id="case-in-price">
                                    {{ $otherActivity->company_price }}
                                <span style="text-transform: uppercase">
                                    {{$otherActivity->company_currency}}
                                </span>
                            </span>
                        @else
                            <span id="case-in-price">{{__('workshop.free')}}</span>
                        @endif
                    </button>
                </li>

                @if(empty($otherActivity->user_price) && empty($otherActivity->user_currency))
                    <li>
                        <button onclick="showModal('user_price')" wire:click="edit('user_price')">
                            <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                                 style="height:20px; margin-bottom: 3px" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            {{__('workshop.expert_out_price')}}: <span
                                    id="case-status"> {{__('workshop.free')}}</span></button>
                    </li>
                @else
                    <li @if($otherActivity->event && $otherActivity->event->type == \App\Models\OtherActivityEvent::TYPE_OTHER_ACTIVITY_PRICE_MODIFIED_BY_EXPERT) class="warning" @endif>
                        <button onclick="showModal('user_price')" wire:click="edit('user_price')">
                            <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                                 style="height:20px; margin-bottom: 3px" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            {{__('workshop.expert_out_price')}}:
                            <span id="case-in-price">
                                    {{ $otherActivity->user_price }}
                                <span style="text-transform: uppercase">
                                    {{$otherActivity->user_currency}}
                                </span>
                            </span>
                        </button>
                    </li>
                @endif

                <li class="@if(is_null($otherActivity->company_email)) danger @endif">
                    <button onclick="showModal('company_email')" wire:click="edit('company_email')">
                        <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                             style="height:20px; margin-bottom: 3px" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg> {{__('workshop.company_email')}}:
                        <span id="case-status"> {{ $otherActivity->company_email }}</span></button>
                </li>
                <li class="@if(is_null($otherActivity->company_phone)) danger @endif">
                    <button onclick="showModal('company_phone')" wire:click="edit('company_phone')">
                        <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                             style="height:20px; margin-bottom: 3px" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg> {{__('workshop.company_phone')}}: <span
                                id="case-status"> {{ $otherActivity->company_phone }}</span>
                    </button>
                </li>
                <li class="@if(is_null($otherActivity->country)) danger @endif">
                    <button onclick="showModal('country_id')" wire:click="edit('country_id')">
                        <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                             style="height:20px; margin-bottom: 3px" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg> {{__('other-activity.country_id')}}: <span
                                id="case-status"> {{ optional($otherActivity->country)->name}}</span></button>
                </li>
                <li class="@if(is_null($otherActivity->city)) danger @endif">
                    <button onclick="showModal('city_id')" wire:click="edit('city_id')">
                        <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                             style="height:20px; margin-bottom: 3px" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg> {{__('workshop.city')}}:
                        <span
                                id="case-status"> {{ optional($otherActivity->city)->name }}</span></button>
                </li>
                <li class="@if(is_null($otherActivity->user)) danger @endif">
                    <button @if($is_outsorceable) onclick="showModal('user_id')" wire:click="edit('user_id')"
                            @else onclick="showAlert('{{__('workshop.outsource_error')}}')" @endif>
                        <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                             style="height:20px; margin-bottom: 3px" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        {{__('workshop.expert')}}: <span
                                id="case-status"> {{ !empty($otherActivity->user) ? $otherActivity->user->name : '' }}</span>
                    </button>
                </li>
                <li>
                    <button> {{__('workshop.expert_email')}}: <span
                                id="case-status"> {{ !empty($otherActivity->user) ? $otherActivity->user->email : '' }}</span>
                    </button>
                </li>
                <li>
                    <button onclick="showModal('user_phone')" wire:click="edit('user_phone')">
                        <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                             style="height:20px; margin-bottom: 3px" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg> {{__('workshop.expert_phone')}}: <span
                                id="case-status"> {{ $otherActivity->user_phone }}</span></button>
                </li>
                <li class="@if(is_null($otherActivity->date)) danger @endif">
                    <button onclick="showModal('date')" wire:click="edit('date')">
                        <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                             style="height:20px; margin-bottom: 3px" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>{{__('workshop.date')}}:
                        <span
                                id="case-status"> {{ $otherActivity->date }}</span></button>
                </li>
                <li class="@if(is_null($otherActivity->start_time)) danger @endif">
                    <button onclick="showModal('start_time')" wire:click="edit('start_time')">
                        <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                             style="height:20px; margin-bottom: 3px" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg> {{__('workshop.start_time')}}: <span
                                id="case-status"> {{ $otherActivity->start_time ? \Carbon\Carbon::parse($otherActivity->start_time)->format('H:i') : ''}}</span>
                    </button>
                </li>
                <li class="@if(is_null($otherActivity->end_time)) danger @endif">
                    <button onclick="showModal('end_time')" wire:click="edit('end_time')">
                        <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                             style="height:20px; margin-bottom: 3px" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg> {{__('workshop.end_time')}}: <span
                                id="case-status"> {{ $otherActivity->end_time ? \Carbon\Carbon::parse($otherActivity->end_time)->format('H:i') : '' }}</span>
                    </button>
                </li>
                <li>
                    <button> {{__('workshop.full_time')}}: <span
                                id="case-status"> {{ elapsed_time($otherActivity->start_time, $otherActivity->end_time)}}</span>
                    </button>
                </li>
                <li class="@if(is_null($otherActivity->language)) danger @endif">
                    <button onclick="showModal('language')" wire:click="edit('language')">
                        <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                             style="height:20px; margin-bottom: 3px" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg> {{__('workshop.language')}}: <span
                                id="case-status"> {{ $otherActivity->language }}</span></button>
                </li>
                @if(optional(optional($otherActivity->org_data())->contract_holder)->id == 2)
                    <li>
                        <button onclick="showModal('participants')" wire:click="edit('participants')">
                            <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                                 style="height:20px; margin-bottom: 3px" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            {{__('workshop.number_of_participants')}}: <span id="case-status">
                    @if($otherActivity->participants)
                                    {{ $otherActivity->participants }}
                                @else
                                    {{__('workshop.not_specified')}}
                                @endif
                </span></button>
                    </li>
                @endif

                @if(!empty($otherActivity->closed_at))
                <li>
                    <div>
                        {{__('common.closed_at')}}: <span id="case-status"> {{ Carbon\Carbon::parse($otherActivity->closed_at)->format('Y-m-d') }}</span>
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
            <div class="row">
                @if($this->otherActivity->event && $this->otherActivity->event->type == \App\Models\OtherActivityEvent::TYPE_OTHER_ACTIVITY_PRICE_MODIFIED_BY_EXPERT)
                    <button wire:click="accept_user_price"
                            class="button btn-radius">
                        <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                             style="height:20px; margin-bottom: 3px" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{__('workshop.accept_expert_offer')}}</button>
                @endif

                @if($otherActivity->status == \App\Enums\OtherActivityStatus::STATUS_IN_PROGRESS || (optional(optional($otherActivity->user)->expert_data)->is_cgp_employee && $otherActivity->status != \App\Enums\OtherActivityStatus::STATUS_CLOSED))
                    <a @if(!empty($otherActivity->participants) || optional(optional($otherActivity->org_data())->contract_holder)->id != 2) wire:click="close"
                       @else
                           onclick="showAlert('{{__('workshop.number_of_participants')}} {{__('eap-online.required')}}!')"
                       @endif
                       class="button position-relative btn-radius d-flex">
                        <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                             style="height:20px; margin-bottom: 3px" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                        {{__('workshop.close_workshop')}}</a>
                @endif

                <button onclick="deleteOtherActivity()" style="background-color: #7c2469;"
                        class="button btn-radius position-relative">
                    <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                         style="height:20px; margin-bottom: 3px" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    {{__('common.delete')}}</button>
            </div>
        </div>

        <div class="modal fade" role="dialog" id="{{$currently_editing}}">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{__('other-activity.'. $currently_editing)}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form class="">
                            @if(in_array($currently_editing, ['activity_id', 'company_email', 'company_phone', 'user_phone', 'language', 'participants']))
                                <input class="w-100" type="text"
                                       wire:model.defer="{{'otherActivity.' . $currently_editing}}">
                            @endif

                            @if($currently_editing == 'type')
                                <select wire:model.defer="otherActivity.type" class="w-100 mr-0"
                                        style="float: right">
                                    <option value="{{ \App\Enums\OtherActivityType::TYPE_ORIENTATION->value }}"
                                        @if ($otherActivity->type == \App\Enums\OtherActivityType::TYPE_ORIENTATION)
                                            selected
                                        @endif>{{__('other-activity.types.1')}}</option>
                                    <option value="{{ \App\Enums\OtherActivityType::TYPE_HEALTH_DAY->value }}"
                                        @if ($otherActivity->type == \App\Enums\OtherActivityType::TYPE_HEALTH_DAY)
                                            selected
                                        @endif>{{__('other-activity.types.2')}}</option>
                                    <option value="{{ \App\Enums\OtherActivityType::TYPE_EXPERT_OUTPLACEMENT->value }}"
                                        @if ($otherActivity->type == \App\Enums\OtherActivityType::TYPE_EXPERT_OUTPLACEMENT)
                                            selected
                                        @endif>{{__('other-activity.types.3')}}</option>
                                </select>
                            @endif

                            @if($currently_editing == 'permission_id')
                                <select wire:model.defer="otherActivity.permission_id" class="w-100 mr-0"
                                        style="float: right">
                                    @foreach($permissions as $permission)
                                        <option value="{{$permission->id}}" @if ($otherActivity->permission_id == $permission->id) selected @endif>{{$permission->translation->value}}</option>
                                    @endforeach
                                </select>
                            @endif

                            @if($currently_editing == 'user_price')
                                <input class="w-100" type="text"
                                       wire:model.defer="{{'otherActivity.' . $currently_editing}}">
                                <input type="hidden" wire:model.defer="otherActivity.user_currency">
                            @endif

                            @if($currently_editing == 'company_price')
                                <div x-data="{free: {{$isFreeForCompany}}}">
                                    <span>{{__('workshop.free')}}</span>
                                    <select wire:model.defer="isFreeForCompany" class="w-100" x-model="free">
                                        <option value="1">{{__('common.yes')}}</option>
                                        <option value="0">{{__('common.no')}}</option>
                                    </select>
                                    <div x-show="free == 0">
                                        <input class="w-100 mt-3" type="text"
                                               wire:model.defer="{{'otherActivity.' . $currently_editing}}"
                                               placeholder="{{__('other-activity.company_price')}}">
                                        <select wire:model.defer="otherActivity.company_currency" class="w-100 mr-0"
                                                style="float: right">
                                            <option @if(!$otherActivity->user_currency) selected
                                                    @endif value="">{{__('common.currency')}}</option>
                                            <option @if($otherActivity->user_currency == "chf") selected
                                                    @endif value="chf">
                                                CHF
                                            </option>
                                            <option @if($otherActivity->user_currency == "czk") selected
                                                    @endif value="czk">
                                                CZK
                                            </option>
                                            <option @if($otherActivity->user_currency == "eur") selected
                                                    @endif value="eur">
                                                EUR
                                            </option>
                                            <option @if($otherActivity->user_currency == "huf") selected
                                                    @endif value="huf">
                                                HUF
                                            </option>
                                            <option @if($otherActivity->user_currency == "mdl") selected
                                                    @endif value="mdl">
                                                MDL
                                            </option>
                                            <option @if($otherActivity->user_currency == "oal") selected
                                                    @endif value="oal">
                                                OAL
                                            </option>
                                            <option @if($otherActivity->user_currency == "PLN") selected
                                                    @endif value="pln">
                                                PLN
                                            </option>
                                            <option @if($otherActivity->user_currency == "RON") selected
                                                    @endif value="ron">
                                                RON
                                            </option>
                                            <option @if($otherActivity->user_currency == "RSD") selected
                                                    @endif value="rsd">
                                                RSD
                                            </option>
                                            <option @if($otherActivity->user_currency == "USD") selected
                                                    @endif value="usd">
                                                USD
                                            </option>
                                        </select>
                                    </div>
                                </div>

                            @endif

                            @if($currently_editing == 'start_time' || $currently_editing == 'end_time')
                                <script>
                                    flatpickr('.timepicker', {
                                        enableTime: true,
                                        noCalendar: true,
                                        dateFormat: "H:i",
                                        time_24hr: true,
                                    });
                                </script>
                                <input class="w-100 timepicker" type="text"
                                       wire:model.defer="{{'otherActivity.' . $currently_editing}}">
                            @endif

                            @if($currently_editing == 'date')
                                <script>
                                    flatpickr('.datepicker', {});
                                </script>
                                <input class="w-100 datepicker" type="text"
                                       wire:model.defer="{{'otherActivity.' . $currently_editing}}">
                            @endif

                            @if($currently_editing == 'status')
                                <select class="w-100" wire:model.defer="{{'otherActivity.' . $currently_editing}}">
                                    <option value="{{\App\Enums\OtherActivityStatus::STATUS_OUTSOURCED}}">{{__("workshop.under_agreement")}}</option>
                                    <option value="{{\App\Enums\OtherActivityStatus::STATUS_IN_PROGRESS}}">{{__("workshop.active")}}</option>
                                    <option value="{{\App\Enums\OtherActivityStatus::STATUS_CLOSED}}">{{__("workshop.closed")}}</option>
                                </select>
                            @endif

                            @if($currently_editing == 'company_id')
                                <select class="w-100" wire:model.defer="{{'otherActivity.' . $currently_editing}}">
                                    @foreach($companies as $company)
                                        <option value="{{$company->id}}"
                                                @if($otherActivity->company && $company->id == $otherActivity->company->id) selected @endif>{{$company->name}}</option>
                                    @endforeach
                                </select>
                            @endif

                            @if($currently_editing == 'country_id')
                                <select class="w-100" wire:model.defer="{{'otherActivity.' . $currently_editing}}">
                                    @foreach($countries as $country)
                                        <option value="{{$country->id}}"
                                                @if($otherActivity->country && $country->id == $otherActivity->country->id) selected @endif>{{$country->name}}</option>
                                    @endforeach
                                </select>
                            @endif

                            @if($currently_editing == 'city_id')
                                <select class="w-100" wire:model.defer="{{'otherActivity.' . $currently_editing}}">
                                    @foreach($cities as $city)
                                        <option value="{{$city->id}}"
                                                @if($otherActivity->city && $city->id == $otherActivity->city->id) selected @endif>{{$city->name}}</option>
                                    @endforeach
                                </select>
                            @endif

                            @if($currently_editing == 'user_id')
                                <select class="w-100" wire:model.defer="{{'otherActivity.' . $currently_editing}}">
                                    @foreach($experts as $expert)
                                        <option value="{{$expert->id}}"
                                                @if($otherActivity->user && $expert->id == $otherActivity->user->id) selected @endif>{{$expert->name}}</option>
                                    @endforeach
                                </select>
                            @endif

                            <button data-dismiss="modal" aria-label="Close" class="mr-0 btn-radius" wire:click="save"
                                    style="float: right; --btn-margin-right: 0px;">
                                <img class="mr-1" style="width:20px;" src="{{asset('assets/img/save.svg')}}">
                                <span>{{__('common.save')}}</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
