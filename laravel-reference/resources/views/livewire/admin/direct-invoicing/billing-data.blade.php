@push('livewire_js')
    <script>
        Livewire.on('directBillingDataEmailsError', message => {
            Swal.fire(
                message,
                '',
                'error'
            );
        });
    </script>
@endpush
<div>
    {{-- <div wire:loading.delay.remove> --}}
        <h1 style="font-size: 18px; color:black">{{__('company-edit.billing_datas')}}:</h1>

        <div class="input-group col-md-7 p-0">
            <div class="input-group-prepend">
                <div class="input-group-text"
                    @if(in_array('directBillingData.billing_frequency', $customErrors))style="border-color: red !important;" @endif
                >
                    {{__('company-edit.billing_frequency')}}:
                </div>
            </div>
            <select wire:model="directBillingData.billing_frequency"
                @if(in_array('directBillingData.billing_frequency', $customErrors))style="border-color: red !important;" @endif
            >
                <option value="{{null}}">{{__('common.please-choose')}}</option>
                <option value="{{App\Models\DirectBillingData::FREQUENCY_MONTHLY}}">{{__('company-edit.monthly')}}</option>
                <option value="{{App\Models\DirectBillingData::FREQUENCY_QUARTELY}}">{{__('company-edit.quarterly')}}</option>
                <option value="{{App\Models\DirectBillingData::FREQUENCY_YEARLY}}">{{__('company-edit.yearly')}}</option>
            </select>
        </div>

        <div class="input-group col-md-7 p-0">
            <div class="input-group-prepend">
                <div class="input-group-text"
                    @if(in_array('directBillingData.invoice_language', $customErrors))style="border-color: red !important;" @endif
                >
                    {{__('company-edit.invoice_language')}}:
                </div>
            </div>
            <select wire:model="directBillingData.invoice_language"
                @if(in_array('directBillingData.invoice_language', $customErrors))style="border-color: red !important;" @endif
            >
                <option value="{{null}}">{{__('common.please-choose')}}</option>
                <option value="en">English</option>
                <option value="hu">Magyar</option>
                <option value="de">Német</option>
            </select>
        </div>

        <div class="form-row">
            <div class="form-group col-md-5 mb-0">
                    <input type="text" disabled placeholder="{{__('company-edit.currency')}}"
                        @if(in_array('directBillingData.currency', $customErrors))style="border-color: red !important;" @endif
                    >
            </div>
            <div class="form-group col-md-2 mb-0">
                <select wire:model="directBillingData.currency"
                    @if(in_array('directBillingData.currency', $customErrors))style="border-color: red !important;" @endif
                >
                    <option value="{{null}}">{{__('common.please-choose')}}</option>
                    <option value="huf">HUF</option>
                    <option value="eur">EUR</option>
                    <option value="czk">CZK</option>
                    <option value="eur">EUR</option>
                    <option value="mdl">MDL</option>
                    <option value="oal">OAL</option>
                    <option value="pln">PLN</option>
                    <option value="ron">RON</option>
                    <option value="rsd">RSD</option>
                    <option value="usd">USD</option>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-5 mb-0">
                    <input type="text" disabled placeholder="{{__('company-edit.vat_rate')}}"
                        @if(in_array('directBillingData.vat_rate', $customErrors))style="border-color: red !important;" @endif
                    >
            </div>
            <div class="form-group col-md-2 mb-0">
                <input type="text" wire:model.lazy="directBillingData.vat_rate" placeholder="%"
                    @if(in_array('directBillingData.vat_rate', $customErrors))style="border-color: red !important;" @endif
                >
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-7 mb-0">
                <div class="input-group mb-0 @if(optional($directBillingData)->outside_eu) inactive @endif">
                    <label class="checkbox-container mt-0 w-100"
                        style="color: rgb(89, 198, 198); padding: 10px 0 10px 15px; border: 2px solid rgb(89,198,198) !important; font-size: 16px; margin-top: 8px;">
                        {{__('company-edit.inside_eu')}}
                        <input type="checkbox" class="delete_later d-none" wire:model="directBillingData.inside_eu" @if(optional($directBillingData)->outside_eu) disabled @endif>
                        <span class="checkmark d-flex justify-content-center align-items-center"
                            style="left:auto; right: 0; height: 100%; width: 50px; border-left: 2px solid rgb(89,198,198) !important">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="checked {{optional($directBillingData)->inside_eu ? '' : 'd-none'}}"
                                style="width: 25px; height: 25px; color: white" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="unchecked {{optional($directBillingData)->inside_eu ? 'd-none' : ''}}"
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

        <div class="form-row mb-5">
            <div class="form-group col-md-7 mb-0">
                <div class="input-group mb-0 @if(optional($directBillingData)->inside_eu) inactive @endif">
                    <label class="checkbox-container mt-0 w-100"
                        style="color: rgb(89, 198, 198); padding: 10px 0 10px 15px; border: 2px solid rgb(89,198,198) !important; font-size: 16px; margin-top: 8px;">
                        {{__('company-edit.outside_eu')}}
                        <input type="checkbox" class="delete_later d-none" wire:model="directBillingData.outside_eu" @if(optional($directBillingData)->inside_eu) disabled @endif>
                        <span class="checkmark d-flex justify-content-center align-items-center"
                            style="left:auto; right: 0; height: 100%; width: 50px; border-left: 2px solid rgb(89,198,198) !important">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="checked {{optional($directBillingData)->outside_eu ? '' : 'd-none'}}"
                                style="width: 25px; height: 25px; color: white" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="unchecked {{optional($directBillingData)->outside_eu ? 'd-none' : ''}}"
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

        <div class="form-row">
            <div class="form-group col-md-7 mb-0">
                <div class="input-group mb-0">
                    <label class="checkbox-container mt-0 w-100"
                        style="
                            color: rgb(89, 198, 198); padding: 10px 0 10px 15px; font-size: 16px; margin-top: 8px;
                            @if(in_array('directBillingData.send_invoice_by_post', $customErrors)) border: 2px solid red !important; @else border: 2px solid rgb(89,198,198) !important; @endif
                        ">
                        {{__('company-edit.send_invoice_by_post')}}
                        <input type="checkbox" class="delete_later d-none" wire:model="directBillingData.send_invoice_by_post">
                        <span class="checkmark d-flex justify-content-center align-items-center"
                            style="left:auto; right: 0; height: 100%; width: 50px; border-left: 2px solid rgb(89,198,198) !important">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="checked {{optional($directBillingData)->send_invoice_by_post ? '' : 'd-none'}}"
                                style="width: 25px; height: 25px; color: white" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="unchecked {{optional($directBillingData)->send_invoice_by_post ? 'd-none' : ''}}"
                                style="width: 20px; height: 20px;" fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </span>
                    </label>
                </div>
            </div>

            @if(optional($directBillingData)->send_invoice_by_post || optional($directBillingData)->send_completion_certificate_by_post)
                <div class="form-group col-md-2 mb-0">
                    <div class="input-group col-12 p-0">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                {{__('company-edit.postal_code')}}:
                            </div>
                        </div>
                        <input type="text" wire:model="directBillingData.post_code">
                    </div>
                </div>

                <div class="form-group col-md-3 mb-0">
                    <div class="input-group col-12 p-0">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                {{__('common.country')}}:
                            </div>
                        </div>
                        <input type="text" wire:model="directBillingData.country">
                    </div>
                </div>
            @endif
        </div>

        <div class="form-row">
            <div class="form-group col-md-7 mb-0">
                <div class="input-group mb-0">
                    <label class="checkbox-container mt-0 w-100"
                        style="color: rgb(89, 198, 198); padding: 10px 0 10px 15px; border: 2px solid rgb(89,198,198) !important; font-size: 16px; margin-top: 8px;">
                        {{__('company-edit.send_completion_certificate_by_post')}}
                        <input type="checkbox" class="delete_later d-none" wire:model="directBillingData.send_completion_certificate_by_post">
                        <span class="checkmark d-flex justify-content-center align-items-center"
                            style="left:auto; right: 0; height: 100%; width: 50px; border-left: 2px solid rgb(89,198,198) !important">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="checked {{optional($directBillingData)->send_completion_certificate_by_post ? '' : 'd-none'}}"
                                style="width: 25px; height: 25px; color: white" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="unchecked {{optional($directBillingData)->send_completion_certificate_by_post ? 'd-none' : ''}}"
                                style="width: 20px; height: 20px;" fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </span>
                    </label>
                </div>
            </div>

            @if(optional($directBillingData)->send_invoice_by_post || optional($directBillingData)->send_completion_certificate_by_post)
                <div class="form-group col-md-5 mb-0">
                    <div class="input-group col-12 p-0">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                {{__('crisis.city')}}:
                            </div>
                        </div>
                        <input type="text" wire:model="directBillingData.city">
                    </div>
                </div>
            @endif
        </div>

        @if(optional($directBillingData)->send_invoice_by_post || optional($directBillingData)->send_completion_certificate_by_post)
            <div class="form-row justify-content-end">
                <div class="form-group col-md-3 mb-0">
                    <div class="input-group col-12 p-0">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                {{__('expert-data.street')}}:
                            </div>
                        </div>
                        <input type="text" wire:model="directBillingData.street">
                    </div>
                </div>

                <div class="form-group col-md-2 mb-0">
                    <div class="input-group col-12 p-0">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                {{__('expert-data.house_number')}}:
                            </div>
                        </div>
                        <input type="text" wire:model="directBillingData.house_number">
                    </div>
                </div>
            </div>
        @endif

        <div class="form-row mt-5">
            <div class="form-group col-md-7 mb-0">
                <div class="input-group mb-0">
                    <label class="checkbox-container mt-0 w-100"
                        style="
                            color: rgb(89, 198, 198); padding: 10px 0 10px 15px; font-size: 16px; margin-top: 8px;
                            @if(in_array('directBillingData.send_invoice_by_email', $customErrors)) border: 2px solid red !important; @else border: 2px solid rgb(89,198,198) !important; @endif
                        ">
                        {{__('company-edit.send_invoice_by_email')}}
                        <input type="checkbox" class="delete_later d-none" wire:model="directBillingData.send_invoice_by_email">
                        <span class="checkmark d-flex justify-content-center align-items-center"
                            style="left:auto; right: 0; height: 100%; width: 50px; border-left: 2px solid rgb(89,198,198) !important">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="checked {{optional($directBillingData)->send_invoice_by_email ? '' : 'd-none'}}"
                                style="width: 25px; height: 25px; color: white" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="unchecked {{optional($directBillingData)->send_invoice_by_email ? 'd-none' : ''}}"
                                style="width: 20px; height: 20px;" fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </span>
                    </label>
                </div>
            </div>

            @if($directBillingData && (optional($directBillingData)->send_invoice_by_email || optional($directBillingData)->send_completion_certificate_by_email))
                <div class="form-group col-md-5 mb-0">
                    <div class="input-group col-12 p-0">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                {{__('common.email')}}:
                            </div>
                        </div>

                        <input type="text" wire:model.lazy="directBillingDataEmails.0.email" wire:key='direct-billing-data-email-0'/>
                    </div>
                </div>
            @endif
        </div>
        <div class="form-row">
            <div class="form-group col-md-7 mb-0">
                <div class="input-group mb-0">
                    <label class="checkbox-container mt-0 w-100"
                        style="color: rgb(89, 198, 198); padding: 10px 0 10px 15px; border: 2px solid rgb(89,198,198) !important; font-size: 16px; margin-top: 8px;">
                        {{__('company-edit.send_completion_certificate_by_email')}}
                        <input type="checkbox" class="delete_later d-none" wire:model="directBillingData.send_completion_certificate_by_email">
                        <span class="checkmark d-flex justify-content-center align-items-center"
                            style="left:auto; right: 0; height: 100%; width: 50px; border-left: 2px solid rgb(89,198,198) !important">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="checked {{optional($directBillingData)->send_completion_certificate_by_email ? '' : 'd-none'}}"
                                style="width: 25px; height: 25px; color: white" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="unchecked {{optional($directBillingData)->send_completion_certificate_by_email ? 'd-none' : ''}}"
                                style="width: 20px; height: 20px;" fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </span>
                    </label>
                </div>
            </div>

            @if($directBillingData && (optional($directBillingData)->send_invoice_by_email || optional($directBillingData)->send_completion_certificate_by_email))
                @if($directBillingDataEmails->count() <= 1)
                    <div class="form-group col-md-5 mb-0">
                        <button type="button" style="padding-bottom: 14px; --btn-height: 48px;" class="text-center btn-radius" wire:click="addBillingDataEmail">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="width:20px; height:20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                            </svg>
                            <span style="margin-top: 3px;">
                                {{__('company-edit.add_email_address')}}
                            </span>
                        </button>
                    </div>
                @else
                    <div class="form-group col-md-5 mb-0">
                        <div class="input-group col-12 p-0">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    {{__('common.email')}}:
                                </div>
                            </div>

                            <input type="text" wire:model.lazy="directBillingDataEmails.1.email" style="border-right: 0 !important;"  wire:key='direct-billing-data-email-1'/>

                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <svg wire:click="deleteBillingDataEmail({{$directBillingDataEmails[1]->id}})" xmlns="http://www.w3.org/2000/svg"
                                        style="width: 25px; height: 25px; color: rgb(89,198,198); cursor: pointer;" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>

                                    @if($directBillingDataEmails[1]->is_cc)
                                        <img wire:click="$set('directBillingDataEmails.1.is_cc', false)" src="{{asset('assets/img/invoice/cc-solid.svg')}}" style="width:25px; height:25px; margin-left:10px; cursor: pointer;"/>
                                    @else
                                        <img wire:click="$set('directBillingDataEmails.1.is_cc', true)" src="{{asset('assets/img/invoice/cc-outline.svg')}}" style="width:25px; height:25px; margin-left:10px; cursor: pointer;"/>
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                @endif
            @endif
        </div>

        <div class="form-row">
            <div class="form-group col-md-7 mb-0">
                <div class="input-group col-12 p-0">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            {{__('company-edit.custom_email_subject')}}:
                        </div>
                    </div>

                    <input type="text" wire:model.lazy="directBillingData.custom_email_subject"/>
                </div>
            </div>

            @if($directBillingData && (optional($directBillingData)->send_invoice_by_email || optional($directBillingData)->send_completion_certificate_by_email))
                @if($directBillingDataEmails->count() <= 2 && $directBillingDataEmails->count() > 1)
                    <div class="form-group col-md-5 mb-0">
                        <button type="button" style="padding-bottom: 14px; --btn-height: 48px;" class="text-center btn-radius" wire:click="addBillingDataEmail">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="width:20px; height:20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                            </svg>
                            <span style="margin-top: 3px;">
                                {{__('company-edit.add_email_address')}}
                            </span>
                        </button>
                    </div>
                @endif

                @if($directBillingDataEmails->count() > 2)
                    <div class="form-group col-md-5 mb-0">
                        <div class="input-group col-12 p-0">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    {{__('common.email')}}:
                                </div>
                            </div>

                            <input type="text" wire:model.lazy="directBillingDataEmails.2.email" style="border-right: 0 !important;" wire:key='direct-billing-data-email-2'/>

                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <svg wire:click="deleteBillingDataEmail({{$directBillingDataEmails[2]->id}})" xmlns="http://www.w3.org/2000/svg"
                                        style="width: 25px; height: 25px; color: rgb(89,198,198); cursor: pointer;" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>

                                    @if($directBillingDataEmails[2]->is_cc)
                                        <img wire:click="$set('directBillingDataEmails.2.is_cc', false)" src="{{asset('assets/img/invoice/cc-solid.svg')}}" style="width:25px; height:25px; margin-left:10px; cursor: pointer;"/>
                                    @else
                                        <img wire:click="$set('directBillingDataEmails.2.is_cc', true)" src="{{asset('assets/img/invoice/cc-outline.svg')}}" style="width:25px; height:25px; margin-left:10px; cursor: pointer;"/>
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                @endif
            @endif
        </div>

        @if($directBillingDataEmails && (optional($directBillingData)->send_invoice_by_email || optional($directBillingData)->send_completion_certificate_by_email))
            @if($directBillingDataEmails->count() >= 3)
                @foreach(collect($directBillingDataEmails)->skip(3) as $email)
                        <div class="form-row justify-content-end">
                            <div class="form-group col-md-5 mb-0">
                                <div class="input-group col-12 p-0" style="osition:relative;">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            {{__('common.email')}}:
                                        </div>
                                    </div>

                                    <input type="text" wire:model.lazy="directBillingDataEmails.{{$loop->index + 3}}.email" style="border-right: 0 !important;" wire:key='difrect-billing-email-{{$email->id}}'/>

                                    <div class="input-group-append">
                                        <span class="input-group-text">
                                            <svg wire:click="deleteBillingDataEmail({{$email->id}})" xmlns="http://www.w3.org/2000/svg"
                                                style="width: 25px; height: 25px; color: rgb(89,198,198); cursor: pointer;" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>

                                            @if($email->is_cc)
                                                <img wire:click="$set('directBillingDataEmails.{{$loop->index + 3}}.is_cc', false)" src="{{asset('assets/img/invoice/cc-solid.svg')}}" style="width:25px; height:25px; margin-left:10px; cursor: pointer;"/>
                                            @else
                                                <img wire:click="$set('directBillingDataEmails.{{$loop->index + 3}}.is_cc', true)" src="{{asset('assets/img/invoice/cc-outline.svg')}}" style="width:25px; height:25px; margin-left:10px; cursor: pointer;"/>
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                @endforeach


                <div class="form-row justify-content-end">
                    <div class="form-group col-md-5 mb-0">
                        <button type="button" style="padding-bottom: 14px; padding-left:0px; --btn-height: 48px;" class="text-center btn-radius" wire:click="addBillingDataEmail">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="width:20px; height:20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                            </svg>
                            <span style="margin-top: 3px;">
                                {{__('company-edit.add_email_address')}}
                            </span>
                        </button>
                    </div>
                </div>
            @endif
        @endif

        <div class="form-row mt-5">
            <div class="form-group col-md-7 mb-0">
                <div class="input-group mb-0">
                    <label class="checkbox-container mt-0 w-100"
                        style="
                            color: rgb(89, 198, 198); padding: 10px 0 10px 15px; font-size: 16px; margin-top: 8px;
                            @if(in_array('directBillingData.upload_invoice_online', $customErrors)) border: 2px solid red !important; @else border: 2px solid rgb(89,198,198) !important; @endif
                        ">
                        {{__('company-edit.upload_invoice_online')}}
                        <input type="checkbox" class="delete_later d-none" wire:model="directBillingData.upload_invoice_online">
                        <span class="checkmark d-flex justify-content-center align-items-center"
                            style="left:auto; right: 0; height: 100%; width: 50px; border-left: 2px solid rgb(89,198,198) !important">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="checked {{optional($directBillingData)->upload_invoice_online ? '' : 'd-none'}}"
                                style="width: 25px; height: 25px; color: white" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="unchecked {{optional($directBillingData)->upload_invoice_online ? 'd-none' : ''}}"
                                style="width: 20px; height: 20px;" fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </span>
                    </label>
                </div>
            </div>

            @if(optional($directBillingData)->upload_invoice_online)
                <div class="form-group col-md-5 mb-0">
                    <div class="input-group col-12 p-0">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                {{__('company-edit.url')}}:
                            </div>
                        </div>
                        <input type="text" wire:model="directBillingData.invoice_online_url">
                    </div>
                </div>
            @endif
        </div>

        <div class="form-row mb-5">
            <div class="form-group col-md-7 mb-0">
                <div class="input-group mb-0">
                    <label class="checkbox-container mt-0 w-100"
                        style="color: rgb(89, 198, 198); padding: 10px 0 10px 15px; border: 2px solid rgb(89,198,198) !important; font-size: 16px; margin-top: 8px;">
                        {{__('company-edit.upload_completion_certificate_online')}}
                        <input type="checkbox" class="delete_later d-none" wire:model="directBillingData.upload_completion_certificate_online">
                        <span class="checkmark d-flex justify-content-center align-items-center"
                            style="left:auto; right: 0; height: 100%; width: 50px; border-left: 2px solid rgb(89,198,198) !important">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="checked {{optional($directBillingData)->upload_completion_certificate_online ? '' : 'd-none'}}"
                                style="width: 25px; height: 25px; color: white" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="unchecked {{optional($directBillingData)->upload_completion_certificate_online ? 'd-none' : ''}}"
                                style="width: 20px; height: 20px;" fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </span>
                    </label>
                </div>
            </div>

            @if(optional($directBillingData)->upload_completion_certificate_online)
                <div class="form-group col-md-5 mb-0">
                    <div class="input-group col-12 p-0">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                {{__('company-edit.url')}}:
                            </div>
                        </div>
                        <input type="text" wire:model="directBillingData.completion_certificate_online_url">
                    </div>
                </div>
            @endif
        </div>

        <div class="form-row">
            <div class="form-group col-md-7 mb-0">
                <div class="input-group col-12 p-0">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            {{__('company-edit.contact_holder_name')}}:
                        </div>
                    </div>
                    <input type="text" wire:model="directBillingData.contact_holder_name">
                </div>
            </div>

            <div class="form-group col-md-3 mb-0">
                <div class="input-group mb-0">
                    <label class="checkbox-container mt-0 w-100"
                        style="color: rgb(89, 198, 198); padding: 10px 0 10px 15px; border: 2px solid rgb(89,198,198) !important; font-size: 16px; margin-top: 8px;">
                        {{__('company-edit.show_on_enevelope')}}
                        <input type="checkbox" class="delete_later d-none" wire:model="directBillingData.show_contact_holder_name_on_post">
                        <span class="checkmark d-flex justify-content-center align-items-center"
                            style="left:auto; right: 0; height: 100%; width: 50px; border-left: 2px solid rgb(89,198,198) !important">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="checked {{optional($directBillingData)->show_contact_holder_name_on_post ? '' : 'd-none'}}"
                                style="width: 25px; height: 25px; color: white" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="unchecked {{optional($directBillingData)->show_contact_holder_name_on_post ? 'd-none' : ''}}"
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
    {{-- </div> --}}
{{--
    <div wire:loading.delay>
        <img style="width: 40px; height: 40px" src="{{asset('assets/img/spinner.svg')}}" alt="spinner" >
    </div> --}}
</div>
