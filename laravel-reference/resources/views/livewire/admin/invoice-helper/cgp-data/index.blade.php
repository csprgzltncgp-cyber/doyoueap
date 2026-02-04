@push('livewire_js')
    <script>
        document.addEventListener('livewire:load', function () {
            Livewire.on('cgpDataUpdated', function(){
                Swal.fire({
                    title: '{{__('common.successful-change')}}',
                    text: '',
                    icon: 'success',
                    confirmButtonText: 'Ok'
                });
            });
        });
    </script>
@endpush

<div>
    <link rel="stylesheet" href="/assets/css/form.css?v={{time()}}">
    <link rel="stylesheet" href="/assets/css/perfix-input.css?v={{time()}}">

    <form wire:submit.prevent='update' style="max-width: 1500px !important;">
        <p>{{__('invoice-helper.cgp-data.datas')}}:</p>

        <div class="input-group col-md-6 p-0">
            <div class="input-group-prepend">
                <div class="input-group-text">
                    {{__('invoice-helper.cgp-data.company-name')}}:
                </div>
            </div>
            <input type="text" wire:model='data.company_name' required>
        </div>

        <div class="input-group col-md-6 p-0">
            <div class="input-group-prepend">
                <div class="input-group-text">
                    {{__('invoice-helper.cgp-data.country')}}:
                </div>
            </div>
            <input type="text" wire:model='data.country' required>
        </div>

        <div class="form-row">
            <div class="form-group col-md-2 mb-0">
                <div class="d-flex flex-column">
                    <div class="input-group col-12 p-0">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                {{__('invoice-helper.cgp-data.post-code')}}:
                            </div>
                        </div>
                        <input type="text" class="col-12" wire:model='data.post_code'>
                    </div>
                </div>
            </div>

            <div class="form-group col-md-4 mb-0 pr-md-0">
                <div class="d-flex flex-column">
                    <div class="input-group col-12 p-0">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                {{__('invoice-helper.cgp-data.city')}}:
                            </div>
                        </div>
                        <input type="text" class="col-12" wire:model='data.city'>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-4 mb-0">
                <div class="d-flex flex-column">
                    <div class="input-group col-12 p-0">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                {{__('invoice-helper.cgp-data.street')}}:
                            </div>
                        </div>
                        <input type="text" class="col-12" wire:model='data.street'>
                    </div>
                </div>
            </div>

            <div class="form-group col-md-2 mb-0 pr-md-0">
                <div class="d-flex flex-column">
                    <div class="input-group col-12 p-0">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                {{__('invoice-helper.cgp-data.house-number')}}:
                            </div>
                        </div>
                        <input type="text" class="col-12" wire:model='data.house_number'>
                    </div>
                </div>
            </div>
        </div>

        <div class="input-group col-md-6 p-0">
            <div class="input-group-prepend">
                <div class="input-group-text">
                    {{__('invoice-helper.cgp-data.vat-number')}}:
                </div>
            </div>
            <input type="text" wire:model='data.vat_number' required>
        </div>

        <div class="input-group col-md-6 p-0">
            <div class="input-group-prepend">
                <div class="input-group-text">
                    {{__('invoice-helper.cgp-data.eu-vat-number')}}:
                </div>
            </div>
            <input type="text" wire:model='data.eu_vat_number' required>
        </div>

        @if(count($accountNumbers) <= 1)
            <div class="form-row">
                <div class="form-group col-md-4 mb-0">
                    <div class="d-flex flex-column">
                        <div class="input-group col-12 p-0">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    {{__('invoice-helper.cgp-data.account-number')}}:
                                </div>
                            </div>
                            <input type="text" class="col-12" wire:model='accountNumbers.0.account_number'>
                        </div>
                    </div>
                </div>

                <div class="form-group col-md-2 mb-0 pr-md-0">
                    <select wire:model="accountNumbers.0.currency">
                        <option value="{{null}}">{{__('common.currency')}}</option>
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
                </div>

                <div class="form-group col-md-4 mb-md-0 p-0">
                    <button wire:click="addNewAccountNumber" type="button" class="button m-0 d-flex justify-content-center"
                    style="border: 2px solid rgb(89,198,198) !important; --btn-height: 64px; --btn-margin-left: var(--btn-margin-x)">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="width: 20px; height:20px;"fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>{{__('invoice-helper.cgp-data.add-new-account-number')}}</span>
                    </button>
                </div>
            </div>

            <div class="form-row">
                <div class="input-group col-md-6 pr-0">
                    <div class="input-group-prepend">
                        <div class="input-group-text text-uppercase">
                            {{__('invoice-helper.cgp-data.iban')}}:
                        </div>
                    </div>
                    <input type="text" wire:model='accountNumbers.0.iban' required>
                </div>
            </div>
        @else
            @foreach($accountNumbers as $account_number)
                <div wire:key='account-number-{{$account_number->id}}'>
                    <div class="form-row">
                        <div class="form-group col-md-4 mb-0">
                            <div class="d-flex flex-column">
                                <div class="input-group col-12 p-0">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            {{__('invoice-helper.cgp-data.account-number')}}:
                                        </div>
                                    </div>
                                    <input type="text" class="col-12" wire:model='accountNumbers.{{$loop->index}}.account_number'>
                                </div>
                            </div>
                        </div>

                        <div class="form-group col-md-2 mb-0 pr-md-0">
                            <select wire:model='accountNumbers.{{$loop->index}}.currency'>
                                <option value="{{null}}">{{__('common.currency')}}</option>
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
                        </div>

                        @if($loop->first)
                            <div class="form-group col-md-4 p-0">
                                <button wire:click="addNewAccountNumber" type="button" class="btn-radius"
                                style="border: 2px solid rgb(89,198,198) !important; --btn-height: auto; --btn-margin-left: var(--btn-margin-x)">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="width: 20px; height:20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                    </svg>
                                    <span>{{__('invoice-helper.cgp-data.add-new-account-number')}}</span>
                                </button>
                            </div>
                        @else
                            <div class="form-group col-md-2 mb-md-0 pl-md-2">
                                <svg wire:click="deleteAccountNumber({{$loop->index}})" xmlns="http://www.w3.org/2000/svg" style="width: 20px; height:20px; margin-top: 13px; color: rgb(89,198,198); cursor: pointer;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </div>
                        @endif
                    </div>

                    <div class="form-row">
                        <div class="input-group col-md-6 pr-0">
                            <div class="input-group-prepend">
                                <div class="input-group-text text-uppercase">
                                    {{__('invoice-helper.cgp-data.iban')}}:
                                </div>
                            </div>
                            <input type="text" wire:model='accountNumbers.{{$loop->index}}.iban' required>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif

        <div class="input-group col-md-6 p-0">
            <div class="input-group-prepend">
                <div class="input-group-text text-uppercase">
                    {{__('invoice-helper.cgp-data.swift')}}:
                </div>
            </div>
            <input type="text" wire:model='data.swift' required>
        </div>

        <div class="input-group col-md-6 p-0">
            <div class="input-group-prepend">
                <div class="input-group-text">
                    {{__('invoice-helper.cgp-data.email')}}:
                </div>
            </div>
            <input type="text" wire:model='data.email' required>
        </div>

        <div class="input-group col-md-6 p-0">
            <div class="input-group-prepend">
                <div class="input-group-text">
                    {{__('invoice-helper.cgp-data.website')}}:
                </div>
            </div>
            <input type="text" wire:model='data.website' required>
        </div>

        <div class="w-full mt-5 d-flex">
            <button type="submit" style="width:auto;" class="button btn-radius ml-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 mb-1" style="width: 20px; height:20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                </svg>
                <span>{{__('common.save')}}</span>
            </button>
        </div>
    </form>
</div>
