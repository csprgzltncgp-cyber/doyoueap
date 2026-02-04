<div class="row">
    <div class="col-12">
        <h1>{{$activity_plan_category->name}} - {{$title}}</h1>
    </div>

    <form wire:submit.prevent="save" class="col-12 col-lg-8">
        <div class="new-case-buttons row" >
            <div class="col-12 steps" style="height: 64px!important">
                @if($step > 0)
                    <button type="button"
                            wire:click="prevStep()"
                            class="col-12 col-lg-2 mb-1 mt-1 mb-lg-0 mt-lg-0 next-button active-button btn-radius"
                            style="--btn-min-width: auto; --btn-margin-right: 0px; --btn-height:100%; --btn-margin-bottom: 0px;">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 40px;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m18.75 4.5-7.5 7.5 7.5 7.5m-6-15L5.25 12l7.5 7.5" />
                            </svg>                              
                    </button>
                @endif

                @foreach($fields as $index => $field)
                    @switch($field['type'])
                        @case(App\Enums\ActivityPlanCategoryFieldTypeEnum::TEXT->value)
                        <input type="text" wire:model.defer="field_values.{{$field['id']}}"
                            class="@if($step != $index) d-none @endif col-12 col-lg-8 h-100"
                            placeholder="{{$field['name']}}">
                            @break

                        @case(App\Enums\ActivityPlanCategoryFieldTypeEnum::NUMBER->value)
                        <input type="number" wire:model.defer="field_values.{{$field['id']}}"
                            class="@if($step != $index) d-none @endif col-12 col-lg-8 h-100"
                            placeholder="{{$field['name']}}">
                            @break
                        @case(App\Enums\ActivityPlanCategoryFieldTypeEnum::DATE->value)
                        <input type="text" wire:model.defer="field_values.{{$field['id']}}"
                            class="@if($step != $index) d-none @endif col-12 col-lg-8 h-100 datepicker"
                            placeholder="{{$field['name']}}">
                            @break
                        @case(App\Enums\ActivityPlanCategoryFieldTypeEnum::EVENT_DATE->value)
                            <input type="text" wire:model.defer="field_values.{{$field['id']}}"
                                class="@if($step != $index) d-none @endif col-12 col-lg-8 h-100 datepicker"
                                placeholder="{{$field['name']}}">
                                @break

                        @case(App\Enums\ActivityPlanCategoryFieldTypeEnum::BOOLEAN->value)
                            <select wire:model.defer="field_values.{{$field['id']}}" class="@if($step != $index) d-none @endif col-12 col-lg-8 h-100">
                                <option value="{{null}}" hidden>{{__('common.please-choose-one')}}</option>
                                <option value="1">{{__('common.yes')}}</option>
                                <option value="1">{{__('common.no')}}</option>
                            </select>
                            @break

                        @case(App\Enums\ActivityPlanCategoryFieldTypeEnum::COUNTRY->value)
                            <select wire:model="field_values.{{$field['id']}}" class="@if($step != $index) d-none @endif col-12 col-lg-8 h-100">
                                <option value="{{null}}" hidden>{{__('common.please-choose-one')}}</option>
                                @foreach($countries as $country)
                                    <option value="{{$country->id}}">{{$country->name}}</option>
                                @endforeach
                            </select>
                            @break

                        @case(App\Enums\ActivityPlanCategoryFieldTypeEnum::COMPANY->value)
                            <select wire:model="field_values.{{$field['id']}}" class="@if($step != $index) d-none @endif col-12 col-lg-8 h-100">
                                <option value="{{null}}" hidden>{{__('common.please-choose-one')}}</option>
                                @foreach($companies as $company)
                                    <option value="{{$company->id}}">{{$company->name}}</option>
                                @endforeach
                            </select>
                            @break

                        @case(App\Enums\ActivityPlanCategoryFieldTypeEnum::CITY->value)
                            <select wire:model.defer="field_values.{{$field['id']}}" class="@if($step != $index) d-none @endif col-12 col-lg-8 h-100">
                                <option value="{{null}}" hidden>{{__('common.please-choose-one')}}</option>
                                @foreach($cities as $city)
                                    <option value="{{$city->id}}">{{$city->name}}</option>
                                @endforeach
                            </select>
                            @break

                        @case(App\Enums\ActivityPlanCategoryFieldTypeEnum::EXPERT->value)
                            <select wire:model.defer="field_values.{{$field['id']}}" class="@if($step != $index) d-none @endif col-12 col-lg-8 h-100">
                                <option value="{{null}}" hidden>{{__('common.please-choose-one')}}</option>
                                @foreach($experts as $expert)
                                    <option value="{{$expert->id}}">{{$expert->name}}</option>
                                @endforeach
                            </select>
                            @break

                        @case(App\Enums\ActivityPlanCategoryFieldTypeEnum::CGP_EMPLOYEE->value)
                            <select wire:model.defer="field_values.{{$field['id']}}" class="@if($step != $index) d-none @endif col-12 col-lg-8 h-100">
                                <option value="{{null}}" hidden>{{__('common.please-choose-one')}}</option>
                                @foreach($cgp_employees as $cgp_employee)
                                    <option value="{{$cgp_employee->id}}">{{$cgp_employee->name}}</option>
                                @endforeach
                            </select>
                            @break

                        @case(App\Enums\ActivityPlanCategoryFieldTypeEnum::TIME->value)
                            <input type="text" wire:model.defer="field_values.{{$field['id']}}"
                                class="@if($step != $index) d-none @endif col-12 col-lg-8 h-100 timepicker"
                                placeholder="{{$field['name']}}">
                            @break

                        @default

                    @endswitch
                @endforeach

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

                @if($step === $max_step)
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
            <p class="title">{{__('activity-plan.attributes')}}:</p>
            <div class="workshop-data">
                @foreach($fields as $index => $field)
                    <span>{{Str::title($field['name'])}}:
                        <span style="color: rgb(0,87,95)">
                            @if(array_key_exists($field['id'], $field_values))
                                @switch($field['type'])
                                    @case(App\Enums\ActivityPlanCategoryFieldTypeEnum::BOOLEAN->value)
                                        @if($field_values[$field['id']])
                                            {{__('common.yes')}}
                                        @elseif(is_null($field_values[$field['id']]))@else
                                            {{__('common.no')}}
                                        @endif
                                    @break
                                    @case(App\Enums\ActivityPlanCategoryFieldTypeEnum::COUNTRY->value)
                                        {{optional($countries->find($field_values[$field['id']]))->name}}
                                    @break
                                    @case(App\Enums\ActivityPlanCategoryFieldTypeEnum::CITY->value)
                                        {{optional($cities->find($field_values[$field['id']]))->name}}
                                    @break
                                    @case(App\Enums\ActivityPlanCategoryFieldTypeEnum::COMPANY->value)
                                        {{optional($companies->find($field_values[$field['id']]))->name}}
                                    @break
                                    @case(App\Enums\ActivityPlanCategoryFieldTypeEnum::EXPERT->value)
                                        {{optional($experts->find($field_values[$field['id']]))->name}}
                                    @break
                                    @default
                                        {{$field_values[$field['id']]}}
                                @endswitch
                            @endif
                        </span>
                    </span>

                    <br>
                @endforeach
            </div>
        </div>
    </div>
</div>
