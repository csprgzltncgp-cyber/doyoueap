@push('livewire_js')
<script src="/assets/js/chosen.js" type="text/javascript" charset="utf-8"></script>
<script src="{{asset('assets/js/datetime.js')}}"></script>
<script>
    document.addEventListener('livewire:load', function () {
        const companyId = @js($company->id);

        $(`#countries-${companyId}`).chosen().change(function (e) {
            @this.set('countries', $(e.target).val());
        });

        $(`#contract_start-${companyId}`).datepicker({
            format: 'yyyy-mm-dd',
        }).change(function (event) {
            @this.set('contractDate', event.target.value);
        });

        $(`#contract_end-${companyId}`).datepicker({
            format: 'yyyy-mm-dd',
        }).change(function (event) {
            @this.set('contractDateEnd', event.target.value);
        });

        window.livewire.on('companyUpdated', () => {
            Swal.fire({
                title: '{{__('common.case_input_edit.successful_save')}}',
                text: '',
                icon: 'success',
            });
        });

        window.livewire.on('setNewPassword', () => {
            setNewPassword();
        });

        async function setNewPassword() {
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
                        showConfirmButton: true,
                    });
                    return;
                }

                // Contain at least one uppercase/lowercase letters, one number and one special character
                // Must be at least 8 characters long
                if(! /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*(_|[^\w])).+$/.test(formValues.password)) {
                    Swal.fire({
                        icon: 'error',
                        title: '{{__('common.force-change-password.validation')}}',
                        showConfirmButton: true,
                    });
                    return;
                }

                @this.set('clientUser.password', formValues.password);

                Swal.fire({
                    title: '{{__('common.new-password-success')}}',
                    icon: 'success',
                    showConfirmButton: false,
                    timer: 1500
                });
            }
        }
    });
</script>
@endpush
<div>
    <link rel="stylesheet" href="/assets/css/form.css?v={{time()}}">
    <link rel="stylesheet" href="/assets/css/perfix-input.css?v={{time()}}">
    <link rel="stylesheet" href="{{asset('assets/css/bordered-checkbox.css')}}?v={{time()}}">
    <link rel="stylesheet" href="{{asset('assets/css/cases/datetime.css')}}">
    <link href="{{asset('assets/css/chosen.css')}}" rel="stylesheet" type="text/css">

    <style>
        .chosen-container{
            width: auto !important;
            flex: 1 0 auto !important;
        }

        .form-group input {
            color: black !important;
        }

        .form-group select {
            color: black !important;
        }

        .chosen-container.chosen-container-multi{
            width: min-content !important;
        }
    </style>

    <form wire:submit.prevent="update" style="max-width: 1000px !important;" autocomplete="off" novalidate>
        @if(!$disabled_breadcrumb)
            {{ Breadcrumbs::render('companies.edit', $company) }}
        @endif
        <h1 style="font-size: 18px; color:black">{{__('company-edit.default_datas')}}:</h1>


        @if($contractHolder == 2)
            <div class="input-group col-md-7 p-0">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        Lead Account:
                    </div>
                </div>
                <select wire:model="activity_plan_user">
                    <option disabled value="null">{{__('common.please-choose')}}</option>
                    @foreach ($account_admins as $admin)
                        <option value='{{$admin->id}}'>{{$admin->name}}</option>
                    @endforeach
                </select>
            </div>
        @endif

        <div class="input-group col-md-7 p-0">
            <div class="input-group-prepend">
                <div class="input-group-text">
                    {{__('workshop.company_name')}}:
                </div>
            </div>
            <input type="text" wire:model="company.name" required>
        </div>

        <div class="input-group col-md-7 p-0" wire:ignore>
            <div class="input-group-prepend">
                <div class="input-group-text">
                    {{__('common.countries')}}:
                </div>
            </div>
            <select id="countries-{{$company->id}}" multiple class="chosen-select" wire:model="countries" wire:ignore>
                @foreach ($allCountries as $country)
                    <option @if(in_array($country->id,$countries)) selected @endif value='{{$country->id}}'>{{$country->name}}</option>
                @endforeach
            </select>
        </div>

        <!--If contract holder is CGP -->
        @if ($contractHolder == 2 && $company->clientUser->first())
            <div class="form-row">
                <div class="form-group col-md-7 mb-0">
                    <div class="input-group col-12 p-0">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                {{__('common.operator-connected-company')}}:
                            </div>
                        </div>
                        <select wire:change='update_connected_company($event.target.value)' >
                            <option value={{null}}>{{__('common.please-choose')}}</option>
                            @foreach($connectCompanies as $c)
                                <option @if($c->clientUser->first() && $company->clientUser->first()
                                && $c->clientUser->first()->id == $company->clientUser->first()->connected_account) selected @endif value="{{ $c->id }}">
                                    {{ \Illuminate\Support\Str::limit($c->name, 60, '...') }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        @endif
        <div class="form-row">
            <div class="form-group col-md-4 mb-0">
                <div class="input-group col-12 p-0">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            {{__('crisis.contract_holder')}}:
                        </div>
                    </div>
                    <select wire:model='contractHolder' {{$countryDifferentiates->contract_holder ? 'disabled' : ''}}>
                        <option value="null" disabled selected>{{__('common.please-choose')}}</option>
                        @foreach($contractHolders as $contractHolderItem)
                            <option class="mb-2" id="ch_{{$contractHolderItem->id}}"
                                    value="{{$contractHolderItem->id}}">{{$contractHolderItem->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group col-md-3 mb-0">
                <div class="input-group col-12 p-0 mb-0">
                    <label class="checkbox-container mt-0 w-100"
                        style="color: rgb(89,198,198); padding: 10px 0 10px 10px; border: 2px solid rgb(89,198,198) !important; font-size: 16px; margin-top: 8px;">
                        {{__('company-edit.different-per-country')}}
                        <input type="checkbox" class="delete_later d-none" wire:model='countryDifferentiates.contract_holder'>
                        <span class="checkmark d-flex justify-content-center align-items-center"
                            style="left:auto; right: 0; height: 100%; width: 50px; border-left: 2px solid rgb(89,198,198) !important;">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="checked {{$countryDifferentiates->contract_holder ? '' : 'd-none'}}"
                                style="width: 25px; height: 25px; color: white" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="unchecked {{$countryDifferentiates->contract_holder ? 'd-none' : ''}}"
                                style="width: 20px; height: 20px;" fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </span>
                    </label>
                </div>
            </div>
        </div>

        @if($contractHolder == 1 || empty($contractHolder))
            <div class="form-row">
                <div class="form-group col-md-4 mb-0">
                    <div class="input-group col-12 p-0">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                ORG ID:
                            </div>
                        </div>
                        <input type="text" wire:model="orgId" {{$countryDifferentiates->org_id ? 'disabled' : ''}}>
                    </div>
                </div>

                <div class="form-group col-md-3 mb-0">
                    <div class="input-group col-12 p-0 mb-0">
                        <label class="checkbox-container mt-0 w-100"
                            style="color: rgb(89,198,198); padding: 10px 0 10px 10px; border: 2px solid rgb(89,198,198) !important; font-size: 16px; margin-top: 8px;">
                            {{__('company-edit.different-per-country')}}
                            <input type="checkbox" class="delete_later d-none" wire:model='countryDifferentiates.org_id'>
                            <span class="checkmark d-flex justify-content-center align-items-center"
                                style="left:auto; right: 0; height: 100%; width: 50px; border-left: 2px solid rgb(89,198,198) !important;">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="checked {{$countryDifferentiates->org_id ? '' : 'd-none'}}"
                                    style="width: 25px; height: 25px; color: white" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="unchecked {{$countryDifferentiates->org_id ? 'd-none' : ''}}"
                                    style="width: 20px; height: 20px;" fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </span>
                        </label>
                    </div>
                </div>
            </div>
        @endif

        <div class="form-row {{!($contractHolder == 2 || empty($contractHolder)) ? 'd-none' : ''}}">
            <div class="form-group col-md-2 mb-0">
                <input wire:model='contractDate' id="contract_start-{{$company->id}}" type="text" {{$countryDifferentiates->contract_date ? 'disabled' : ''}} placeholder="{{__('company-edit.contract-start')}}">
            </div>

            <div class="form-group col-md-2 mb-0">
                <input wire:model='contractDateEnd' id="contract_end-{{$company->id}}" type="text" {{$countryDifferentiates->contract_date ? 'disabled' : ''}} placeholder="{{__('company-edit.contract-end')}}">
            </div>

            <div class="form-group col-md-3 mb-0">
                <div class="input-group col-12 p-0 mb-0">
                    <label class="checkbox-container mt-0 w-100"
                        style="color: rgb(89,198,198); padding: 10px 0 10px 10px; border: 2px solid rgb(89,198,198) !important; font-size: 16px; margin-top: 8px;">
                        {{__('company-edit.different-per-country')}}
                        <input type="checkbox" class="delete_later d-none" wire:model='countryDifferentiates.contract_date'>
                        <span class="checkmark d-flex justify-content-center align-items-center"
                            style="left:auto; right: 0; height: 100%; width: 50px; border-left: 2px solid rgb(89,198,198) !important;">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="checked {{$countryDifferentiates->contract_date ? '' : 'd-none'}}"
                                style="width: 25px; height: 25px; color: white" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="unchecked  {{$countryDifferentiates->contract_date ? 'd-none' : ''}}"
                                style="width: 20px; height: 20px;" fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </span>
                    </label>
                </div>
            </div>
        </div>

        @if($contractHolder == 2 || empty($contractHolder))
            <div class="form-row">
                <div class="form-group col-md-4 mb-0">
                    <input type="text" wire:model.lazy="contractDateReminderEmail" {{$countryDifferentiates->contract_date_reminder_email ? 'disabled' : ''}} placeholder="{{__('company-edit.contract-date-reminder-email')}}">
                </div>

                <div class="form-group col-md-3 mb-0">
                    <div class="input-group col-12 p-0 mb-0">
                        <label class="checkbox-container mt-0 w-100"
                            style="color: rgb(89,198,198); padding: 10px 0 10px 10px; border: 2px solid rgb(89,198,198) !important; font-size: 16px; margin-top: 8px;">
                            {{__('company-edit.different-per-country')}}
                            <input type="checkbox" class="delete_later d-none" wire:model='countryDifferentiates.contract_date_reminder_email'>
                            <span class="checkmark d-flex justify-content-center align-items-center"
                                style="left:auto; right: 0; height: 100%; width: 50px; border-left: 2px solid rgb(89,198,198) !important;">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="checked {{$countryDifferentiates->contract_date_reminder_email ? '' : 'd-none'}}"
                                    style="width: 25px; height: 25px; color: white" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="unchecked {{$countryDifferentiates->contract_date_reminder_email ? 'd-none' : ''}}"
                                    style="width: 20px; height: 20px;" fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </span>
                        </label>
                    </div>
                </div>
            </div>
        @endif

        @if (!$companyConnected)
            <!--If contract holder is CGP -->
            @if($contractHolder == 2 || empty($contractHolder))
                <div class="form-row">
                    <div class="form-group col-md-4 mb-0">
                        <input type="text" disabled placeholder="{{__('company-edit.reporting')}}">
                    </div>

                    <div class="form-group col-md-3 mb-0">
                        <div class="input-group col-12 p-0 mb-0">
                            <label class="checkbox-container mt-0 w-100"
                                style="color: rgb(89,198,198); padding: 10px 0 10px 10px; border: 2px solid rgb(89,198,198) !important; font-size: 16px; margin-top: 8px;">
                                {{__('company-edit.different-per-country')}}
                                <input type="checkbox" class="delete_later d-none" wire:model='countryDifferentiates.reporting'>
                                <span class="checkmark d-flex justify-content-center align-items-center"
                                    style="left:auto; right: 0; height: 100%; width: 50px; border-left: 2px solid rgb(89,198,198) !important;">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="checked {{$countryDifferentiates->reporting ? '' : 'd-none'}}"
                                        style="width: 25px; height: 25px; color: white" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="unchecked {{$countryDifferentiates->reporting ? 'd-none' : ''}}"
                                        style="width: 20px; height: 20px;" fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </span>
                            </label>
                        </div>
                    </div>
                </div>
            @endif
             <!--If reporting is not enabled and contract holder is CGP -->
            @if(!optional($countryDifferentiates)->reporting && $contractHolder == 2 || empty($contractHolder))
                <div class="form-row">
                    <div class="form-group col-md-7 mb-0">
                        <div class="input-group col-12 p-0">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    {{__('company-edit.report-username')}}:
                                </div>
                            </div>
                            <input type="text" wire:model='clientUser.username'>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    @if(empty($clientUser['password']))
                        <div class="form-group col-md-7 mb-0">
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
                        <div class="form-group col-md-7" style="margin-bottom: 20px;">
                            <button type="button" style="" class="text-center btn-radius" wire:click="emitNewPasswordEvent">
                                <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="width:20px; height:20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                </svg>
                                {{__('company-edit.set-new-password')}}
                            </button>
                        </div>
                    @endif
                </div>

                <div class="form-row">
                    <div class="form-group col-md-7 mb-0">
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
                </div>
            @endif
        @endif

        <div class="form-row">
            <div class="form-group col-md-7 mb-0">
                <div class="input-group col-12 p-0">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            {{__('crisis.active')}}:
                        </div>
                    </div>
                    <select wire:model="company.active">
                        <option value="{{null}}" disabled>{{__('common.please-choose')}}</option>
                        <option value="true">{{__('common.yes')}}</option>
                        <option value="false">{{__('common.no')}}</option>
                    </select>
                </div>
            </div>
        </div>

        <h1 style="font-size: 18px; color:black">{{__('company-edit.country_settigns')}}:</h1>
        <div class="col-md-7 p-0 mb-5">
            @foreach($countries as $country)
                <livewire:admin.company-country-component
                        :country_id="$country"
                        :company="$company"
                        :countryDifferentiates="$countryDifferentiates"
                        :companyConnected="$companyConnected"
                        :wire:key="$country"
                />
            @endforeach
        </div>

        @if($contractHolder == 2 || empty($contractHolder))
        <h1 style="font-size: 18px; color:black">{{__('company-edit.invoicing')}}:</h1>
            <div class="form-row">
                <div class="form-group col-md-4 mb-0">
                    <input type="text" placeholder="Számlázás" disabled>
                </div>
                <div class="form-group col-md-3 mb-0">
                    <div class="input-group col-12 p-0 mb-0">
                        <label class="checkbox-container mt-0 w-100"
                            style="color: rgb(89,198,198); padding: 10px 0 10px 10px; border: 2px solid rgb(89,198,198) !important; font-size: 16px; margin-top: 8px;">
                            {{__('company-edit.different-per-country')}}
                            <input type="checkbox" class="delete_later d-none" wire:model='countryDifferentiates.invoicing'>
                            <span class="checkmark d-flex justify-content-center align-items-center"
                                style="left:auto; right: 0; height: 100%; width: 50px; border-left: 2px solid rgb(89,198,198) !important;">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="checked {{$countryDifferentiates->invoicing ? '' : 'd-none'}}"
                                    style="width: 25px; height: 25px; color: white" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="unchecked {{$countryDifferentiates->invoicing ? 'd-none' : ''}}"
                                    style="width: 20px; height: 20px;" fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </span>
                        </label>
                    </div>
                </div>
            </div>

            <livewire:admin.direct-invoicing.container
                :company="$company"
                :countryDifferentiates="$countryDifferentiates"
            >
        @endif

        <div class="form-row mt-5 ml-0">
            <div>
                <button style="text-transform: uppercase; --btn-max-width: var(--btn-min-width);" type="submit" name="button" class="text-center btn-radius">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="height: 20px; width:20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                    </svg>
                    <span class="mt-1">
                        {{__('common.save')}}
                    </span>
                </button>
            </div>

            @if($contractHolder == 2)
                <div>
                    <button wire:click="$emitTo('admin.direct-invoicing.container','createList')" style="text-transform: uppercase;" type="button" name="button" class="text-center btn-radius">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="height: 20px; width:20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        <span class="mt-1">
                            {{__('company-edit.create_list')}}
                        </span>
                    </button>
                </div>
            @endif
        </div>
    </form>
</div>
