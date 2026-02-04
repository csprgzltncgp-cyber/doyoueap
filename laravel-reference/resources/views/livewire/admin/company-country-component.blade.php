@push('livewire_js')
    <script>
        $('#contract_date_{{$company->id}}_{{$country->id}}').datepicker({
            format: 'yyyy-mm-dd',
        });

        $('#contract_date_{{$company->id}}_{{$country->id}}').change(function (event) {
            @this.set('contract_date', event.target.value);
        });

        $('#contract_date_end_{{$company->id}}_{{$country->id}}').datepicker({
            format: 'yyyy-mm-dd',
        });

        $('#contract_date_end_{{$company->id}}_{{$country->id}}').change(function (event) {
            @this.set('contract_date_end', event.target.value);
        });

        Livewire.on('showError', message => {
            Swal.fire(
                message,
                '',
                'error'
            );
        });

        async function setCountryNewPassword(country_id) {
            const { value: formValues } = await Swal.fire({
                title: '{{__('company-edit.set-new-password')}}',
                html:
                    `
                    <label style="float: left; font-size:15px; margin-top:15px" for="new-password">{{__('operator-data.new_password')}}</label>
                    <div data-content="">
                        <input style="margin-top:0" id="new-password" class="swal2-input" type="password" required>
                    <div>

                    <label style="float: left; font-size:15px; margin-top:15px" for="password-confirmation">{{__('common.force-change-password.password-confirmation')}}</label>
                    <div data-content="">
                        <input style="margin-top:0" id="password-confirmation" class="swal2-input" type="password" required>
                    <div>
                    `,
                focusConfirm: false,
                preConfirm: () => {
                    return {
                        password: document.getElementById('new-password').value,
                        password_confirmation: document.getElementById('password-confirmation').value,
                    }
                }
            });

            if (formValues) {

                if(formValues.password != formValues.password_confirmation || (formValues.password == '' && formValues.password_confirmation == '')) {
                    Swal.fire({
                        icon: 'error',
                        title: '{{__('common.password-incorrect')}}',
                        showConfirmButton: false,
                        timer: 3000
                    });
                    return;
                }

                Livewire.emitTo('admin.company-country-component', 'setCountryClientNewPassword', country_id, formValues.password);

                Swal.fire({
                    title: '{{__('common.new-password-success')}}',
                    icon: 'success',
                    showConfirmButton: false,
                    timer: 1500
                });
            }
        }
    </script>
@endpush


<div>
    <div class="case-list-in col-12 group" wire:click="toggleOpen('is_opened')"
         style="--btn-height: auto; {{$is_opened ? 'background: rgb(0,87,95); color:white;' : ''}}">
        <label style="margin: 0">{{$country->code}}</label>
        <p class="caret-left float-right">
            <svg id="{{$country->id}}" xmlns="http://www.w3.org/2000/svg"
                 style="width: 20px; height: 20px; {{$is_opened ? 'transform: rotate(180deg);' : ''}}" fill="none"
                 viewBox="0 0 24 24"
                 stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
            </svg>
        </p>
    </div>

    <div class="{{$is_opened ? 'd-block' : 'd-none'}}" id="country_{{$country->id}}">
        @if($contract_holder == 2)
        <div class="form-group">
            <div class="input-group col-12 p-0">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        Account:
                    </div>
                </div>
                <select wire:model="activity_plan_user">
                    <option disabled value="null">{{__('common.please-choose')}}</option>
                    @foreach ($account_admins as $admin)
                        <option value='{{$admin->id}}'>{{$admin->name}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        @endif

        @if($countryDifferentiates->contract_holder)
            <div class="form-group">
                <div class="input-group col-12 p-0">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            {{__('crisis.contract_holder')}}:
                        </div>
                    </div>
                    <select id="contract_holder_id_{{$company->id}}_{{$country->id}}" wire:model="contract_holder" required>
                        <option value="null" disabled selected>{{__('common.please-choose')}}</option>
                        @foreach($contractHolders as $contractHolder)
                            <option class="mb-2" id="ch_{{$contractHolder->id}}"
                                    value="{{$contractHolder->id}}">{{$contractHolder->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        @endif

        @if($countryDifferentiates->org_id && $contract_holder == 1)
            <div class="form-group">
                <div class="input-group col-12 p-0">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            ORG ID:
                        </div>
                    </div>
                    <input type="text" id="org_id_{{$company->id}}_{{$country->id}}" wire:model="org_id" required/>
                </div>
            </div>
        @endif

        <div class="form-row mb-0 {{ !($countryDifferentiates->contract_date && $contract_holder == 2) ? 'd-none' : '' }}">
            <div class="form-group col-md-6 mb-0">
                <div class="form-group">
                    <div class="input-group col-12 p-0">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                {{__('company-edit.contract-start')}}:
                            </div>
                        </div>
                        <input wire:ignore wire:model='contract_date' id="contract_date_{{$company->id}}_{{$country->id}}" type="text">
                    </div>
                </div>
            </div>
            <div class="form-group col-md-6 mb-0">
                <div class="form-group">
                    <div class="input-group col-12 p-0">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                {{__('company-edit.contract-end')}}:
                            </div>
                        </div>
                        <input wire:ignore wire:model='contract_date_end' id="contract_date_end_{{$company->id}}_{{$country->id}}" type="text">
                    </div>
                </div>
            </div>
        </div>


        @if($countryDifferentiates->contract_date_reminder_email && $contract_holder == 2)
            <div class="form-group col-md-12 mb-0 p-0">
                <input type="text" wire:model.lazy="contract_date_reminder_email" placeholder="{{__('company-edit.contract-date-reminder-email')}}">
            </div>
        @endif

        <div class="form-group">
            <div class="input-group col-12 p-0">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        {{__('common.company_edit.headcount')}}:
                    </div>
                </div>
                <input type="number" wire:model="head_count"/>
            </div>
        </div>

        @if(!$companyConnected && $countryDifferentiates->reporting && $contract_holder == 2)
            <h1 style="font-size: 14px; color:#59c6c6">{{__('company-edit.client_dashboard_settings')}}:</h1>
            <div class="form-group">
                <div class="input-group col-12 p-0 mb-0">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            {{__('company-edit.report-username')}}:
                        </div>
                    </div>
                    <input type="text" wire:model='clientUser.username'>
                </div>
            </div>

            @if(empty($clientUser['password']))
                <div class="form-group">
                    <div class="input-group col-12 p-0">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                {{__('company-edit.report-password')}}:
                            </div>
                        </div>
                        <input type="password" autocomplete="new-password" onchange="@this.set('clientUser.password', event.target.value);">
                    </div>
                </div>
            @else
                <div class="form-group">
                    <button type="button" style="padding-bottom: 14px; padding-left:0px" class="text-center btn-radius" onclick="setCountryNewPassword({{intval($country->id)}})">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="width:20px; height:20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        {{__('company-edit.set-new-password')}}
                    </button>
                </div>
            @endif

            <div class="form-group">
                <div class="input-group col-12 p-0">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            {{__('company-edit.report-language')}}:
                        </div>
                    </div>
                    <select wire:model='clientUser.language_id'>
                        <option value="{{null}}" disabled selected>{{__('common.please-choose')}}</option>
                        <option value="3">English</option>
                        <option value="1">Magyar</option>
                        <option value="2">Polska</option>
                        <option value="4">Slovenský</option>
                        <option value="5">Česky</option>
                        <option value="6">Українська</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <div class="input-group col-12 p-0">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            {{__('common.access_to_evey_country')}}:
                        </div>
                    </div>
                    <select wire:model='clientUser.all_country'>
                        <option value="{{null}}" disabled selected>{{__('common.please-choose')}}</option>
                        <option value="0">{{__('common.no')}}</option>
                        <option value="1">{{__('common.yes')}}</option>
                    </select>
                </div>
            </div>
        @endif

        <h1 style="font-size: 14px; color:#59c6c6">{{__('company-edit.crisis_and_workshop_settings')}}:</h1>
        <div class="form-row">
            <div class="form-group col mb-0 pr-0">
                <input type="text" id="workshops_number_{{$company->id}}_{{$country->id}}"  style="color: rgb(89,198,198) !important"
                       value="{{__('common.workshops')}}"
                       placeholder="Workshop - Number of sessions available" readonly>
            </div>
            <div class="form-group col-4 p-0">
                <button wire:click="addWorkshop" type="button"
                        class="mt-auto justify-content-center align-items-center btn-radius"
                        style="--btn-height: 48px; --btn-margin-left: var(--btn-margin-x); border: 2px solid rgb(89,198,198) !important;"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="color: white; width: 20px; height: 20px;" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>{{__('common.add')}}</span>
                </button>
            </div>

            <div class="form-group col-4 mb-0 p-0">
                <button wire:click="toggleOpen('is_workshops_opened')" type="button"
                        class="mt-auto d-flex justify-content-center align-items-center btn-radius"
                        style="--btn-height: 48px; --btn-margin-left: var(--btn-margin-x); --btn-min-width: auto; --btn-padding-x: 10px; border: 2px solid rgb(89,198,198); {{$is_workshops_opened ? 'background: rgb(0,87,95); color:white; border: 2px solid rgb(0,87,95) !important;' : ''}}">
                    <svg xmlns="http://www.w3.org/2000/svg"
                         style="color: white; width: 20px; height: 20px; margin:2px; {{$is_workshops_opened ? 'transform: rotate(180deg)' : ''}}"
                         fill="none"
                         viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
            </div>
        </div>

        <div class="{{$is_workshops_opened ? 'd-block' : 'd-none'}} mb-4">
            @foreach($workshops as $workshop)
                <livewire:admin.crisis-workshop-component
                        :company="$company"
                        :model="$workshop"
                        type="workshop"
                        :country="$country"
                        :wire:key="$workshop->id"
                />
            @endforeach
        </div>

        <div class="form-row">
            <div class="form-group col pr-0">
                <input type="text" id="crisis_number_{{$company->id}}_{{$country->id}}"  style="color: rgb(89,198,198) !important"
                       value="{{__('common.crisis_interventions')}}"
                       placeholder="Crisis intervention - Number of sessions available" readonly>
            </div>
            <div class="form-group col-4 p-0">
                <button wire:click="addCrisisIntervention" type="button"
                        class="mt-auto d-flex justify-content-center align-items-center btn-radius"
                        style="--btn-height: 48px; --btn-margin-left: var(--btn-margin-x); border: 2px solid rgb(89,198,198) !important;"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="color: white; width: 20px; height: 20px;" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>{{__('common.add')}}</span>
                </button>
            </div>
            <div class="form-group col-4 p-0">
                <button wire:click="toggleOpen('is_crisis_interventions_opened')" type="button"
                        class="mt-auto d-flex justify-content-center align-items-center btn-radius"
                        style="--btn-height: 48px; --btn-margin-left: var(--btn-margin-x); --btn-min-width: auto; --btn-padding-x: 10px; border: 2px solid rgb(89,198,198); {{$is_crisis_interventions_opened ? 'transform: rotate(180deg); background: rgb(0,87,95); !important color:white; border: 2px solid rgb(0,87,95) !important;' : ''}}">
                         <svg xmlns="http://www.w3.org/2000/svg" style="color: white; width: 20px; height: 20px; margin:2px;" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
            </div>
        </div>

        <div class="{{$is_crisis_interventions_opened ? 'd-block' : 'd-none'}} mb-4">
            @foreach($crisis_interventions as $crisis_intervention)
                <livewire:admin.crisis-workshop-component
                        :company="$company"
                        :model="$crisis_intervention"
                        type="crisis"
                        :country="$country"
                        :wire:key="$crisis_intervention->id"
                />
            @endforeach
        </div>
    </div>
</div>
