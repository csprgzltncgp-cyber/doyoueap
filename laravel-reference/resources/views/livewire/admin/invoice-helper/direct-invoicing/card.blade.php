<div>
    <div class="mb-4">
        <form style="max-width: 1110px !important;">
            <h1 style="font-size: 18px; color:black" class="mt-0 pt-0">{{__('invoice-helper.invoice-data')}}:</h1>

            @if(!empty($direct_invoice_data['invoice_data']['po_number']) || $direct_invoice_data['invoice_data']['is_po_number_changing'])
                <div class="form-row">
                    <div class="form-group col-md-3 mb-0">
                        <div class="input-group col-12 p-0 mb-0">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    {{__('invoice-helper.po-number')}}:
                                </div>
                            </div>
                            <input type="text" disabled readonly>
                        </div>
                    </div>

                    <div class="form-group col-md-3 mb-0">
                        <div class="input-group col-12 p-0 @if(empty($direct_invoice_data['invoice_data']['po_number'])) yellow @endif">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg" style="height:20px; width:20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </div>
                            </div>
                            <input
                                type="text"
                                wire:model="direct_invoice_data.invoice_data.po_number"
                            />
                        </div>
                    </div>
                </div>
            @endif

            <div class="form-row">
                <div class="form-group col-md-3 mb-0">
                    <div class="input-group col-12 p-0 mb-0">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                {{__('invoice-helper.invoice-date')}}:
                            </div>
                        </div>
                        <input type="text" disabled readonly>
                    </div>
                </div>

                <div class="form-group col-md-3 mb-0">
                    <div class="input-group col-12 p-0">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg" style="height:20px; width:20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                            </div>
                        </div>
                        <input type="text" wire:model="date_of_invoice" disabled readonly/>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-3 mb-0">
                    <div class="input-group col-12 p-0 mb-0">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                {{__('invoice-helper.completion-date')}}:
                            </div>
                        </div>
                        <input type="text" disabled readonly>
                    </div>
                </div>

                <div class="form-group col-md-3 mb-0">
                    <div class="input-group col-12 p-0">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg" style="height:20px; width:20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                            </div>
                        </div>
                        <input type="text" wire:model="date_of_completion" disabled readonly/>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-3 mb-0">
                    <div class="input-group col-12 p-0 mb-0">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                {{__('invoice-helper.payment-date')}}:
                            </div>
                        </div>
                        <input type="text" disabled readonly>
                    </div>
                </div>

                <div class="form-group col-md-3 mb-0">
                    <div class="input-group col-12 p-0">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg" style="height:20px; width:20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                            </div>
                        </div>
                        <input type="text" wire:model='date_of_payment' disabled readonly/>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-3 mb-0">
                    <div class="input-group col-12 p-0 mb-0">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                {{__('invoice-helper.payment-method')}}:
                            </div>
                        </div>
                        <input type="text" disabled readonly>
                    </div>
                </div>

                <div class="form-group col-md-3 mb-0">
                    <div class="input-group col-12 p-0">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg" style="height:20px; width:20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                            </div>
                        </div>
                        <select wire:model="paymentMethod">
                            <option value="transfer" >{{__('invoice-helper.bank-transfer')}}</option>
                            <option value="cash" >{{__('invoice-helper.cash')}}</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- direct_invoice_data.billing_data.custom_email_subject --}}

            @if(!empty($direct_invoice_data['billing_data']['custom_email_subject']))
                <div class="form-row mb-3">
                    <div class="form-group col-md-3 mb-0">
                        <div class="input-group col-12 p-0 mb-0">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    {{__('company-edit.custom_email_subject')}}:
                                </div>
                            </div>
                            <input type="text" disabled readonly>
                        </div>
                    </div>

                    <div class="form-group col-md-3 mb-0">
                        <div class="input-group col-12 p-0">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg" style="height:20px; width:20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </div>
                            </div>
                            <input type="text" wire:model='direct_invoice_data.billing_data.custom_email_subject'/>
                        </div>
                    </div>
                </div>
            @endif


            <h1 style="font-size: 18px; color:black">{{__('invoice-helper.invoice-items')}}:</h1>
            @foreach($direct_invoice_data['invoice_items'] as $key => $invoice_item)
                @if(intval($invoice_item['input']) === App\Models\InvoiceItem::INPUT_TYPE_WORKSHOP && collect($direct_invoice_data['workshop_datas'])->sum('price') <= 0)
                    @continue
                @endif

                @if(intval($invoice_item['input']) === App\Models\InvoiceItem::INPUT_TYPE_CRISIS && collect($direct_invoice_data['crisis_datas'])->sum('price') <= 0)
                    @continue
                @endif

                @if(intval($invoice_item['input']) === App\Models\InvoiceItem::INPUT_TYPE_OTHER_ACTIVITY && collect($direct_invoice_data['other_activity_datas'])->sum('price') <= 0)
                    @continue
                @endif


                <div class="form-row mb-3">
                    <div class="form-group col-md-3 mb-0">
                        <div class="green-box dark col-12">
                            {{$invoice_item['name']}}:
                        </div>
                    </div>
                    @if(array_key_exists('volume', $invoice_item) && !empty($invoice_item['volume']))
                        <div class="form-group col-md-3 mb-0">
                            <div class="input-group col-12 p-0 mb-0 @if(empty($invoice_item['volume']['value'])) yellow @endif">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        {{$invoice_item['volume']['name']}}:
                                    </div>
                                </div>
                                <input type="text" wire:model="direct_invoice_data.invoice_items.{{$key}}.volume.value">
                            </div>
                        </div>
                    @endif

                    @if(array_key_exists('amount', $invoice_item) && !empty($invoice_item['amount']))
                        <div class="form-group col-md-3 mb-0">
                            <div class="input-group col-12 p-0 mb-0 @if(empty($invoice_item['amount']['value'])) yellow @endif">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        {{strtoupper($direct_invoice_data['billing_data']['currency'])}}:
                                    </div>
                                </div>
                                <input type="text" placeholder="{{$invoice_item['amount']['name']}}" wire:model="direct_invoice_data.invoice_items.{{$key}}.amount.value">
                            </div>
                        </div>
                    @endif

                    @if(intval($invoice_item['input']) === App\Models\InvoiceItem::INPUT_TYPE_WORKSHOP)
                        <div class="form-group col-md-3 mb-0">
                            <div class="input-group col-12 p-0 mb-0">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        {{strtoupper($direct_invoice_data['billing_data']['currency'])}}:
                                    </div>
                                </div>
                                <input type="text" value="{{collect($direct_invoice_data['workshop_datas'])->sum('price')}}" disabled readonly>
                            </div>
                        </div>
                    @endif

                    @if(intval($invoice_item['input']) === App\Models\InvoiceItem::INPUT_TYPE_CRISIS)
                        <div class="form-group col-md-3 mb-0">
                            <div class="input-group col-12 p-0 mb-0">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        {{strtoupper($direct_invoice_data['billing_data']['currency'])}}:
                                    </div>
                                </div>
                                <input type="text" value="{{collect($direct_invoice_data['crisis_datas'])->sum('price')}}" disabled readonly>
                            </div>
                        </div>
                    @endif

                    @if(intval($invoice_item['input']) === App\Models\InvoiceItem::INPUT_TYPE_OTHER_ACTIVITY)
                        <div class="form-group col-md-3 mb-0">
                            <div class="input-group col-12 p-0 mb-0">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        {{strtoupper($direct_invoice_data['billing_data']['currency'])}}:
                                    </div>
                                </div>
                                <input type="text" value="{{collect($direct_invoice_data['other_activity_datas'])->sum('price')}}" disabled readonly>
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach

            <div class="form-row">
                <div class="form-group col-md-3 mb-0">
                    <div class="green-box dark col-12">
                        {{__('invoice-helper.net-total')}}:
                    </div>
                </div>

                <div class="form-group col-md-3 mb-0">
                    <div class="input-group col-12 p-0">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                {{strtoupper($direct_invoice_data['billing_data']['currency'])}}:
                            </div>
                        </div>
                        <input type="text" value={{$net_total}}  disabled readonly/>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-3 mb-0">
                    <div class="green-box dark col-12">
                        {{__('invoice-helper.total')}}:
                    </div>
                </div>

                <div class="form-group col-md-3 mb-0">
                    <div class="input-group col-12 p-0">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                ÁFA:
                            </div>
                        </div>
                        <input type="text" value="{{$tax}}" disabled readonly/>
                    </div>
                </div>

                <div class="form-group col-md-3 mb-0">
                    <div class="input-group col-12 p-0">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                {{strtoupper($direct_invoice_data['billing_data']['currency'])}}:
                            </div>
                        </div>
                        <input type="text" value={{$total}}  disabled readonly/>
                    </div>
                </div>
            </div>

            <h1 style="font-size: 18px; color:black">{{__('invoice-helper.comments')}}:</h1>

            <div class="comments mb-3">
                <ul>
                    @if(!empty($direct_invoice_data['invoice_comments']))
                        @foreach($direct_invoice_data['invoice_comments'] as $comment)
                            <li>{{$comment['value']}}</li>
                        @endforeach
                    @else
                        <li>{{__('invoice-helper.no-comment')}}</li>
                    @endif
                </ul>
            </div>

            <h1 style="font-size: 18px; color:black">{{__('invoice-helper.create-invoice')}}:</h1>

            {{-- GENERATE INVOICE BUTTON --}}
            <div class="form-group col-md-3 mb-2 px-0">
                <div  class="green-box btn-radius @if(!empty($direct_invoice->invoice_number)) purple @endif button-c" wire:click="generate_invoice" @if(has_missing_information_on_direct_invoice($direct_invoice) || has_company_missing_information($direct_invoice->company, $direct_invoice->country_id)) style="opacity: 0.3; pointer-events: none;" @endif>
                    <svg wire:loading.delay.remove xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                    </svg>

                    <span wire:loading.delay.remove >{{__('invoice-helper.generate-invoice')}}</span>

                    <div wire:loading.delay>
                        <img style="width: 20px; height: 20px" src="{{asset('assets/img/spinner.svg')}}" alt="spinner" >
                    </div>
                </div>
            </div>
            {{-- GENERATE INVOICE BUTTON --}}

            {{-- SHOW INVOICE BUTTON --}}
            <div class="form-group col-md-3 mb-2 px-0">
                <div class="green-box btn-radius button-c" wire:click="show_invoice" @if(has_missing_information_on_direct_invoice($direct_invoice) || has_company_missing_information($direct_invoice->company, $direct_invoice->country_id)) style="opacity: 0.3; pointer-events: none;" @endif>
                    <svg wire:loading.delay.remove xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>

                    <span wire:loading.delay.remove>{{__('invoice-helper.show-invoice')}}</span>

                    <div wire:loading.delay>
                        <img style="width: 20px; height: 20px" src="{{asset('assets/img/spinner.svg')}}" alt="spinner" >
                    </div>
                </div>
            </div>
            {{-- SHOW INVOICE BUTTON --}}

             {{-- DOWNLOAD INVOICE BUTTON --}}
             <div class="form-group col-md-3 mb-2 px-0">
                <div class="green-box btn-radius button-c" wire:click="download_invoice" @if(has_missing_information_on_direct_invoice($direct_invoice) || has_company_missing_information($direct_invoice->company, $direct_invoice->country_id)) style="opacity: 0.3; pointer-events: none;" @endif>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                      </svg>
                    <span wire:loading.delay.remove>{{__('invoice-helper.download-invoice')}}</span>

                    <div wire:loading.delay>
                        <img style="width: 20px; height: 20px" src="{{asset('assets/img/spinner.svg')}}" alt="spinner" >
                    </div>
                </div>
            </div>
            {{-- DOWNLOAD INVOICE BUTTON --}}

            {{-- CANCEL INVOICE BUTTON --}}
            <div class="form-group col-md-3 mb-2 px-0">
                <div class="green-box button-c btn-radius" wire:click="cancel_invoice()" @if(has_missing_information_on_direct_invoice($direct_invoice) || has_company_missing_information($direct_invoice->company, $direct_invoice->country_id)) style="opacity: 0.3; pointer-events: none;" @endif>
                    <svg wire:loading.delay.remove xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>

                    <span wire:loading.delay.remove>{{__('invoice-helper.invoice-cancellation')}}</span>

                    <div wire:loading.delay>
                        <img style="width: 20px; height: 20px" src="{{asset('assets/img/spinner.svg')}}" alt="spinner" >
                    </div>
                </div>
            </div>
            {{-- CANCEL INVOICE BUTTON --}}


            <h1 style="font-size: 18px; color:black">{{__('invoice-helper.send-invoice')}}:</h1>

            {{-- SEND INVOICE BY EMAIL BUTTON --}}
            @if($direct_invoice_data['billing_data']['send_invoice_by_email'] && !$direct_invoice_data['billing_data']['send_completion_certificate_by_email'])
                <div class="form-group col-md-3 mb-2 px-0" wire:click="send_email" @if(has_missing_information_on_direct_invoice($direct_invoice) || has_company_missing_information($direct_invoice->company, $direct_invoice->country_id)) style="opacity: 0.3; pointer-events: none;" @endif>
                    <div class="green-box button-c btn-radius @if(!empty($direct_invoice->sent_at)) purple @endif">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <span>{{__('invoice-helper.completion-certificate.send-email')}}</span>
                    </div>
                </div>
            @endif
            {{-- SEND INVOICE BY EMAIL BUTTON --}}

            {{-- SEND COMPLETION CERTIFICATE BY EMAIL BUTTON --}}
            @if($direct_invoice_data['billing_data']['send_completion_certificate_by_email'] && !$direct_invoice_data['billing_data']['send_invoice_by_email'] )
                <div class="form-group col-md-3 mb-2 px-0" wire:click="send_email" @if(has_missing_information_on_direct_invoice($direct_invoice) || has_company_missing_information($direct_invoice->company, $direct_invoice->country_id)) style="opacity: 0.3; pointer-events: none;" @endif>
                    <div class="green-box button-c btn-radius @if(!empty($direct_invoice->completion_certificate->sent_at)) purple @endif">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <span>{{__('invoice-helper.completion-certificate.send-email')}}</span>
                    </div>
                </div>
            @endif
            {{-- SEND COMPLETION CERTIFICATE BY EMAIL BUTTON --}}

            {{-- SEND INVOICE AND COMPLETION CERTIFICATE BY EMAIL BUTTON --}}
            @if($direct_invoice_data['billing_data']['send_invoice_by_email'] && $direct_invoice_data['billing_data']['send_completion_certificate_by_email'])
                <div class="form-group col-md-3 mb-2 px-0" wire:click="send_email" @if(has_missing_information_on_direct_invoice($direct_invoice) || has_company_missing_information($direct_invoice->company, $direct_invoice->country_id)) style="opacity: 0.3; pointer-events: none;" @endif>
                    <div class="green-box button-c btn-radius
                    @if(!empty($direct_invoice->completion_certificate->sent_at) && !empty($direct_invoice->sent_at))
                        purple
                    @endif">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <span>{{__('invoice-helper.completion-certificate.send-email')}}</span>
                    </div>
                </div>
            @endif
            {{-- SEND INVOICE AND COMPLETION CERTIFICATE BY EMAIL BUTTON --}}

            {{-- UPLOAD INVOICE BUTTON --}}
            @if($direct_invoice_data['billing_data']['upload_invoice_online'])
                <div class="form-group col-md-3 mb-2 px-0" @if(has_missing_information_on_direct_invoice($direct_invoice) || has_company_missing_information($direct_invoice->company, $direct_invoice->country_id)) style="opacity: 0.3; pointer-events: none;" @endif>
                    <a class="green-box @if(!empty($direct_invoice->invoice_uploaded_at)) purple @endif button-c btn-radius" wire:click="upload_invoice" href="{{$direct_invoice_data['billing_data']['invoice_online_url']}}" target="_blank">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                        </svg>
                        <span>{{__('invoice-helper.upload-invoice')}}</span>
                    </a>
                </div>
            @endif
            {{-- UPLOAD INVOICE BUTTON --}}

            {{-- UPLOAD COMPLETION CERTIFICATE BUTTON --}}
            @if($direct_invoice_data['billing_data']['upload_completion_certificate_online'])
                <div class="form-group col-md-3 mb-2 px-0" @if(has_missing_information_on_direct_invoice($direct_invoice) || has_company_missing_information($direct_invoice->company)) style="opacity: 0.3; pointer-events: none;" @endif>
                    <a class="green-box @if(!empty($direct_invoice->completion_certificate->uploaded_at)) purple @endif button-c btn-radius" wire:click="upload_completion_certificate" href="{{$direct_invoice_data['billing_data']['completion_certificate_online_url']}}" target="_blank">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                        </svg>
                        <span>{{__('invoice-helper.upload-completion-certificate')}}</span>
                    </a>
                </div>
            @endif
            {{-- UPLOAD COMPLETION CERTIFICATE BUTTON --}}

            {{-- DOWNLOAD INVOICE BUTTON --}}
            @if($direct_invoice_data['billing_data']['send_invoice_by_post'])
                <div class="form-group col-md-3 mb-2 px-0" @if(has_missing_information_on_direct_invoice($direct_invoice) || has_company_missing_information($direct_invoice->company, $direct_invoice->country_id)) style="opacity: 0.3; pointer-events: none;" @endif>
                    <div class="green-box button-c btn-radius @if(!empty($direct_invoice->downloaded_at)) purple @endif" wire:click="download_invoice">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        <span>{{__('invoice-helper.print-invoice')}}</span>
                    </div>
                </div>
            @endif
            {{-- DOWNLOAD INVOICE BUTTON --}}

            @if(is_invoice_done($direct_invoice))
                <h1 style="font-size: 18px; color:black">{{__('invoice-helper.pay-invoice')}}:</h1>
                {{-- INVOICE PAID BUTTON --}}
                <div class="form-group col-md-3 mb-2 px-0">
                    <div
                        type="button"
                        onclick="showInvoicePaidPopup('{{ $this->id }}', '{{optional($direct_invoice->paid_at)->format('Y-m-d')}}', '{{$direct_invoice->paid_amount}}')"
                        class="
                            green-box button-c btn-radius
                            @if(!empty($direct_invoice->paid_at) && !empty($direct_invoice->paid_amount))
                            red
                            @elseif(!empty($direct_invoice->paid_at))
                            orange
                            @endif"
                    >
                        @if(!empty($direct_invoice->paid_at) && !empty($direct_invoice->paid_amount))
                            <span>{{$direct_invoice->paid_at->format('Y-m-d')}} -  {{$direct_invoice->paid_amount}} {{strtoupper($direct_invoice_data['billing_data']['currency'])}}</span>
                        @elseif(!empty($direct_invoice->paid_at))
                            <span>{{$direct_invoice->paid_at->format('Y-m-d')}}</span>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>{{__('invoice-helper.invoice-paid')}}</span>
                        @endif
                    </div>
                </div>
                {{-- INVOICE PAID BUTTON --}}
            @endif
        </form>
    </div>
</div>




  {{-- @if($direct_invoice_data['billing_data']['send_invoice_by_post'])
        <div class="form-group col-md-3 mb-2 px-0" @if(has_missing_information_on_direct_invoice($direct_invoice) || has_company_missing_information($direct_invoice->company)) style="opacity: 0.3; pointer-events: none;" @endif>
            <div class="green-box @if(!empty($direct_invoice->envelope->printed_at)) purple @endif button-c" wire:click="download_envelope({{$direct_invoice->company_id}}, '{{$direct_invoice->to}}')">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                <span>{{__('invoice-helper.print-envelope')}}</span>
            </div>
        </div>
    @endif

    @if($direct_invoice_data['billing_data']['send_completion_certificate_by_post'])
        <div class="form-group col-md-3 mb-2 px-0" @if(has_missing_information_on_direct_invoice($direct_invoice) || has_company_missing_information($direct_invoice->company)) style="opacity: 0.3; pointer-events: none;" @endif>
            <div class="green-box @if(!empty($direct_invoice->completion_certificate->printed_at)) purple @endif button-c" wire:click="download_completion_certificate({{$direct_invoice->company_id}}, '{{$direct_invoice->to}}')">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                <span>{{__('invoice-helper.print-completion-certificate')}}</span>
            </div>
        </div>
    @endif --}}
