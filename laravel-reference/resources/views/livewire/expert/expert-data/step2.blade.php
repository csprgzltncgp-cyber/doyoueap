@push('livewire_js')
    <script src="/assets/js/chosen.js" type="text/javascript" charset="utf-8"></script>
    <script>
        $('#uploadContract').click(function() {
            $('#contracts').click();
        });

        $('#uploadCertificate').click(function() {
            $('#certificates').click();
        });

        Livewire.on('expertDataUpdated', function() {
            Swal.fire({
                title: '{{ __('common.successful-change') }}',
                text: '',
                icon: 'success',
                confirmButtonText: 'Ok'
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

        function showContactFinancePopup(type = 'finance') {
            let title = '';

            if (type == 'finance') {
                title = '{{ __('invoice.failed_currency_change_title') }}';
            } else {
                title = '{{ __('invoice.failed_hourly_rate_change_title') }}';
            }

            Swal.fire({
                title: title,
                text: '{{ __('invoice.failed_currency_change_text') }}',
                icon: 'warning',
            })
        }

        $("#specializations").chosen().change(function(e) {
            @this.set('expertSpecializations', $(e.target).val());
        });

        $("#languageSkills").chosen().change(function(e) {
            @this.set('expertLanguageSkills', $(e.target).val());
        });

        @if ($missingData)
            Swal.fire({
                html: `
                    <div class="text-center" style="margin-bottom:40px; font-size: 25px">
                        {{ __('common.invoice-missing-expert-data') }}
                    </div>
                    <div class="text-center" style="margin-bottom:40px; font-size: 20px">
                        {{ __('common.invoice-missing-expert-data-redirect') }}
                    </div>
                    `,
                icon: 'warning',
                confirmButtonText: 'Ok'
            }).then(function(result) {
                Livewire.emit('save');
            });
        @endif

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
    </script>
@endpush

<div>
    @section('title')
        Expert Dashboard
    @endsection
    <link rel="stylesheet" href="{{ asset('assets/css/form.css?v=') . time() }}">
    <link rel="stylesheet" href="/assets/css/perfix-input.css?v={{ time() }}">
    <link href="{{ asset('assets/css/chosen.css') }}" rel="stylesheet" type="text/css">
    <style>
        .button {
            padding: 10px 30px 10px 40px;
            background: rgb(89, 198, 198);
            color: white;
            text-transform: uppercase;
            border: none;
            margin-top: 10px;
            margin-bottom: 20px;
            margin-left: 20px;
        }

        .button:hover {
            color: white;
            text-decoration: none;
        }

        form {
            max-width: 750px;
        }

        .form-group {
            margin-bottom: 0;
        }

        .input-group-text.error {
            border-left: 2px solid rgb(219, 11, 32) !important;
            border-top: 2px solid rgb(219, 11, 32) !important;
            border-bottom: 2px solid rgb(219, 11, 32) !important;
            color: rgb(219, 11, 32) !important;
            margin-bottom: 0 !important;
        }

        input.error {
            border-right: 2px solid rgb(219, 11, 32) !important;
            border-left: 2px solid rgb(219, 11, 32) !important;
            border-top: 2px solid rgb(219, 11, 32) !important;
            border-bottom: 2px solid rgb(219, 11, 32) !important;
            color: rgb(219, 11, 32) !important;
            margin-bottom: 0 !important;
        }

        input.dark {
            color: rgb(0, 87, 95) !important;
            border-color: rgb(0, 87, 95) !important;
        }

        input.dark::placeholder {
            color: rgb(0, 87, 95) !important;
        }

        input.error::placeholder {
            color: rgb(219, 11, 32);
        }

        select.error {
            border-right: 2px solid rgb(219, 11, 32) !important;
            border-top: 2px solid rgb(219, 11, 32) !important;
            border-bottom: 2px solid rgb(219, 11, 32) !important;
            color: rgb(219, 11, 32) !important;
            margin-bottom: 0 !important;
        }

        select.error::placeholder {
            color: rgb(219, 11, 32);
        }

        small.error {
            color: rgb(219, 11, 32);
            margin-top: -15px !important;
        }

        .form-group input {
            color: black !important;
        }

        .form-group select {
            color: black !important;
        }

        #specializations_chosen,
        #languageSkills_chosen {
            flex: 1 !important;
        }
    </style>
    @if ($currentUrl != route('expert.profile'))
        <style>
            #menu,
            #logged-in-as {
                display: none !important;
            }

            #logo {
                filter: brightness(0) saturate(100%) invert(20%) sepia(46%) saturate(2675%) hue-rotate(159deg) brightness(99%) contrast(103%);
            }

            header {
                background-color: white;
                pointer-events: none;
                cursor: default;
                text-decoration: none;
            }

            header p.text-uppercase {
                pointer-events: none;
                cursor: default;
                text-decoration: none;
                color: #00575f !important;
            }
        </style>
    @endif
    <h1>{{ __('expert-data.profile-menu-data') }}</h1>
    <form class="row" wire:submit.prevent='save'>
        <div class="col-12 col-sm-12">
            <h1 style="font-size: 20px;">{{ __('expert-data.contact-informations') }}:</h1>

            <div class="d-flex flex-column">
                <div class="input-group col-12 p-0">
                    <div class="input-group-prepend">
                        <div class="input-group-text @error('user.email') error @enderror">
                            {{ __('common.email') }}:
                        </div>
                    </div>
                    <input @error('user.email') class="error" @enderror wire:model="user.email" type="email">
                </div>
                @error('user.email')
                    <small class="error">{{ __('common.field-required') }}</small>
                    <div style="height: 20px;"></div>
                @enderror
            </div>

            <div class="form-row">
                <div class="form-group col-md-4">
                    <input type="text" class="col-12" placeholder="{{ __('expert-data.phone') }}" disabled>
                </div>

                <div class="form-group col-md-4">
                    <div class="d-flex flex-column">
                        <div class="input-group col-12 p-0">
                            <div class="input-group-prepend">
                                <div class="input-group-text @error('expertData.phone_prefix') error @enderror">
                                    {{ __('expert-data.phone_prefix') }}:
                                </div>
                            </div>
                            <select class="col-12 @error('expertData.phone_prefix') error @enderror"
                                wire:model="expertData.phone_prefix">
                                <option value="null" disabled>{{ __('common.please-choose') }}</option>
                                @foreach ($phonePrefixes as $phonePrefix)
                                    <option value="{{ $phonePrefix['code'] }}">{{ $phonePrefix['code'] }}
                                        {{ $phonePrefix['dial_code'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        @error('expertData.phone_prefix')
                            <small class="error">{{ __('common.field-required') }}</small>
                            <div style="height: 20px;"></div>
                        @enderror
                    </div>
                </div>

                <div class="form-group col-md-4">
                    <input wire:model='expertData.phone_number' type="number"
                        class="col-12 @error('expertData.phone_number') error @enderror"
                        @error('expertData.phone_number') style="border-left: 2px solid rgb(219, 11, 32) !important;" @enderror>
                    @error('expertData.phone_number')
                        <small class="error">{{ __('common.field-required') }}</small>
                        <div style="height: 20px;"></div>
                    @enderror
                </div>
            </div>

            <h1 style="font-size: 20px;">{{ __('expert-data.post-address') }}:</h1>

            <div class="form-group">
                <div class="d-flex flex-column">
                    <div class="input-group col-12 p-0">
                        <div class="input-group-prepend">
                            <div class="input-group-text @error('expertData.post_code') error @enderror">
                                {{ __('expert-data.post_code') }}:
                            </div>
                        </div>
                        <input type="text" class="col-12 @error('expertData.post_code') error @enderror"
                            wire:model='expertData.post_code'>
                    </div>
                    @error('expertData.post_code')
                        <small class="error">{{ __('common.field-required') }}</small>
                        <div style="height: 20px;"></div>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <div class="d-flex flex-column">
                    <div class="input-group col-12 p-0">
                        <div class="input-group-prepend">
                            <div class="input-group-text @error('expertData.country_id') error @enderror">
                                {{ __('expert-data.country') }}:
                            </div>
                        </div>
                        <select class="col-12 @error('expertData.country_id') error @enderror"
                            wire:model='expertData.country_id'>
                            <option value="null" disabled>{{ __('common.please-choose') }}</option>
                            @foreach ($countries as $country)
                                <option value="{{ $country->id }}">{{ $country->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @error('expertData.country_id')
                        <small class="error">{{ __('common.field-required') }}</small>
                        <div style="height: 20px;"></div>
                    @enderror
                </div>
            </div>
            <div class="form-group">
                <div class="d-flex flex-column">
                    <div class="input-group col-12 p-0">
                        <div class="input-group-prepend">
                            <div class="input-group-text @error('expertData.city_id') error @enderror">
                                {{ __('expert-data.city') }}:
                            </div>
                        </div>
                        <select class="col-12 @error('expertData.city_id') error @enderror"
                            wire:model='expertData.city_id'>
                            <option value="null" disabled>{{ __('common.please-choose') }}</option>
                            @foreach ($cities as $city)
                                <option value="{{ $city->id }}">{{ $city->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @error('expertData.city_id')
                        <small class="error">{{ __('common.field-required') }}</small>
                        <div style="height: 20px;"></div>
                    @enderror
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <div class="d-flex flex-column">
                        <div class="input-group col-12 p-0">
                            <div class="input-group-prepend">
                                <div class="input-group-text @error('expertData.street') error @enderror">
                                    {{ __('expert-data.street') }}:
                                </div>
                            </div>
                            <input type="text" class="col-12 @error('expertData.street') error @enderror"
                                wire:model='expertData.street'>
                        </div>
                        @error('expertData.street')
                            <small class="error">{{ __('common.field-required') }}</small>
                            <div style="height: 20px;"></div>
                        @enderror
                    </div>
                </div>
                <div class="form-group col-md-6">
                    <div class="d-flex flex-column">
                        <div class="input-group col-12 p-0">
                            <div class="input-group-prepend">
                                <div class="input-group-text @error('expertData.street_suffix') error @enderror">
                                    {{ __('expert-data.street_suffix.title') }}:
                                </div>
                            </div>
                            <select class="col-12 @error('expertData.street_suffix') error @enderror"
                                wire:model='expertData.street_suffix'>
                                <option value="null">{{ __('common.please-choose') }}</option>
                                @foreach ($streetSuffixes as $streetSuffix)
                                    <option value="{{ $streetSuffix['id'] }}">{{ $streetSuffix['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        @error('expertData.street_suffix')
                            <small class="error">{{ __('common.field-required') }}</small>
                            <div style="height: 20px;"></div>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="d-flex flex-column">
                    <div class="input-group col-12 p-0">
                        <div class="input-group-prepend">
                            <div class="input-group-text @error('expertData.house_number') error @enderror">
                                {{ __('expert-data.house_number') }}:
                            </div>
                        </div>
                        <input type="text" class="col-12 @error('expertData.house_number') error @enderror"
                            wire:model='expertData.house_number'>
                    </div>
                    @error('expertData.house_number')
                        <small class="error">{{ __('common.field-required') }}</small>
                        <div style="height: 20px;"></div>
                    @enderror
                </div>
            </div>


            <h1 style="font-size: 20px;">{{ __('expert-data.invoice-informations') }}:</h1>
            <div class="form-row">
                <div class="form-group col-md-10">
                    <input type="text" class="col-12" placeholder="{{ __('invoice.currency') }}" disabled>
                </div>

                <div class="form-group col-md-2" onclick="showContactFinancePopup('finance')">
                    <select class="col-12 @error('invoiceData.currency') error @enderror"
                        wire:model='invoiceData.currency' required disabled>
                        <option value="null">{{ __('common.please-choose') }}</option>
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
                    @error('invoiceData.currency')
                        <small class="error">{{ __('common.field-required') }}</small>
                        <div style="height: 20px;"></div>
                    @enderror
                </div>
            </div>

            @if($invoiceData->invoicing_type !== \App\Enums\InvoicingType::TYPE_NORMAL)
                <div class="form-row">
                    <div class="form-group col-md-7">
                        <input type="text" class="col-12" placeholder="{{ __('invoice.hourly_rate_50') }}" disabled>
                    </div>

                    <div class="form-group col-md-3">
                        <input type="text" class="col-12"
                            placeholder="{{ __('common.period') }} 50 {{ __('crisis.minute') }}" disabled>
                    </div>

                    <div class="form-group col-md-2" onclick="showContactFinancePopup('hourly_rate')">
                        <input type="text" class="col-12 @error('invoiceData.hourly_rate_50') error @enderror"
                            wire:model='invoiceData.hourly_rate_50' required disabled>
                        @error('invoiceData.hourly_rate_50')
                            <small class="error">{{ __('common.field-required') }}</small>
                            <div style="height: 20px;"></div>
                        @enderror
                    </div>
                </div>

                @if ($user->hasPermission(2) || $user->hasPermission(3) || $user->hasPermission(7))
                    <div class="form-row">
                        <div class="form-group col-md-7">
                            <input type="text" class="col-12" placeholder="{{ __('invoice.hourly_rate_30') }}"
                                disabled>
                        </div>

                        <div class="form-group col-md-3">
                            <input type="text" class="col-12"
                                placeholder="{{ __('common.period') }} 30 {{ __('crisis.minute') }}" disabled>
                        </div>

                        <div class="form-group col-md-2" onclick="showContactFinancePopup('hourly_rate')">
                            <input type="text" class="col-12 @error('invoiceData.hourly_rate_30') error @enderror"
                                wire:model='invoiceData.hourly_rate_30' disabled required>
                            @error('invoiceData.hourly_rate_30')
                                <small class="error">{{ __('common.field-required') }}</small>
                                <div style="height: 20px;"></div>
                            @enderror
                        </div>
                    </div>
                @endif
            @endif


            <h1 style="font-size: 20px;">{{ __('expert-data.professional-informations') }}:</h1>
            <div>
                <div class="d-flex flex-column flex-md-row w-100 ml-0">
                    <div class="d-flex flex-column w-100">
                        <input @error('contracts') style="border-left: 2px solid rgb(219, 11, 32) !important;" @enderror
                        type="text" class="@error('contracts') error @enderror"
                        placeholder="{{ __('expert-data.scanned-contract') }}" disabled>
                        @error('contracts')
                            <small class="error">{{ __('common.field-required') }}</small>
                        @enderror
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="button" id="uploadContract" style="--btn-height: 48px; --btn-margin-left: var(--btn-margin-x); --btn-margin-right: 0px;"
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

            <div>
                @if (count($contracts))
                    @foreach ($contracts as $id => $contract)
                        <div>
                            <div class="d-flex flex-column flex-md-row w-100 ml-0">
                                <input type="text" class="dark"
                                    placeholder="{{ $contract->getClientOriginalName() }}" disabled>
                                <div class="d-flex justify-content-end">
                                    <button wire:click="removeFileFromTempContracts({{ $id }})" type="button"
                                        style="--btn-height: 48px; --btn-margin-left: var(--btn-margin-x);
                                        --btn-margin-right: 0px; background: rgb(0,87,95); padding-left: 0;"
                                        class="text-center btn-radius">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-1"
                                            style="width:20px; height:20px;" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                        <span>
                                            {{ __('common.cancel') }}
                                        </span>
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
                                        style="--btn-height: 48px; --btn-margin-left: var(--btn-margin-x); background: rgb(0,87,95);"
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
                                        style="--btn-height: 48px; --btn-margin-right: 0px; background: rgb(0,87,95);"
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
                <div class="d-flex flex-column flex-md-row w-100 ml-0">
                    <input @error('certificates') style="border-left: 2px solid rgb(219, 11, 32) !important;" @enderror
                        type="text" class="@error('certificates') error @enderror"
                        placeholder="{{ __('expert-data.scanned-certificate') }}" disabled>
                    @error('certificates')
                        <small class="error">{{ __('common.field-required') }}</small>
                        <div style="height: 20px;"></div>
                    @enderror
                    <div class="d-flex justify-content-end">
                        <button id="uploadCertificate" type="button" style="--btn-height: 48px; --btn-margin-left: var(--btn-margin-x); --btn-margin-right: 0px;"
                        class="text-center btn-radius d-flex">
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

            <div>
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
                                        background: rgb(0,87,95);"
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
                                        style="--btn-height: 48px; --btn-margin-left: var(--btn-margin-x); background: rgb(0,87,95);"
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
                                        style="--btn-height: 48px; background: rgb(0,87,95); --btn-margin-right: 0px;"
                                        class="text-center btn-radius w-auto;">
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

            <input id="contracts" class="d-none" type="file" multiple wire:model='contracts'>
            <input id="certificates" class="d-none" type="file" multiple wire:model='certificates'>

            @if ($user->hasPermission(1))
                <div class="form-group skill-ignore">
                    <div class="input-group col-12 p-0" wire:ignore>
                        <div class="input-group-prepend">
                            <div class="input-group-text @error('expertSpecializations') error @enderror">
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
                    @error('expertSpecializations')
                        <small class="error">{{ __('common.field-required') }}</small>
                        <div style="height: 20px;"></div>
                        <style>
                            .skill-ignore .input-group-text {
                                border-color: red !important;
                                color: red !important;
                            }

                            .skill-ignore ul {
                                border-color: red !important;
                            }
                        </style>
                    @enderror
                </div>
            @endif

            <div class="form-group lang-ignore">
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
                                {{ optional($languageSkill->translation)->value }}</option>
                        @endforeach
                    </select>
                </div>
                @error('expertLanguageSkills')
                    <small class="error">{{ __('common.field-required') }}</small>
                    <div style="height: 20px;"></div>
                    <style>
                        .lang-ignore .input-group-text {
                            border-color: rgb(219, 11, 32) !important;
                            color: rgb(219, 11, 32) !important;
                        }

                        .lang-ignore ul {
                            border-color: rgb(219, 11, 32) !important;
                        }
                    </style>
                @enderror
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

            @if ($currentUrl == route('expert.profile'))
                <div class="w-full mt-5 d-flex">
                    <button type="submit" style="width:auto;" class="button btn-radius ml-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 mb-1" style="width: 20px; height:20px;"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                        </svg>
                        {{ __('common.save') }}
                    </button>
                </div>
            @else
                <div class="w-full mt-5 d-flex justify-content-center">
                    <button type="submit" style="width:auto;" class="button">
                        {{ __('expert-data.next') }}
                        <svg xmlns="http://www.w3.org/2000/svg" class="ml-1" style="width: 20px; height:20px;"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </button>
                </div>
            @endif
        </div>
    </form>
</div>
