<div>
    @if($opened)
        <div class="lis-element-div" style="display: block">
            @if(is_null($contractHolderCompany))
                <x-invoice-helper.search />
            @endif
            
            @foreach($companies as $company)
                <div class="invoice-list-holder">
                    <div class="case-list-in col-12 group d-flex justify-content-between align-items-center" wire:click='toggleOpenCompany({{$company->id}})'>
                        <div class="d-flex">
                            <div wire:loading.delay>
                                <img style="width: 20px; height: 20px; margin-right:5px;" src="{{asset('assets/img/spinner.svg')}}" alt="spinner" >
                            </div>

                            <div wire:loading.delay.remove>
                                @if($company->direct_invoice_datas->count() > 1)
                                    {{-- YELLOW ALERT --}}
                                    @if(has_missing_information_on_direct_invoices($company->direct_invoices))
                                        <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height:20px; color:#ffc208; margin-right:5px;" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                    @endif
                                    {{-- YELLOW ALERT --}}

                                    {{-- RED ALERT --}}
                                    @if(has_company_missing_information($company))
                                        <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height:20px; color:#f70000; margin-right:5px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                         </svg>
                                    @endif
                                    {{-- RED ALERT --}}

                                    {{-- GREEN ALERT --}}
                                    @if(has_invoice_with_no_missing_company_and_direct_invoice_information($company->direct_invoices))
                                        <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height:20px; color:#91b752; margin-right:5px;" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M3 12v3c0 1.657 3.134 3 7 3s7-1.343 7-3v-3c0 1.657-3.134 3-7 3s-7-1.343-7-3z" />
                                            <path d="M3 7v3c0 1.657 3.134 3 7 3s7-1.343 7-3V7c0 1.657-3.134 3-7 3S3 8.657 3 7z" />
                                            <path d="M17 5c0 1.657-3.134 3-7 3S3 6.657 3 5s3.134-3 7-3 7 1.343 7 3z" />x
                                        </svg>
                                    @endif
                                    {{-- GREEN ALERT --}}

                                    {{-- PURPLE ALERT --}}
                                    @if(has_invoice_with_done_status($company->direct_invoices))
                                        <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height:20px; color:#a33095; margin-right:5px;" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                    @endif
                                    {{-- PURPLE ALERT --}}
                                @else
                                    {{-- RED ALERT --}}
                                    @if(has_company_missing_information($company))
                                        <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height:20px; color:#f70000; margin-right:5px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                    @endif
                                    {{-- RED ALERT --}}

                                    {{-- YELLOW ALERT --}}
                                    @if(has_missing_information_on_direct_invoice($company->direct_invoices->first()))
                                        <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height:20px; color:#ffc208; margin-right:5px;" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                    @endif
                                    {{-- YELLOW ALERT --}}

                                    {{-- GREEN AND PURPLE ALERT --}}
                                    @if(!has_company_missing_information($company) && !has_missing_information_on_direct_invoice($company->direct_invoices->first()))
                                        @if(is_invoice_done($company->direct_invoices->first()))
                                            <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height:20px; color:#a33095; margin-right:5px;" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height:20px; color:#91b752; margin-right:5px;" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M3 12v3c0 1.657 3.134 3 7 3s7-1.343 7-3v-3c0 1.657-3.134 3-7 3s-7-1.343-7-3z" />
                                                <path d="M3 7v3c0 1.657 3.134 3 7 3s7-1.343 7-3V7c0 1.657-3.134 3-7 3S3 8.657 3 7z" />
                                                <path d="M17 5c0 1.657-3.134 3-7 3S3 6.657 3 5s3.134-3 7-3 7 1.343 7 3z" />x
                                            </svg>
                                        @endif
                                    @endif
                                    {{-- GREEN AND PURPLE ALERT --}}
                                @endif

                                {{-- PAID ALERT --}}
                                @foreach($company->direct_invoices as $direct_invoice)
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        style="width: 20px; height:20px; @if(!empty($direct_invoice->paid_at) && !empty($direct_invoice->paid_amount)) color:#f70000; @elseif(!empty($direct_invoice->paid_at)) color:#eb7e30; @else color:#59c6c6; opacity: 0.3; @endif margin-right:4px; margin-top: 3px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                @endforeach
                                {{-- PAID ALERT --}}

                            </div>

                            <span style="margin-top:3px;">{{$company->name}}</span>
                        </div>

                        <button class="caret-left float-right">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                style="width: 20px; height: 20px; @if(in_array($company->id, $opened_companies)) transform: rotateZ(180deg); @endif" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                    </div>
                    <div class="invoice-list @if(!in_array($company->id, $opened_companies)) d-none @endif">
                        @if($company->country_differentiates->invoicing)
                            <div class="country-holder">
                                @foreach($company->countries as $country)
                                    @if(empty($company->direct_invoices->where('country_id', $country->id)->first()))
                                       @continue
                                    @else
                                    
                                    <div class="green-box country-box @if(in_array($company->id, $opened_companies) && in_array($country->id, $opened_countries)) dark @endif" wire:key="{{ $loop->index }}" 
                                        wire:click='@if ($company->direct_invoices->where('country_id', $country->id)->first()->active) toggleOpenCountry({{$country->id}}) @endif'
                                        style="@if (!$company->direct_invoices->where('country_id', $country->id)->first()->active) cursor:default!important; color: rgb(110, 110, 110) @endif">
                                        <div class="d-flex">
                                            <div wire:loading.delay>
                                                <img style="width: 20px; height: 20px; margin-right:5px;" src="{{asset('assets/img/spinner.svg')}}" alt="spinner" >
                                            </div>

                                            <div wire:loading.delay.remove>
                                                @if($company->direct_invoices->where('country_id', $country->id)->count() > 1)
                                                    {{-- YELLOW ALERT --}}
                                                    @if(has_missing_information_on_direct_invoices($company->direct_invoices->where('country_id', $country->id)))
                                                        <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height:20px; color:#ffc208; margin-right:5px;" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                        </svg>
                                                    @endif
                                                    {{-- YELLOW ALERT --}}

                                                    {{-- RED ALERT --}}
                                                    @if(has_company_missing_information($company, $country->id))
                                                        <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height:20px; color:#f70000; margin-right:5px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                        </svg>
                                                    @endif
                                                    {{-- RED ALERT --}}

                                                    {{-- GREEN ALERT --}}
                                                    @if(has_invoice_with_no_missing_company_and_direct_invoice_information($company->direct_invoices->where('country_id', $country->id)))
                                                        <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height:20px; color:#91b752; margin-right:5px;" viewBox="0 0 20 20" fill="currentColor">
                                                            <path d="M3 12v3c0 1.657 3.134 3 7 3s7-1.343 7-3v-3c0 1.657-3.134 3-7 3s-7-1.343-7-3z" />
                                                            <path d="M3 7v3c0 1.657 3.134 3 7 3s7-1.343 7-3V7c0 1.657-3.134 3-7 3S3 8.657 3 7z" />
                                                            <path d="M17 5c0 1.657-3.134 3-7 3S3 6.657 3 5s3.134-3 7-3 7 1.343 7 3z" />x
                                                        </svg>
                                                    @endif
                                                    {{-- GREEN ALERT --}}

                                                    {{-- PURPLE ALERT --}}
                                                    @if(has_invoice_with_done_status($company->direct_invoices->where('country_id', $country->id)))
                                                        <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height:20px; color:#a33095; margin-right:5px;" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                        </svg>
                                                    @endif
                                                    {{-- PURPLE ALERT --}}
                                                @else
                                                    {{-- RED ALERT --}}
                                                    @if(has_company_missing_information($company, $country->id))
                                                        <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height:20px; color:#f70000; margin-right:5px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                        </svg>
                                                    @endif
                                                    {{-- RED ALERT --}}

                                                    {{-- YELLOW ALERT --}}
                                                    @if(has_missing_information_on_direct_invoice($company->direct_invoices->where('country_id', $country->id)->first()))
                                                        <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height:20px; color:#ffc208; margin-right:5px;" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                        </svg>
                                                    @endif
                                                    {{-- YELLOW ALERT --}}

                                                    {{-- GREEN AND PURPLE ALERT --}}
                                                    @if(!has_company_missing_information($company, $country->id) && !has_missing_information_on_direct_invoice($company->direct_invoices->where('country_id', $country->id)->first()))
                                                        @if(is_invoice_done($company->direct_invoices->where('country_id', $country->id)->first()))
                                                            <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height:20px; color:#a33095; margin-right:5px;" viewBox="0 0 20 20" fill="currentColor">
                                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                            </svg>
                                                        @else
                                                            <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height:20px; color:#91b752; margin-right:5px;" viewBox="0 0 20 20" fill="currentColor">
                                                                <path d="M3 12v3c0 1.657 3.134 3 7 3s7-1.343 7-3v-3c0 1.657-3.134 3-7 3s-7-1.343-7-3z" />
                                                                <path d="M3 7v3c0 1.657 3.134 3 7 3s7-1.343 7-3V7c0 1.657-3.134 3-7 3S3 8.657 3 7z" />
                                                                <path d="M17 5c0 1.657-3.134 3-7 3S3 6.657 3 5s3.134-3 7-3 7 1.343 7 3z" />x
                                                            </svg>
                                                        @endif

                                                    @endif
                                                    {{-- GREEN AND PURPLE ALERT --}}
                                                @endif


                                                {{-- PAID ALERT --}}
                                                @foreach($company->direct_invoices->where('country_id', $country->id) as $direct_invoice)
                                                    <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height:20px; @if(!empty($direct_invoice->paid_at) && !empty($direct_invoice->paid_amount)) color:#f70000; @elseif(!empty($direct_invoice->paid_at)) color:#eb7e30; @else color:#59c6c6; opacity: 0.3; @endif margin-right:4px; margin-top: 3px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                @endforeach
                                                {{-- PAID ALERT --}}
                                            </div>

                                            <span style="margin-top: 3px;">{{$country->name}}</span>
                                        </div>
                                    </div>
                                    @endif
                                @endforeach
                            </div>

                            @foreach($company->countries as $country)
                                <div class="@if(!(in_array($company->id, $opened_companies) && in_array($country->id, $opened_countries))) d-none @endif">
                                    @if($company->direct_invoices->where('country_id', $country->id)->count() == 1)
                                        @livewire('admin.invoice-helper.direct-invoicing.card',
                                            ['direct_invoice_id' => $company->direct_invoices->where('country_id', $country->id)->first()->id],
                                            key('invoice-card-' . $company->direct_invoices->where('country_id', $country->id)->first()->id. '-' . $date)
                                        )
                                    @else
                                        @foreach ($company->direct_invoices->where('country_id', $country->id) as $direct_invoice)
                                            <div class="invoice-list-holder">
                                                <div class="case-list-in col-12 group bordered-line d-flex justify-content-between align-items-center" wire:click='toggleOpenDirectInvoice({{$direct_invoice->id}})'>
                                                    <div class="d-flex">
                                                        <div wire:loading.delay>
                                                            <img style="width: 20px; height: 20px; margin-right:5px;" src="{{asset('assets/img/spinner.svg')}}" alt="spinner" >
                                                        </div>

                                                        <div wire:loading.delay.remove>
                                                            {{-- RED ALERT --}}
                                                            @if(has_company_missing_information($company, $country->id))
                                                                <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height:20px; color:#f70000; margin-right:5px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                                </svg>
                                                            @endif
                                                            {{-- RED ALERT --}}

                                                            {{-- YELLOW ALERT --}}
                                                            @if(has_missing_information_on_direct_invoice($direct_invoice, $country->id))
                                                                <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height:20px; color:#ffc208; margin-right:5px;" viewBox="0 0 20 20" fill="currentColor">
                                                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                                </svg>
                                                            @endif
                                                            {{-- YELLOW ALERT --}}

                                                            {{-- GREEN AND PURPLE ALERT --}}
                                                            @if(!has_company_missing_information($company, $country->id) && !has_missing_information_on_direct_invoice($direct_invoice))
                                                                @if(is_invoice_done($direct_invoice))
                                                                    <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height:20px; color:#a33095; margin-right:5px;" viewBox="0 0 20 20" fill="currentColor">
                                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                                    </svg>
                                                                @else
                                                                    <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height:20px; color:#91b752; margin-right:5px;" viewBox="0 0 20 20" fill="currentColor">
                                                                        <path d="M3 12v3c0 1.657 3.134 3 7 3s7-1.343 7-3v-3c0 1.657-3.134 3-7 3s-7-1.343-7-3z" />
                                                                        <path d="M3 7v3c0 1.657 3.134 3 7 3s7-1.343 7-3V7c0 1.657-3.134 3-7 3S3 8.657 3 7z" />
                                                                        <path d="M17 5c0 1.657-3.134 3-7 3S3 6.657 3 5s3.134-3 7-3 7 1.343 7 3z" />x
                                                                    </svg>
                                                                @endif

                                                            @endif
                                                            {{-- GREEN AND PURPLE ALERT --}}


                                                            {{-- PAID ALERT --}}
                                                                <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height:20px; @if(!empty($direct_invoice->paid_at) && !empty($direct_invoice->paid_amount)) color:#f70000; @elseif(!empty($direct_invoice->paid_at)) color:#eb7e30; @else color:#59c6c6; opacity: 0.3; @endif margin-right:4px; margin-top: 3px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                </svg>
                                                            {{-- PAID ALERT --}}
                                                        </div>
                                                        <span style="margin-top: 3px;">
                                                            {{
                                                                array_key_exists('admin_identifier', $direct_invoice->data['invoice_data']) ?
                                                                    $direct_invoice->data['invoice_data']['admin_identifier']
                                                                :
                                                                    $direct_invoice->data['invoice_data']['name']
                                                            }}
                                                        </span>
                                                    </div>

                                                    <button class="caret-left float-right">
                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                            style="width: 20px; height: 20px; @if(in_array($direct_invoice->id, $opened_direct_invoices)) transform: rotateZ(180deg); @endif" fill="none" viewBox="0 0 24 24"
                                                            stroke="currentColor" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                                        </svg>
                                                    </button>
                                                </div>

                                                <div class="invoice-list @if(!(in_array($company->id, $opened_companies) && in_array($country->id, $opened_countries) && in_array($direct_invoice->id, $opened_direct_invoices))) d-none @endif">
                                                    @livewire('admin.invoice-helper.direct-invoicing.card',
                                                        ['direct_invoice_id' => $direct_invoice->id],
                                                        key('invoice-card-' . $direct_invoice . '-' . $date)
                                                    )
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            @endforeach
                        @else
                            @if($company->direct_invoices->count() == 1)
                                @livewire('admin.invoice-helper.direct-invoicing.card',
                                    ['direct_invoice_id' => $company->direct_invoices->first()->id],
                                    key('invoice-card-' . $company->direct_invoices->first()->id. '-' . $date)
                                )
                            @else
                                @foreach ($company->direct_invoices as $direct_invoice)
                                    <div class="invoice-list-holder">
                                        <div wire:key="{{ $direct_invoice->id }}" class="case-list-in col-12 group bordered-line d-flex justify-content-between align-items-center" 
                                        wire:click='@if ($direct_invoice->active) toggleOpenDirectInvoice({{$direct_invoice->id}}) @endif'
                                        style="@if (!$direct_invoice->active) cursor:default!important; color: rgb(110, 110, 110) @endif">
                                            <div class="d-flex">
                                                <div wire:loading.delay>
                                                    <img style="width: 20px; height: 20px; margin-right:5px;" src="{{asset('assets/img/spinner.svg')}}" alt="spinner" >
                                                </div>

                                                @if ($direct_invoice->active)
                                                    <div wire:loading.delay.remove>
                                                        {{-- RED ALERT --}}
                                                        @if(has_company_missing_information($company))
                                                            <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height:20px; color:#f70000; margin-right:5px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                            </svg>
                                                        @endif
                                                        {{-- RED ALERT --}}
                                                            
                                                        {{-- YELLOW ALERT --}}
                                                        @if(has_missing_information_on_direct_invoice($direct_invoice))
                                                            <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height:20px; color:#ffc208; margin-right:5px;" viewBox="0 0 20 20" fill="currentColor">
                                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                            </svg>
                                                        @endif
                                                        {{-- YELLOW ALERT --}}

                                                        {{-- GREEN AND PURPLE ALERT --}}
                                                        @if(!has_company_missing_information($company) && !has_missing_information_on_direct_invoice($direct_invoice))
                                                            @if(is_invoice_done($direct_invoice))
                                                                <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height:20px; color:#a33095; margin-right:5px;" viewBox="0 0 20 20" fill="currentColor">
                                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                                </svg>
                                                            @else
                                                                <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height:20px; color:#91b752; margin-right:5px;" viewBox="0 0 20 20" fill="currentColor">
                                                                    <path d="M3 12v3c0 1.657 3.134 3 7 3s7-1.343 7-3v-3c0 1.657-3.134 3-7 3s-7-1.343-7-3z" />
                                                                    <path d="M3 7v3c0 1.657 3.134 3 7 3s7-1.343 7-3V7c0 1.657-3.134 3-7 3S3 8.657 3 7z" />
                                                                    <path d="M17 5c0 1.657-3.134 3-7 3S3 6.657 3 5s3.134-3 7-3 7 1.343 7 3z" />x
                                                                </svg>
                                                            @endif

                                                        @endif
                                                        {{-- GREEN AND PURPLE ALERT --}}

                                                        {{-- PAID ALERT --}}
                                                            <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height:20px; @if(!empty($direct_invoice->paid_at) && !empty($direct_invoice->paid_amount)) color:#f70000; @elseif(!empty($direct_invoice->paid_at)) color:#eb7e30; @else color:#59c6c6; opacity: 0.3; @endif margin-right:4px; margin-top: 3px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                            </svg>
                                                        {{-- PAID ALERT --}}
                                                    </div>
                                                @endif

                                                <span style="margin-top: 3px;">
                                                    {{
                                                        array_key_exists('admin_identifier', $direct_invoice->data['invoice_data'])?
                                                            $direct_invoice->data['invoice_data']['admin_identifier'] :
                                                            $direct_invoice->data['invoice_data']['name']
                                                    }}
                                                </span>
                                                
                                                @if (!$direct_invoice->active)
                                                    <span style="margin-top: 3px;" class="ml-1 text-uppercase">
                                                        - {{__('company-edit.inactive')}}
                                                    </span>
                                                @endif
                                            </div>
                                            
                                            @if ($direct_invoice->active)
                                                <button class="caret-left float-right">
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        style="width: 20px; height: 20px; @if(in_array($direct_invoice->id, $opened_direct_invoices)) transform: rotateZ(180deg); @endif" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>
                                        
                                        <div class="invoice-list @if(!(in_array($company->id, $opened_companies) && in_array($direct_invoice->id, $opened_direct_invoices))) d-none @endif">
                                            @livewire('admin.invoice-helper.direct-invoicing.card',
                                                ['direct_invoice_id' => $direct_invoice->id],
                                                key('invoice-card-' . $direct_invoice . '-' . $date)
                                            )
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        @endif
                    </div>
                </div>
            @endforeach

            @if($companies->hasMorePages())
                <div class="load-more-container" style="gap: 0px">
                    <div class="green-box load-more-cases btn-radius d-flex" wire:click="loadMore">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="width: 20px; height: 20px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                        </svg>
                        {{__('invoice-helper.load-more')}}
                    </div>

                    <div class="green-box load-more-cases btn-radius d-flex" wire:click="loadAll">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="width: 20px; height: 20px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 13l-7 7-7-7m14-8l-7 7-7-7"></path>
                        </svg>
                        {{__('invoice-helper.load-all')}}
                    </div>
                </div>
            @endif
        </div>
    @endif
</div>
