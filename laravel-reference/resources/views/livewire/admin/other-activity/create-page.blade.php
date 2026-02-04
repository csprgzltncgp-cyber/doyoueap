@push('livewire_js')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            flatpickr('.datepicker', {});

            flatpickr('.start_time', {
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true,
            });

            flatpickr('.end_time', {
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true,
            });

            Livewire.on('validationError', function (data) {
                Swal.fire({
                    title: data.message,
                    icon: 'error'
                });
            });
        });
    </script>
@endpush


<div class="row">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="{{asset('assets/css/cases/new.css')}}?t={{ time() }}">
    <link rel="stylesheet" href="{{asset('assets/css/workshops.css')}}?t={{ time() }}">

    <div class="col-12">
        {{ Breadcrumbs::render('other-activities.create') }}
        <h1>{{__('other-activity.title')}} - {{$title}}</h1>
    </div>

    <form wire:submit.prevent="save" class="col-12 col-lg-8">
        <div class="new-case-buttons row">
            <div class="col-12 steps" style="height: 64px!important">

                <button type="button"
                        wire:click="prevStep()"
                        class="@if(!$step > 0) d-none @endif col-12 col-lg-2 mb-1 mt-1 mb-lg-0 mt-lg-0 next-button active-button btn-radius"
                        style="--btn-min-width: auto; --btn-margin-right: 0px; --btn-height:100%; --btn-margin-bottom: 0px;">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 40px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m18.75 4.5-7.5 7.5 7.5 7.5m-6-15L5.25 12l7.5 7.5" />
                    </svg>
                </button>

                <select wire:model="otherActivity.type" class="@if($step != 0) d-none @endif col-12 col-lg-6 h-100">
                    <option value="-1" disabled selected>{{__('common.please-choose-one')}}</option>
                    <option value="{{ \App\Enums\OtherActivityType::TYPE_ORIENTATION->value }}">{{__('other-activity.types.1')}}</option>
                    <option value="{{ \App\Enums\OtherActivityType::TYPE_HEALTH_DAY->value }}">{{__('other-activity.types.2')}}</option>
                    <option value="{{ \App\Enums\OtherActivityType::TYPE_EXPERT_OUTPLACEMENT->value }}">{{__('other-activity.types.3')}}</option>
                </select>

                <select wire:model="otherActivity.permission_id" class="@if($step != 1) d-none @endif col-12 col-lg-6 h-100">
                    <option value="-1" disabled selected>{{__('common.please-choose-one')}}</option>
                    @foreach($permissions as $permission)
                        <option value="{{$permission->id}}">{{$permission->translation->value}}</option>
                    @endforeach
                </select>

                <select wire:model="otherActivity.country_id" class="@if($step != 2) d-none @endif col-12 col-lg-6 h-100">
                    <option value="-1" disabled selected>{{__('common.please-choose-one')}}</option>
                    @foreach($countries as $country)
                        <option value="{{$country->id}}">{{$country->name}}</option>
                    @endforeach
                </select>

                <select wire:model="otherActivity.company_id" class="@if($step != 3) d-none @endif col-12 col-lg-6 h-100">
                    <option value="-1" disabled selected>{{__('common.please-choose-one')}}</option>
                    @foreach($companies as $company)
                        <option value="{{$company->id}}">{{$company->name}}</option>
                    @endforeach
                </select>

                <input type="text" wire:model="otherActivity.activity_id"
                       class="@if($step != 4) d-none @endif col-12 col-lg-6 h-100"
                       placeholder="{{__('crisis.activity_id')}}">

                <select wire:model="otherActivity.city_id" class="@if($step != 5) d-none @endif col-12 col-lg-6 h-100">
                    <option value="-1" disabled selected>{{__('common.please-choose-one')}}</option>
                    @foreach($cities as $city)
                        <option value="{{$city->id}}">{{$city->name}}</option>
                    @endforeach
                </select>

                <select wire:model.defer="is_free_for_company" class="@if($step != 6) d-none @endif col-12 col-lg-6 h-100">
                    <option value="1">{{__('common.yes')}}</option>
                    <option value="0">{{__('common.no')}}</option>
                </select>

                <input type="number" wire:model.defer="otherActivity.company_price"
                       placeholder="{{__('workshop.contract_price')}}"
                       class="@if($step != 7) d-none @endif price col-12 col-lg-3 h-100"
                >
                <select wire:model.defer="otherActivity.company_currency"
                        class="@if($step != 7) d-none @endif valuta col-12 mt-1 mb-lg-0 mt-lg-0 col-lg-2 h-100">
                    <option value="">{{__('common.currency')}}</option>
                    <option value="chf">CHF</option>
                    <option value="czk">CZK</option>
                    <option value="eur">EUR</option>
                    <option value="huf">HUF</option>
                    <option value="mdl">MDL</option>
                    <option value="oal">OAL</option>
                    <option value="pln">PLN</option>
                    <option value="ron">RON</option>
                    <option value="rsd">RSD</option>
                    <option value="usd">USD</option>
                </select>

                <input type="number" wire:model.defer="otherActivity.company_phone"
                       class="@if($step != 8) d-none @endif col-12 col-lg-6 h-100"
                       placeholder="{{__('crisis.company_phone')}}">

                <input type="text" wire:model.defer="otherActivity.company_email"
                       class="@if($step != 9) d-none @endif col-12 col-lg-6 h-100"
                       placeholder="{{__('crisis.company_email')}}">

                <select wire:model.defer="otherActivity.user_id" class="@if($step != 10) d-none @endif col-12 col-lg-6 h-100">
                    <option value="-1" disabled selected>{{__('common.please-choose-one')}}</option>
                    @foreach($users as $user)
                        <option value="{{$user->id}}">{{$user->name}}</option>
                    @endforeach
                </select>

                <input type="number" wire:model.defer="otherActivity.user_phone"
                       class="@if($step != 11) d-none @endif col-12 col-lg-6 h-100"
                       placeholder="{{__('crisis.expert_phone')}}">


                <select wire:model.defer="is_free_for_user" class="@if($step != 12) d-none @endif col-12 col-lg-6 h-100">
                    <option value="1">{{__('common.yes')}}</option>
                    <option value="0">{{__('common.no')}}</option>
                </select>

                <input type="number" wire:model.defer="otherActivity.user_price"
                       placeholder="{{__('workshop.expert_out_price')}}"
                       class="@if($step != 13) d-none @endif price col-12 col-lg-3 h-100"
                >
                <select wire:model.defer="otherActivity.user_currency"
                        class="@if($step != 13) d-none @endif valuta col-12 mt-1 mb-lg-0 mt-lg-0 col-lg-2 h-100">
                    <option value="">{{__('common.currency')}}</option>
                    @foreach($currencies as $key => $value)
                        <option value="{{$key}}">{{$value}}</option>
                    @endforeach
                </select>

                <input type="text" wire:model.defer="otherActivity.language"
                       class="@if($step != 14) d-none @endif col-12 col-lg-6 h-100"
                       placeholder="{{__('crisis.language')}}">

                <input type="number" wire:model.defer="otherActivity.participants"
                       class="@if($step != 15) d-none @endif col-12 col-lg-6 h-100"
                       placeholder="{{__('crisis.number_of_participants')}}">

                <input type="text" wire:model.defer="otherActivity.date"
                       class="@if($step != 16) d-none @endif col-12 col-lg-6 h-100 datepicker"
                       placeholder="{{__('crisis.date')}}">

                <input type="text" wire:model.defer="otherActivity.start_time"
                       class="@if($step != 17) d-none @endif col-12 col-lg-6 h-100 start_time"
                       placeholder="{{__('crisis.start_time')}}">

                <input type="text" wire:model.defer="otherActivity.end_time"
                       class="@if($step != 18) d-none @endif col-12 col-lg-6 h-100 end_time"
                       placeholder="{{__('crisis.end_time')}}">

                <button type="submit"
                        class="@if($step != 19) d-none @endif next-button inactive-button col-12 col-lg-6 mb-1 mb-lg-0 mt-lg-0 h-100">
                    {{__('other-activity.save')}}
                </button>

                @if($step < $max_step)
                    <button type="button"
                            wire:click="nextStep()"
                            class="next-button active-button col-12 col-lg-2 mb-1 mt-1 mb-lg-0 mt-lg-0 btn-radius"
                            style="--btn-min-width: auto; --btn-height:100%; --btn-margin-bottom: 0px; --btn-margin-right: 0px;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:40px">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m5.25 4.5 7.5 7.5-7.5 7.5m6-15 7.5 7.5-7.5 7.5" />
                        </svg> 
                    </button>
                @endif

                @if($step > 1)
                    <button type="submit"
                            class="col-12 col-lg-2 mb-1 mb-lg-0 mt-lg-0 delete-button-from-list btn-radius"
                            style="--btn-min-width: 100px; --btn-margin-right: 0px; --btn-height:100%; --btn-margin-bottom: 0px;">
                        <span class="mt-1">{{__('common.save')}}</span>
                    </button>
                @endif
            </div>
        </div>
    </form>
    <div class="col-12 col-lg-4">
        <div id="permissions" class="right-side">
            <p class="title">{{__('other-activity.attributes')}}:</p>
            <div class="workshop-data">
                <span>{{ __('other-activity.type') }}: <span
                    style="color: rgb(0,87,95)">{{ Lang::has('other-activity.types.'.optional($otherActivity->type)->value) ? __('other-activity.types.'.optional($otherActivity->type)->value) : '' }}</span></span><br>
                <span>{{ __('other-activity.permission_id') }}: <span
                        style="color: rgb(0,87,95)">{{ optional(optional($permissions->find($otherActivity->permission_id))->translation)->value }}</span></span><br>
                <span>{{ __('common.country') }}: <span
                            style="color: rgb(0,87,95)">{{optional($otherActivity->country)->name}}</span></span><br>
                <span>{{ __('workshop.company_name') }}: <span
                            style="color: rgb(0,87,95)">{{optional($otherActivity->company)->name}}</span></span><br>
                <span>{{ __('workshop.activity_id') }}: <span
                            style="color: rgb(0,87,95)">{{$otherActivity->activity_id}}</span></span><br>
                <span>{{ __('workshop.city') }}: <span
                            style="color: rgb(0,87,95)">{{optional($otherActivity->city)->name}}</span></span><br>
                <span>{{ __('workshop.contract_price') }}:
                    <span style="color: rgb(0,87,95)">
                        @if($is_free_for_company)
                            {{__('workshop.free')}}
                        @else
                            {{$otherActivity->company_price . ' ' . $otherActivity->company_currency}}
                        @endif
                    </span></span><br>
                <span>{{ __('workshop.company_phone') }}: <span
                            style="color: rgb(0,87,95)">{{$otherActivity->company_phone}}</span></span><br>
                <span>{{ __('workshop.company_email') }}: <span
                            style="color: rgb(0,87,95)">{{$otherActivity->company_email}}</span></span><br>
                <span>{{ __('workshop.expert') }}: <span
                            style="color: rgb(0,87,95)">{{optional($otherActivity->user)->name}}</span></span><br>
                <span>{{ __('workshop.expert_email') }}: <span
                            style="color: rgb(0,87,95)">{{optional($otherActivity->user)->email}}</span></span><br>
                <span>{{ __('workshop.expert_phone') }}: <span
                            style="color: rgb(0,87,95)">{{$otherActivity->user_phone}}</span></span><br>
                <span>{{ __('workshop.expert_out_price') }}:
                    <span style="color: rgb(0,87,95)">
                        @if($is_free_for_user)
                            {{__('workshop.free')}}
                        @else
                            {{$otherActivity->user_price . ' ' . $otherActivity->user_currency}}
                        @endif
                    </span></span><br>
                <span>{{ __('workshop.language') }}: <span style="color: rgb(0,87,95)">{{$otherActivity->language}}</span></span><br>
                <span>{{ __('workshop.number_of_participants') }}: <span
                            style="color: rgb(0,87,95)">{{$otherActivity->participants}}</span></span><br>
                <span>{{ __('workshop.date') }}: <span
                            style="color: rgb(0,87,95)">{{$otherActivity->date}}</span></span><br>
                <span>{{ __('workshop.start_time') }}: <span
                            style="color: rgb(0,87,95)">{{$otherActivity->start_time}}</span></span><br>
                <span>{{ __('workshop.end_time') }}: <span style="color: rgb(0,87,95)">{{$otherActivity->end_time}}</span></span><br>
                <span>{{ __('workshop.full_time') }}: <span
                            style="color: rgb(0,87,95)">{{elapsed_time($otherActivity->start_time, $otherActivity->end_time)}}</span></span><br>
            </div>
        </div>
    </div>
</div>
