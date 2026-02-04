@push('livewire_js')
    <script src="/assets/js/chosen.js" type="text/javascript" charset="utf-8"></script>
    <script>
        $('#uploadContract').click(function() {
            $('#contracts').click();
        });

        $('#uploadCertificate').click(function() {
            $('#certificates').click();
        });

        $('#uploadEapOnlinePhoto').click(function(){
            $('#eapOnlinePhoto').click();
        });

        $("#countries").chosen().change(function(e) {
            @this.set('expertCountries', $(e.target).val());
        });

        $("#crisis_countries").chosen().change(function(e) {
            @this.set('expertCrisisCountries', $(e.target).val());
        });

        $("#cities").chosen().change(function(e) {
            @this.set('expertCities', $(e.target).val());
        });

        $("#outsource_countries").chosen().change(function(e) {
            @this.set('expertOutsourceCountries', $(e.target).val());
        });

        $("#permissions").chosen().change(function(e) {
            @this.set('expertPermissions', $(e.target).val());
        });

        $("#specializations").chosen().change(function(e) {
            @this.set('expertSpecializations', $(e.target).val());
        });

        $("#languageSkills").chosen().change(function(e) {
            @this.set('expertLanguageSkills', $(e.target).val());
        });

        Livewire.on('expertDataUpdated', function() {
            Swal.fire({
                title: '{{ __('common.successful-change') }}',
                text: '',
                icon: 'success',
                confirmButtonText: 'Ok'
            });
        });

        Livewire.on('errorEvent', function(error) {
            Swal.fire({
                title: error,
                text: '',
                icon: 'error',
                confirmButtonText: 'Ok'
            });
        });

        Livewire.on('errorEventCustomItem', function(error){
            Swal.fire({
                title: '{{ __('invoice.warning_delete_custom_item_invalid') }}',
                text: '',
                icon: 'error',
                confirmButtonText: 'Ok'
            });
        });

        Livewire.on('extraItemDialogVisible', function(currency) {
            custom_item_form(currency);
        });

        Livewire.on('custom_item_added', function() {
            Swal.fire({
                title: '{{ __('invoice.custom-item-added') }}',
                text: '',
                icon: 'success',
                confirmButtonText: 'Ok'
            });
        });

        // Confirm and delete asset item
        Livewire.on('trigger_custom_item_delete', key => {
            Swal.fire({
                title: '{{ __('invoice.warning_delete_custom_item_title') }}',
                html: '{{ __('invoice.warning_delete_custom_item_text') }}',
                icon: 'warning',
                showCancelButton: true,
            }).then((result) => {
                if (result.value) {
                    @this.call('delete_custom_item',key)
                }
            });
        });

        function deleteExisitingFile(id) {
            Swal.fire({
                title: '{{ __('common.are-you-sure-to-delete') }}',
                text: '{{ __('common.operation-cannot-undone') }}',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '{{ __('common.yes-delete-it') }}',
            }).then((result) => {
                if (result.value) {
                    Livewire.emit('deleteExistingFile', id);
                }
            })
        }

        function deleteExpertCurrencyChangeDocument(){
            Swal.fire({
                title: '{{ __('common.are-you-sure-to-delete') }}',
                text: '{{ __('common.operation-cannot-undone') }}',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '{{ __('common.yes-delete-it') }}',
            }).then((result) => {
                if (result.value) {
                    Livewire.emit('deleteExpertCurrencyChangeDocument');
                }
            })
        }

        function custom_item_form(currency) {
            Swal.fire({
                html: `
                <form class="col-12 p-4 px-5">
                    <div class="text-center mb-4" style="color:rgb(34,85,94); font-size: 16px">
                        <h1>{{ __('invoice.custom-item') }}</h1>
                    </div>
                    <div class="text-left" style="color:rgb(89,198,198); font-size: 16px">
                        {{ __('invoice.custom-item-vars.item-name') }}:
                    </div>
                    <div class="d-flex w-100 align-items-center">
                        <div class="d-flex w-100 align-items-center" style="margin-right:10px;">
                            <input class="swal2-input answer-input" type="text" name="input1"
                            value="" style="color:black!important">
                        </div>
                    </div>
                    <div class="text-left" style="color:rgb(89,198,198); font-size: 16px">
                        {{ __('invoice.custom-item-vars.item-country-name') }}:
                    </div>
                    <div class="d-flex w-100 align-items-center">
                        <div class="d-flex w-100 align-items-center" style="margin-right:10px">
                            <select class="swal2-input answer-input" name="input2" style="color:black!important">
                                <option value="" hidden>{{ __('common.please-choose') }}</option>
                                @foreach ($countries as $country)
                                    <option value="{{ $country->id }}">{{ $country->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="text-left" style="color:rgb(89,198,198); font-size: 16px">
                        {{ __('invoice.custom-item-vars.item-amount_1') }} `+currency.toUpperCase()+` {{ __('invoice.custom-item-vars.item-amount_2') }}:
                    </div>
                    <div class="d-flex w-100 align-items-center">
                        <div class="d-flex w-100 align-items-center" style="margin-right:10px">
                            <input class="swal2-input answer-input" type="text" name="input3"
                            placeholder="`+currency.toUpperCase()+`" style="color:black!important">
                        </div>
                    </div>
                </form>
                `,
                width: '600px',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK',
                showConfirmButton: true,
                showCloseButton: true,
                focusConfirm: true,
                onOpen: function() {
                    // Disable confirm button initially.
                    $(Swal.getConfirmButton()).prop('disabled', true);

                    // IF every question is answered enbale confirm button
                    $('.answer-input').on('keyup change', function() {

                        input_1 = Swal.getPopup().querySelector('input[name="input1"]');
                        input_2 = Swal.getPopup().querySelector('select[name="input2"]');
                        input_3 = Swal.getPopup().querySelector('input[name="input3"]');

                        if (input_3 && input_3.value != '') { // Format amount to local currency
                            $(input_3).val(parseInt(input_3.value.replace(/\s/g, '')).toLocaleString());
                        }

                        if (input_1.value && input_2.value && input_3.value ) {
                            $(Swal.getConfirmButton()).prop('disabled', false);
                        }
                    });
                },
                preConfirm: () => {

                    if (!input_1 || !input_2 || !input_3 ) {
                        Swal.showValidationMessage("{{ __('popup.wos_survey_warning') }}");
                    }
                    return {
                        input_1: input_1.value,
                        input_2: input_2.value,
                        input_3: input_3.value,
                    }
                }
            }).then(function(result) {
                if (result.value) {
                    @this.add_custom_invoice_item(result.value);
                }
            });
        }
    </script>
@endpush

<div>
    <link rel="stylesheet" href="/assets/css/form.css?v={{ time() }}">
    <link rel="stylesheet" href="/assets/css/perfix-input.css?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('assets/css/bordered-checkbox.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('assets/css/cases/datetime.css') }}">
    <link href="{{ asset('assets/css/chosen.css') }}" rel="stylesheet" type="text/css">
    <style>
        #permissions_chosen,
        #cities_chosen,
        #outsource_countries_chosen,
        #countries_chosen,
        #crisis_countries_chosen,
        #specializations_chosen,
        #languageSkills_chosen {
            flex: 1 !important;
        }

        #cities_chosen>ul>li.search-field {
            width: 120px !important;
        }
    </style>

    @section('title', 'Admin Dashboard')

    {{ Breadcrumbs::render('experts.edit', $user) }}
    <h1>{{ $user->name }}</h1>

    <form wire:submit.prevent='update' style="max-width: 750px !important;">
        <div style="margin-bottom:70px">
            <div class="input-group col-12 p-0">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        {{ __('eap-online.footer.menu_points.name') }}:
                    </div>
                </div>
                <input type="text" wire:model='user.name' >
            </div>

            <div class="input-group col-12 p-0">
                <label class="checkbox-container mt-0 w-100"
                    style="color: rgb(89, 198, 198); padding: 14px 0 14px 15px; border: 2px solid rgb(89,198,198) !important; font-size: 16px; margin-top: 8px;">
                    {{ __('expert-data.is_cgp_employee') }}
                    <input type="checkbox" class="delete_later d-none" wire:model="expertData.is_cgp_employee">
                    <span class="checkmark d-flex justify-content-center align-items-center"
                        style="left:auto; right: 0; height: 100%; width: 50px; border-left: 2px solid rgb(89,198,198) !important; {{ !$expertData->is_cgp_employee ? 'background-color: #eee !important;' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="checked {{ !$expertData->is_cgp_employee ? 'd-none' : '' }}"
                            style="width: 25px; height: 25px; color: white" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="unchecked {{ $expertData->is_cgp_employee ? 'd-none' : '' }}"
                            style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </span>
                </label>

                <label class="checkbox-container mt-0 w-100"
                        style="color: rgb(89, 198, 198); padding: 14px 0 14px 15px; border: 2px solid rgb(89,198,198) !important; font-size: 16px; margin-top: 8px;">
                    {{__('expert-data.is_eap_online_expert')}}
                    <input type="checkbox" class="delete_later d-none" wire:model="expertData.is_eap_online_expert">
                    <span class="checkmark d-flex justify-content-center align-items-center"
                            style="left:auto; right: 0; height: 100%; width: 50px; border-left: 2px solid rgb(89,198,198) !important; {{ !$expertData->is_eap_online_expert ? 'background-color: #eee !important;' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg"
                                class="checked {{!$expertData->is_eap_online_expert ? 'd-none' : ''}}"
                                style="width: 25px; height: 25px; color: white" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                        <svg xmlns="http://www.w3.org/2000/svg"
                                class="unchecked {{$expertData->is_eap_online_expert ? 'd-none' : ''}}"
                                style="width: 20px; height: 20px;" fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </span>
                </label>
            </div>
        </div>

        <div style="margin-bottom:50px">
            <h1 style="font-size: 20px;">{{ __('expert-data.contact-informations') }}:</h1>
            <div class="input-group col-12 p-0">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        {{ __('common.email') }}:
                    </div>
                </div>
                <input type="email" wire:model='user.email' >
            </div>

            <div class="form-row">
                <div class="form-group col-md-4 mb-0">
                    <input type="text" class="col-12" placeholder="{{ __('expert-data.phone') }}" disabled>
                </div>

                <div class="form-group col-md-4 mb-0">
                    <div class="d-flex flex-column">
                        <div class="input-group col-12 p-0">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    {{ __('expert-data.phone_prefix') }}:
                                </div>
                            </div>
                            <select class="col-12" wire:model="expertData.phone_prefix">
                                <option value="null" disabled>{{ __('common.please-choose') }}</option>
                                @foreach ($phonePrefixes as $phonePrefix)
                                    <option value="{{ $phonePrefix['code'] }}">{{ $phonePrefix['code'] }}
                                        {{ $phonePrefix['dial_code'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group col-md-4 mb-0">
                    <input wire:model='expertData.phone_number' type="number" class="col-12">
                </div>
            </div>
        </div>


        <div style="margin-bottom:50px" class="{{ $expertData->is_cgp_employee ? 'd-none' : '' }}">
            <h1 style="font-size: 20px;">{{ __('expert-data.post-address') }}:</h1>

            <div class="form-group mb-0">
                <div class="d-flex flex-column">
                    <div class="input-group col-12 p-0">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                {{ __('expert-data.post_code') }}:
                            </div>
                        </div>
                        <input type="text" class="col-12" wire:model='expertData.post_code'>
                    </div>
                </div>
            </div>

            <div class="form-group mb-0">
                <div class="d-flex flex-column">
                    <div class="input-group col-12 p-0">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                {{ __('expert-data.country') }}:
                            </div>
                        </div>
                        <select class="col-12" wire:model='expertData.country_id'>
                            <option value="null" disabled>{{ __('common.please-choose') }}</option>
                            @foreach ($countries as $country)
                                <option value="{{ $country->id }}">{{ $country->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-group mb-0">
                <div class="d-flex flex-column">
                    <div class="input-group col-12 p-0">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                {{ __('expert-data.city') }}:
                            </div>
                        </div>
                        <select class="col-12" wire:model='expertData.city_id'>
                            <option value="null" disabled>{{ __('common.please-choose') }}</option>
                            @foreach ($cities as $city)
                                <option value="{{ $city->id }}">{{ $city->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6 mb-0">
                    <div class="d-flex flex-column">
                        <div class="input-group col-12 p-0">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    {{ __('expert-data.street') }}:
                                </div>
                            </div>
                            <input type="text" class="col-12" wire:model='expertData.street'>
                        </div>
                    </div>
                </div>
                <div class="form-group col-md-6 mb-0">
                    <div class="d-flex flex-column">
                        <div class="input-group col-12 p-0">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    {{ __('expert-data.street_suffix.title') }}:
                                </div>
                            </div>
                            <select class="col-12" wire:model='expertData.street_suffix'>
                                <option value="null">{{ __('common.please-choose') }}</option>
                                @foreach ($streetSuffixes as $streetSuffix)
                                    <option value="{{ $streetSuffix['id'] }}">{{ $streetSuffix['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group mb-0">
                <div class="d-flex flex-column">
                    <div class="input-group col-12 p-0">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                {{ __('expert-data.house_number') }}:
                            </div>
                        </div>
                        <input type="text" class="col-12" wire:model='expertData.house_number'>
                    </div>
                </div>
            </div>
        </div>


        <div style="margin-bottom:50px">
            <h1 style="font-size: 20px;">{{ __('expert-data.invoice-informations') }}:</h1>
            <div class="form-group mb-0">
                <div class="d-flex flex-column">
                    <div class="input-group col-12 p-0">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                {{ __('invoice.invoicing_types') }}:
                            </div>
                        </div>
                        <select class="col-12" wire:model='invoiceData.invoicing_type'>
                            <option value="null" disabled>{{ __('common.please-choose') }}</option>
                            <option value="{{ \App\Enums\InvoicingType::TYPE_NORMAL }}">
                                {{__('invoice.invoicing_types_lang')[\App\Enums\InvoicingType::TYPE_NORMAL->value]}}
                            </option>
                            <option value="{{ \App\Enums\InvoicingType::TYPE_FIXED }}">
                                {{__('invoice.invoicing_types_lang')[\App\Enums\InvoicingType::TYPE_FIXED->value]}}
                            </option>
                            <option value="{{ \App\Enums\InvoicingType::TYPE_CUSTOM }}">
                                {{__('invoice.invoicing_types_lang')[\App\Enums\InvoicingType::TYPE_CUSTOM->value]}}
                            </option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-10 mb-0">
                    <input type="text" class="col-12" placeholder="{{ __('invoice.currency') }}" disabled>
                </div>

                <div class="form-group col-md-2 mb-0">
                    <select class="col-12 @error('invoiceData.currency') error @enderror" wire:model='invoiceData.currency' required>
                        <option value="null">{{__('common.please-choose')}}</option>
                        <option value="czk">CZK</option>
                        <option value="eur">EUR</option>
                        <option value="huf">HUF</option>
                        <option value="mdl">MDL</option>
                        <option value="oal">OAL</option>
                        <option value="pln">PLN</option>
                        <option value="ron">RON</option>
                        <option value="rsd">RSD</option>
                        <option value="usd">USD</option>
                        <option value="chf">CHF</option>
                    </select>
                    @error('invoiceData.currency') <small class="error">{{__('common.field-required')}}</small> <div style="height: 20px;"></div> @enderror
                </div>
            </div>
            @if($invoiceData->invoicing_type === \App\Enums\InvoicingType::TYPE_NORMAL)
                <div class="form-row">
                    <div class="form-group col-md-7 mb-0">
                        <input type="text" class="col-12" placeholder="{{ __('invoice.hourly_rate_50') }}" disabled>
                    </div>

                    <div class="form-group col-md-3 mb-0">
                        <input type="text" class="col-12"
                            placeholder="{{ __('common.period') }} 50 {{ __('crisis.minute') }}" disabled>
                    </div>

                    <div class="form-group col-md-2 mb-0">
                        <input type="{{$hidden_prices ? 'password' : 'text'}}" class="col-12 @error('invoiceData.hourly_rate_50') error @enderror" wire:model='invoiceData.hourly_rate_50' required>
                        @error('invoiceData.hourly_rate_50') <small class="error">{{__('common.field-required')}}</small> <div style="height: 20px;"></div> @enderror
                    </div>
                </div>

                @if ($user->hasPermission(2) || $user->hasPermission(3) || $user->hasPermission(7) || $user->hasPermission(16))
                    <div class="form-row">
                        <div class="form-group col-md-7 mb-0">
                            <input type="text" class="col-12" placeholder="{{ __('invoice.hourly_rate_30') }}"
                                disabled>
                        </div>

                        <div class="form-group col-md-3 mb-0">
                            <input type="text" class="col-12"
                                placeholder="{{ __('common.period') }} 30 {{ __('crisis.minute') }}" disabled>
                        </div>

                        <div class="form-group col-md-2 mb-0">
                            <input type="{{$hidden_prices ? 'password' : 'text'}}" class="col-12 @error('invoiceData.hourly_rate_30') error @enderror" wire:model='invoiceData.hourly_rate_30' required>
                            @error('invoiceData.hourly_rate_30') <small class="error">{{__('common.field-required')}}</small> <div style="height: 20px;"></div> @enderror
                        </div>
                    </div>
                @endif

                @if ($user->hasPermission(16))
                    <div class="form-row">
                        <div class="form-group col-md-7">
                            <input type="text" class="col-12" placeholder="{{ __('invoice.hourly_rate_15') }}"
                                disabled>
                        </div>

                        <div class="form-group col-md-3">
                            <input type="text" class="col-12"
                                placeholder="{{ __('common.period') }} 15 {{ __('crisis.minute') }}" disabled>
                        </div>

                        <div class="form-group col-md-2">
                            <input type="{{$hidden_prices ? 'password' : 'text'}}" class="col-12 @error('invoiceData.hourly_rate_15') error @enderror" wire:model='invoiceData.hourly_rate_15' required>
                            @error('invoiceData.hourly_rate_15') <small class="error">{{__('common.field-required')}}</small> <div style="height: 20px;"></div> @enderror
                        </div>
                    </div>
                @endif
            @endif
            @if ($invoiceData->invoicing_type === \App\Enums\InvoicingType::TYPE_FIXED)
                <div class="form-row" wire:key="wage_data">
                    <div class="form-group col-md-10 mb-0">
                        <input type="text" class="col-12" placeholder="{{ __('invoice.fixed_wage') }}" disabled>
                    </div>

                    <div class="form-group col-md-2 mb-0">
                        <input type="{{$hidden_prices ? 'password' : 'text'}}" class="col-12 @error('invoiceData.fixed_wage') error @enderror" wire:model='invoiceData.fixed_wage' required>
                        @error('invoiceData.fixed_wage') <small class="error">{{__('common.field-required')}}</small> <div style="height: 20px;"></div> @enderror
                    </div>
                </div>
                <div class="form-row" wire:key="wage_data">
                    <div class="form-group col-md-10 mb-0">
                        <input type="text" class="col-12" placeholder="{{ __('invoice.ranking_hourly_rate') }}" disabled>
                    </div>

                    <div class="form-group col-md-2 mb-0">
                        <input type="{{$hidden_prices ? 'password' : 'text'}}" class="col-12 @error('invoiceData.ranking_hourly_rate') error @enderror" wire:model='invoiceData.ranking_hourly_rate' required>
                        @error('invoiceData.ranking_hourly_rate') <small class="error">{{__('common.field-required')}}</small> <div style="height: 20px;"></div> @enderror
                    </div>
                </div>
            @endif

            @if ($single_session_rate_required)
                <div class="form-row">
                    <div class="form-group col-md-10 mb-0">
                        <input type="text" class="col-12" placeholder="{{ __('invoice.single_session_rate') }}" disabled>
                    </div>

                    <div class="form-group col-md-2 mb-0">
                        <input type="{{$hidden_prices ? 'password' : 'text'}}" class="col-12 @error('invoiceData.single_session_rate') error @enderror" wire:model='invoiceData.single_session_rate' required>
                        @error('invoiceData.single_session_rate') <small class="error">{{__('common.field-required')}}</small> <div style="height: 20px;"></div> @enderror
                    </div>
                </div>
            @endif

            @if ($custom_invoice_items)
                <h1 class="p-0" style="font-size: 20px;">{{ __('invoice.custom-invoice-items') }}:</h1>
                @foreach ($custom_invoice_items as $invoice_item)
                    <div class="form-row" wire:key="custom_invoice_item_{{$loop->index}}">
                        <div class="form-group col-md-7 mb-0">
                            <input type="text" class="col-12" placeholder="{{ $invoice_item->name }}" disabled>
                        </div>

                        <div class="form-group col-md-3 mb-0">
                            <input type="text" class="col-12" placeholder="{{ $invoice_item->country->name }}" disabled>
                        </div>

                        <div class="form-group col-md-2 mb-0">
                            <input type="{{$hidden_prices ? 'password' : 'text'}}" style="float: left!important" class="col-12" value="{{ $invoice_item->amount }}" disabled>
                            <div style="float: left!important; position: absolute; margin-left: 130px;">
                                <svg class="fuction-btn" wire:click="$emit('trigger_custom_item_delete',{{ $loop->index }})"
                                    xmlns="http://www.w3.org/2000/svg"
                                    style="width: 20px; height:20px; margin-top: 13px; color: rgb(89,198,198); cursor: pointer;"
                                    fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif

            @if ($invoiceData->currency)
                <div>
                    <button type="button" class="btn-radius" wire:click="show_custom_item_dialog">
                    <span class="mr-1" style="font-size: 25px">+</span>
                    {{__('invoice.custom-item-add')}}
                    </button>
                </div>
            @endif
        </div>

        <div style="margin-bottom:70px">
            <h1 style="font-size: 20px;">{{ __('expert-data.professional-informations') }}:</h1>
            <div>
                <div class="d-flex flex-row w-100 ml-0 {{ $expertData->is_cgp_employee ? 'd-none' : '' }}">
                    <input type="text" style="margin-right:15px!important;" placeholder="{{ __('expert-data.scanned-contract') }}"
                            disabled>
                    <div class="d-flex justify-content-end">
                        <button type="button" id="uploadContract" style="--btn-height:48px; --btn-margin-right: 0px"
                            class="text-center btn-radius">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="width:20px; height:20px;"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                            {{ __('common.upload') }}
                        </button>
                    </div>
                </div>
            </div>

            <div
                class="{{ count($contracts) || count($existing_contracts) ? 'mb-5' : '' }} {{ $expertData->is_cgp_employee ? 'd-none' : '' }}">
                @if (count($contracts))
                    @foreach ($contracts as $id => $contract)
                        <div>
                            <div class="d-flex flex-column flex-md-row w-100 ml-0">
                                <input type="text" class="col-12 dark"
                                    placeholder="{{ $contract->getClientOriginalName() }}" disabled>
                                <div class="d-flex justify-content-end">
                                    <button wire:click="removeFileFromTempContracts({{ $id }})" type="button"
                                        style="--btn-height: 48px; --btn-margin-left: var(--btn-margin-x); --btn-margin-right: 0px;
                                        background: rgb(0,87,95); padding-left: 0;"
                                        class="text-center btn-radius">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-1"
                                            style="width:20px; height:20px;" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                        {{ __('common.cancel') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif

                @if (count($existing_contracts))
                    @foreach ($existing_contracts as $contract)
                        <div>
                            <div class="d-flex flex-column flex-md-row w-100 ml-0">
                                <input type="text" class="dark" placeholder="{{ $contract->filename }}"
                                    disabled>
                                <div class="d-flex justify-content-end">
                                    <button wire:click="downloadExistingFile({{ $contract->id }})" type="button"
                                        style="background: rgb(0,87,95); padding-left: 0;
                                        --btn-height: 48px; --btn-margin-left: var(--btn-margin-x);"
                                        class="text-center btn-radius">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-1"
                                            style="width:20px; height:20px;" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                        </svg>
                                        {{ __('expert-data.download') }}
                                    </button>
                                    <button onclick="deleteExisitingFile({{ $contract->id }})" type="button"
                                        style="background: rgb(0,87,95); padding-left: 0;
                                        --btn-height: 48px; --btn-margin-right: 0px;"
                                        class="text-center btn-radius">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-1"
                                            style="width:20px; height:20px;" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>

                                        {{ __('common.delete') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            <div>
                <div class="d-flex flex-row w-100 ml-0 {{ $expertData->is_cgp_employee ? 'd-none' : '' }}">
                    <input type="text" style="margin-right:15px!important;" placeholder="{{ __('expert-data.scanned-certificate') }}"
                        disabled>
                    <button id="uploadCertificate" type="button" style="--btn-margin-right: 0px; --btn-height:48px"
                        class="text-center btn-radius">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="width:20px; height:20px;"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        {{ __('common.upload') }}
                    </button>
                </div>
            </div>

            <div
                class="{{ count($certificates) || count($existing_certificates) ? 'mb-5' : '' }} {{ $expertData->is_cgp_employee ? 'd-none' : '' }}">
                @if (count($certificates))
                    @foreach ($certificates as $id => $certificate)
                        <div>
                            <div class="d-flex flex-column flex-md-row w-100 ml-0">
                                <input type="text" class="dark"
                                    placeholder="{{ $certificate->getClientOriginalName() }}" disabled>
                                <div class="d-flex justify-content-end">
                                    <button wire:click="removeFileFromTempCertificates({{ $id }})"
                                        type="button"
                                        style="--btn-height: 48px; --btn-margin-left: var(--btn-margin-x); --btn-margin-right: 0px;
                                        background: rgb(0,87,95); padding-left: 0;"
                                        class="text-center btn-radius">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-1"
                                            style="width:20px; height:20px;" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                        {{ __('common.cancel') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif

                @if (count($existing_certificates))
                    @foreach ($existing_certificates as $cretificate)
                        <div>
                            <div class="d-flex flex-column flex-md-row w-100 ml-0">
                                <input type="text" class="dark" placeholder="{{ $cretificate->filename }}"
                                    disabled>
                                <div class="d-flex justify-content-end">
                                    <button wire:click="downloadExistingFile({{ $cretificate->id }})" type="button"
                                        style="background: rgb(0,87,95); padding-left: 0;
                                        --btn-height: 48px; --btn-margin-left: var(--btn-margin-x);"
                                        class="text-center btn-radius">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-1"
                                            style="width:20px; height:20px;" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                        </svg>
                                        {{ __('expert-data.download') }}
                                    </button>
                                    <button onclick="deleteExisitingFile({{ $cretificate->id }})" type="button"
                                        style="background: rgb(0,87,95); padding-left: 0;
                                        --btn-height: 48px; --btn-margin-right: 0px;"
                                        class="text-center btn-radius">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-1"
                                            style="width:20px; height:20px;" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        {{ __('common.delete') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            <div class="form-group @if ($showPsychologistData && $expertData->crisis_psychologist) d-bloc @else d-none @endif">
                <div class="input-group col-12 p-0" wire:ignore>
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            {{ __('expert-data.crisis_countries') }}:
                        </div>
                    </div>
                    <select id="crisis_countries" multiple class="chosen-select" wire:ignore>
                        @foreach ($countries as $country)
                            <option @if (in_array($country->id, $user->expertCrisisCountries->pluck('id')->toArray())) selected @endif value='{{ $country->id }}'>
                                {{ $country->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group" wire:ignore>
                <div class="input-group col-12 p-0">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            {{ __('common.country') }}:
                        </div>
                    </div>
                    <select id="countries" multiple class="chosen-select" wire:ignore>
                        @foreach ($countries as $country)
                            <option @if (in_array($country->id, $user->expertCountries->pluck('id')->toArray())) selected @endif value='{{ $country->id }}'>
                                {{ $country->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>


            <div class="form-group" wire:ignore>
                <div class="input-group col-12 p-0">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            {{ __('crisis.city') }}:
                        </div>
                    </div>
                    <select id="cities" multiple class="chosen-select" wire:ignore>
                        @foreach ($cities as $city)
                            <option @if (in_array($city->id, $user->cities->pluck('id')->toArray())) selected @endif value='{{ $city->id }}'>
                                {{ $city->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group" wire:ignore>
                <div class="input-group col-12 p-0">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            WS/CI/O {{ __('common.country') }}:
                        </div>
                    </div>

                    <select id="outsource_countries" multiple class="chosen-select" wire:ignore>
                        @foreach ($countries as $country)
                            <option @if (in_array($country->id, $user->outsource_countries->pluck('id')->toArray())) selected @endif value='{{ $country->id }}'>
                                {{ $country->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group">
                <div class="input-group col-12 p-0" wire:ignore>
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            {{ __('common.areas-of-experties') }}:
                        </div>
                    </div>
                    <select wire:ignore id="permissions" multiple class="chosen-select" wire:ignore>
                        @foreach ($permissions as $permission)
                            <option @if (in_array($permission->id, $user->permission->pluck('id')->toArray())) selected @endif value='{{ $permission->id }}'>
                                {{ $permission->translation->value }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group @if ($showPsychologistData) d-bloc @else d-none @endif">
                <div class="input-group col-12 p-0">
                    <label class="checkbox-container mt-0 mb-0 w-100"
                        style="color: rgb(89, 198, 198); padding: 14px 0 14px 15px; border: 2px solid rgb(89,198,198) !important; font-size: 16px; margin-top: 8px;">
                        {{ __('common.crisis-psychologist') }}
                        <input type="checkbox" class="" wire:model="crisisPsychologist">
                        <span class="checkmark d-flex justify-content-center align-items-center"
                            style="left:auto; right: 0; height: 100%; width: 50px; border-left: 2px solid rgb(89,198,198) !important; {{ !$crisisPsychologist ? 'background-color: #eee !important;' : '' }}">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="checked {{ !$crisisPsychologist ? 'd-none' : '' }}"
                                style="width: 25px; height: 25px; color: white" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="unchecked {{ $crisisPsychologist ? 'd-none' : '' }}"
                                style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </span>
                    </label>
                </div>
            </div>
            <div class="form-group @if ($showPsychologistData) d-block @else d-none @endif">
                <div class="input-group col-12 p-0" wire:ignore>
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            {{ __('expert-data.specialization') }}:
                        </div>
                    </div>
                    <select wire:ignore id="specializations" multiple class="chosen-select" wire:ignore>
                        @foreach ($specializations as $specialization)
                            <option @if (in_array($specialization->id, $user->specializations->pluck('id')->toArray())) selected @endif
                                value="{{ $specialization->id }}">
                                {{ optional($specialization->translation)->value }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="input-group col-12 p-0">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        {{ __('expert-data.native_language') }}:
                    </div>
                </div>
                <select required wire:model="expertData.native_language">
                    <option value="null" disabled>{{ __('common.please-choose') }}</option>
                    @foreach($languageSkills as $language)
                      <option value="{{$language->id}}">{{$language->translation->value}}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <div class="input-group col-12 p-0" wire:ignore>
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            {{ __('expert-data.language_skills') }}:
                        </div>
                    </div>
                    <select wire:ignore id="languageSkills" multiple class="chosen-select" wire:ignore>
                        @foreach ($languageSkills as $languageSkill)
                            <option @if (in_array($languageSkill->id, $user->language_skills->pluck('id')->toArray())) selected @endif
                                value="{{ $languageSkill->id }}">
                                {{ $languageSkill->translation->value }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6 mb-0">
                    <div class="input-group col-12 p-0">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                {{ __('expert-data.max_inprogress_cases') }}:
                            </div>
                        </div>
                        <input type="text" class="col-12" wire:model="expertData.max_inprogress_cases">
                    </div>
                </div>

                <div class="form-group col-md-6 mb-0">
                    <div class="input-group col-12 p-0">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                {{ __('expert-data.min_inprogress_cases') }}:
                            </div>
                        </div>
                        <input type="text" class="col-12" wire:model="expertData.min_inprogress_cases">
                    </div>
                </div>
            </div>
        </div>

        <div style="margin-bottom:70px">
            <h1 style="font-size: 20px;">{{ __('expert-data.expert-dashboard-informations') }}:</h1>
            <div class="input-group col-12 p-0">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        {{ __('common.username') }}:
                    </div>
                </div>
                <input type="text" wire:model='user.username' >
            </div>
            <div class="input-group col-12 p-0">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        {{ __('operator-data.dashboard_language') }}:
                    </div>
                </div>
                <select required wire:model="user.language_id">
                    @foreach($languages as $language)
                      <option value="{{$language->id}}">{{$language->name}}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div style="margin-bottom:70px" class="{{$expertData->is_eap_online_expert ? '' : 'd-none'}}">
            <h1 style="font-size: 20px;" >{{__('expert-data.eap-online-informations')}}:</h1>

            <div class="d-flex flex-row w-100 ml-0">
                <input type="text" style="margin-right:15px!important;" placeholder="@if(empty($eapOnlineData->image)){{__('prizegame.gallery.photo')}}@else{{basename($eapOnlineData->image)}}@endif" disabled>
                <div class="justify-content-end {{empty($eapOnlineData->image) ? 'd-flex' : 'd-none'}}">
                    <button type="button" id="uploadEapOnlinePhoto" style="--btn-height:48px; --btn-margin-right: 0px"
                        class="text-center btn-radius">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="width:20px; height:20px;"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        {{ __('common.upload') }}
                    </button>
                </div>
                <div class="justify-content-end {{empty($eapOnlineData->image) ? 'd-none' : 'd-flex '}}">
                    <button type="button" wire:click="deleteEapOnlinePhoto" style="--btn-height:48px; --btn-margin-right: 0px"
                        class="text-center btn-radius">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="width:20px; height:20px;"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        {{__('common.cancel')}}
                    </button>
                </div>
            </div>

            @if(!empty($eapOnlineData->image))
                <div class="form-row">
                    <div class="form-group" style="padding: 0 5px;">
                        <img class="col-12" src="{{asset('assets/' . $eapOnlinePhoto)}}" alt="preview" style="border:2px solid rgb(89,198,198); border-radius: 1rem; width:200px; height:200px; padding:0; object-fit:cover;">
                    </div>
                </div>
            @endif

            <div class="input-group col-12 p-0">
                <textarea wire:model="eapOnlineData.description" rows="10" class="mr-0" maxlength="180" placeholder="{{__('expert-data.description')}}"></textarea>
            </div>
        </div>


        <div style="margin-bottom:70px" class="{{!empty($expertCurrencyChange->document) ? '' : 'd-none'}}">
            <h1 style="font-size: 20px;">{{ __('currency-change.menu') }}:</h1>
            <div>
                <div class="d-flex flex-column flex-md-row w-100 ml-0">
                    <input type="text" class="dark" placeholder="{{ __('currency-change.type') }}"
                        disabled>
                    <div class="d-flex justify-content-end">
                        <button wire:click="downloadExpertCurrencyChangeDocument()" type="button"
                            style="background: rgb(0,87,95); padding-left: 0;
                            --btn-height: 48px; --btn-margin-left: var(--btn-margin-x);"
                            class="text-center btn-radius">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-1"
                                style="width:20px; height:20px;" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            {{ __('expert-data.download') }}
                        </button>
                        <button onclick="deleteExpertCurrencyChangeDocument()" type="button"
                            style="background: rgb(0,87,95); padding-left: 0;
                            --btn-height: 48px; --btn-margin-right: 0px;"
                            class="text-center btn-radius">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-1"
                                style="width:20px; height:20px;" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            {{ __('common.delete') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <input id="contracts" class="d-none" type="file" multiple wire:model='contracts'>
        <input id="certificates" class="d-none" type="file" multiple wire:model='certificates'>
        <input id="eapOnlinePhoto" class="d-none" type="file" wire:model='eapOnlinePhoto'>

        <div class="w-full mt-5 d-flex">
            <button type="submit" style="width:auto;" class="button btn-radius ml-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 mb-1" style="width: 20px; height:20px;"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                </svg>
                <span class="mt-1">{{__('common.save')}}</span>
            </button>
        </div>
    </form>
</div>
