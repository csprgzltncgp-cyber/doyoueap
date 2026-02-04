@push('livewire_js')
    <script>
        $(function () {
            Livewire.on('invalid_file_extension', function () {
                Swal.fire({
                    title: "{{__('other-activity.invalid_file_extension')}}",
                    icon: 'error'
                });
            });
        });

        function showModal(id) {
            setTimeout(function () {
                $('#' + id).modal('show');
            }, 200)
        }
    </script>
@endpush

<div>
    <link rel="stylesheet" href="{{asset('assets/css/cases/view.css')}}?t={{time()}}">
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

        li.warning {
            background-color: #f2da2f !important;
            color: #fff !important;
        }
    </style>


    <div class="row">
        <div class="col-12">
            <h1>{{__('other-activity.view')}}</h1>
        </div>
        <div class="col-12 case-title">
            <p>
                {{__('workshop.created_at')}} {{$otherActivity->created_at}} -
                {{optional(optional($otherActivity)->company)->name}} -
                {{optional(optional($otherActivity)->country)->name}}
            </p>
        </div>
        <div class="col-12 case-details">
            <ul>
                <li>
                    <button>
                        {{__('other-activity.activity_id')}}:
                        <span id="case-status"> {{ $otherActivity->activity_id }}</span></button>
                </li>

                <li>
                    <button>
                        {{__('other-activity.type')}}:
                        <span id="case-status"> {{ __('other-activity.types.'.$otherActivity->type->value) }}</span></button>
                </li>
                <li>
                    <button>
                        {{__('other-activity.permission_id')}}:
                        <span id="case-status"> {{ optional(optional($permissions->find($otherActivity->permission_id))->translation)->value }}</span></button>
                </li>

                <li>
                    <button>
                        {{__('other-activity.company_id')}}:
                        <span id="case-status"> {{ optional(optional($otherActivity)->company)->name }}</span></button>
                </li>

                <li>
                    <button> {{__('other-activity.contract_holder_id')}}: <span
                                id="case-status"> {{ optional(optional($otherActivity->org_data())->contract_holder)->name }}</span>
                    </button>
                </li>


                @if(empty($otherActivity->user_price) && empty($otherActivity->user_currency))
                    <li>
                        <span>
                            {{__('workshop.expert_out_price')}}: <span
                                    id="case-status"> {{__('workshop.free')}}</span></span>
                    </li>
                @else
                    <li @if($otherActivity->event && $otherActivity->event->type == \App\Models\OtherActivityEvent::TYPE_OTHER_ACTIVITY_PRICE_MODIFIED_BY_ADMIN) class="warning" @endif>
                        <button @if($otherActivity->status == \App\Enums\OtherActivityStatus::STATUS_OUTSOURCED) onclick="showModal('user_price')"
                                wire:click="edit('user_price')" @endif>
                            @if($otherActivity->status == \App\Enums\OtherActivityStatus::STATUS_OUTSOURCED)
                                <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                                     style="height:20px; margin-bottom: 3px" fill="none"
                                     viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            @endif
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
                    <button>
                        {{__('workshop.company_email')}}:
                        <span id="case-status"> {{ $otherActivity->company_email }}</span></button>
                </li>
                <li class="@if(is_null($otherActivity->company_phone)) danger @endif">
                    <button>
                        {{__('workshop.company_phone')}}: <span
                                id="case-status"> {{ $otherActivity->company_phone }}</span>
                    </button>
                </li>
                <li class="@if(is_null($otherActivity->country)) danger @endif">
                    <button>
                        {{__('other-activity.country_id')}}: <span
                                id="case-status"> {{ optional($otherActivity->country)->name}}</span></button>
                </li>
                <li class="@if(is_null($otherActivity->city)) danger @endif">
                    <button>
                        {{__('workshop.city')}}:
                        <span
                                id="case-status"> {{ optional($otherActivity->city)->name }}</span></button>
                </li>
                <li class="@if(is_null($otherActivity->date)) danger @endif">
                    <button>
                        {{__('workshop.date')}}:
                        <span
                                id="case-status"> {{ $otherActivity->date }}</span></button>
                </li>
                <li class="@if(is_null($otherActivity->start_time)) danger @endif">
                    <button>
                        {{__('workshop.start_time')}}: <span
                                id="case-status"> {{ $otherActivity->start_time ? \Carbon\Carbon::parse($otherActivity->start_time)->format('H:i') : '' }}</span>
                    </button>
                </li>
                <li class="@if(is_null($otherActivity->end_time)) danger @endif">
                    <button>
                        {{__('workshop.end_time')}}: <span
                                id="case-status"> {{ $otherActivity->end_time ? \Carbon\Carbon::parse($otherActivity->end_time)->format('H:i') : '' }}</span>
                    </button>
                </li>
                <li>
                    <button> {{__('workshop.full_time')}}: <span
                                id="case-status"> {{ elapsed_time($otherActivity->start_time, $otherActivity->end_time)}}</span>
                    </button>
                </li>
                <li class="@if(is_null($otherActivity->language)) danger @endif">
                    <button>
                        {{__('workshop.language')}}: <span
                                id="case-status"> {{ $otherActivity->language }}</span></button>
                </li>
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
            </ul>
        </div>
        <div class="col-4 col-lg-2 back-button mb-5">
            <a href="{{ session()->get('list_url') }}">{{__('common.back-to-list')}}</a>
        </div>
        <div class="col-8 col-lg-10 button-holder d-flex flex-column flex-lg-row align-items-start justify-content-end"
             style="text-align: right">
            <div class="row">
                @if($otherActivity->status == \App\Enums\OtherActivityStatus::STATUS_OUTSOURCED && !($otherActivity->event && $otherActivity->event->type == \App\Models\OtherActivityEvent::TYPE_OTHER_ACTIVITY_PRICE_MODIFIED_BY_EXPERT))
                    <button wire:click="accept"
                            class="button">
                        <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                             style="height:20px; margin-bottom: 3px" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{__('workshop.accept')}}</button>

                    <button wire:click="deny"
                            class="button">
                        <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                             style="height:20px; margin-bottom: 3px" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor"
                             stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{__('workshop.denied')}}</button>
                @endif
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
                            @if($currently_editing == 'participants')
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
                                <select wire:model.defer="otherActivity.user_currency" class="w-100 mr-0"
                                        style="float: right">
                                    <option @if(!$otherActivity->user_currency) selected
                                            @endif value="">{{__('common.currency')}}</option>
                                    <option @if($otherActivity->user_currency == "chf") selected @endif value="chf">
                                        CHF
                                    </option>
                                    <option @if($otherActivity->user_currency == "czk") selected @endif value="czk">
                                        CZK
                                    </option>
                                    <option @if($otherActivity->user_currency == "eur") selected @endif value="eur">
                                        EUR
                                    </option>
                                    <option @if($otherActivity->user_currency == "huf") selected @endif value="huf">
                                        HUF
                                    </option>
                                    <option @if($otherActivity->user_currency == "mdl") selected @endif value="mdl">
                                        MDL
                                    </option>
                                    <option @if($otherActivity->user_currency == "oal") selected @endif value="oal">
                                        OAL
                                    </option>
                                    <option @if($otherActivity->user_currency == "PLN") selected @endif value="pln">
                                        PLN
                                    </option>
                                    <option @if($otherActivity->user_currency == "RON") selected @endif value="ron">
                                        RON
                                    </option>
                                    <option @if($otherActivity->user_currency == "RSD") selected @endif value="rsd">
                                        RSD
                                    </option>
                                    <option @if($otherActivity->user_currency == "USD") selected @endif value="usd">
                                        USD
                                    </option>
                                </select>
                            @endif

                            <button data-dismiss="modal" aria-label="Close" class="mr-0 btn-radius" wire:click="save"
                                    style="float: right; --btn-margin-right: 0px;">
                                <img class="mr-1" src="{{asset('assets/img/save.svg')}}" class="mr-1" style="height: 20px; width: 20px" alt="">
                                <span class="mt-1">{{__('common.save')}}</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
