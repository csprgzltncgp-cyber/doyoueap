<div>
    <style>
        .yellow-input-notification{
            background: #ffc107;
            height: 48px;
            margin-bottom: 12px;
            text-align: center;
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .inactive {
            opacity: 0.5;
        }
    </style>
<script
src="https://code.jquery.com/jquery-3.4.1.min.js"
integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
crossorigin="anonymous"></script>
<script src="{{asset('assets/js/datetime.js')}}"></script>
    <script>
         function showSuccessAlert(){
            Swal.fire(
                '{{__('common.case_input_edit.successful_save')}}',
                '',
                'success'
            );
        }     
        
        function disable_inputs (disable) {
            if (disable) {
                $(".inactive input").prop('disabled', true);
            } else {
                $(".inactive input").removeProp('disabled');
            }
            
        }

        disable_inputs ({{ (optional($directInvoiceData)->invoicing_inactive) ?: true }});
        
        document.addEventListener('livewire:load', function () {
            window.livewire.on('disable_inputs', (disable) => {
                disable_inputs(disable)
            });

            @if($directInvoiceData)
                window.livewire.on('invoicing_inactive_date_missing_{{optional($directInvoiceData)->id}}', () => {
                    Swal.fire({
                        title: '{{__('company-edit.until_date_missing')}}',
                        text: '',
                        icon: 'error',
                    });
                });
            @endif

            window.livewire.on('invoicing_inactive_saved', () => {
                Swal.fire({
                    title: '{{__('common.case_input_edit.successful_save')}}',
                    text: '',
                    icon: 'success',
                });
            });
            
        });
    </script>

    {{-- <div wire:loading.delay.remove> --}}
        {{-- DIRECT INVOICE DATA --}}
        <div>
            <div class="form-row">
                <div class="form-group col-md-3 mb-0">
                    <div class="input-group col-12 p-0 mb-0">
                        <label class="checkbox-container mt-0 w-100"
                            style="color: rgb(89,198,198); padding: 10px 0 10px 10px; border: 2px solid rgb(89,198,198) !important; font-size: 16px; margin-top: 8px;">
                            {{__('company-edit.inactive')}}
                            <input type="checkbox" class="delete_later d-none" wire:model='directInvoiceData.invoicing_inactive'>
                            <span class="checkmark d-flex justify-content-center align-items-center"
                                style="left:auto; right: 0; height: 100%; width: 50px; border-left: 2px solid rgb(89,198,198) !important;">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="checked {{optional($directInvoiceData)->invoicing_inactive ? '' : 'd-none'}}"
                                    style="width: 25px; height: 25px; color: white" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="unchecked {{optional($directInvoiceData)->invoicing_inactive ? 'd-none' : ''}}"
                                    style="width: 20px; height: 20px;" fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </span>
                        </label>
                    </div>
                </div>
                <div class="form-group col-md-4 mb-0 mr-0 pr-0">
                    <div class="input-group col-12 p-0 @if(!optional($directInvoiceData)->invoicing_inactive) inactive @endif">
                        <div class="input-group-prepend">
                            <div class="input-group-text"
                            @if($inactivity_date_required) 
                                style="
                                    border-left: 2px solid red!important; 
                                    border-bottom: 2px solid red!important; 
                                    border-top: 2px solid red!important" 
                            @endif >
                                {{__('company-edit.until_this_date')}}:
                            </div>
                        </div>
                        <input 
                            type="text" 
                            id="invoicing_inactive_to_{{optional($directInvoiceData)->id}}"
                            wire:model="directInvoiceData.invoicing_inactive_to"
                            readonly
                            @if(!optional($directInvoiceData)->invoicing_inactive) disabled @endif
                            @if($inactivity_date_required) 
                                style="
                                    border-right: 2px solid red!important; 
                                    border-bottom: 2px solid red!important; 
                                    border-top: 2px solid red!important" 
                            @endif
                            >
                    </div>
                </div>
                <div>
                    <button class="btn-radius @if(!optional($directInvoiceData)->invoicing_inactive) inactive @endif" style="height:48px!important; margin-left:10px" type="button" wire:click="save_invoicing_inactive"
                        @if(!optional($directInvoiceData)->invoicing_inactive) disabled @endif>
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="width:20px; height:20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                        </svg>
                        {{__('common.save')}}
                    </button>
                </div>
            </div>
            
            <div class="form-row @if(optional($directInvoiceData)->invoicing_inactive) inactive @endif" disabled>
                <div class="form-group col-md-7 mb-0">
                    <div class="input-group col-12 p-0">
                        <div class="input-group-prepend">
                            <div class="input-group-text"
                                @if(in_array('directInvoiceData.name', $customErrors))style="border-color: red !important;" @endif
                            >
                                {{__('company-edit.invoicing_name')}}:
                            </div>
                        </div>
                        <input type="text" wire:model="directInvoiceData.name" required
                            @if(in_array('directInvoiceData.name', $customErrors))style="border-color: red !important;" @endif
                        >
                    </div>
                </div>
            </div>

            <div class="form-row @if(optional($directInvoiceData)->invoicing_inactive) inactive @endif">
                <div class="form-group col-md-2 mb-0">
                    <input type="text" disabled placeholder="{{__('company-edit.invoicing_address')}}">
                </div>

                <div class="form-group col-md-5 mb-0">
                    <div class="input-group col-12 p-0">
                        <div class="input-group-prepend">
                            <div class="input-group-text"
                                @if(in_array('directInvoiceData.country', $customErrors))style="border-color: red !important;" @endif
                            >
                                {{__('common.country')}}:
                            </div>
                        </div>
                        <input
                            class="invalid"
                            type="text"
                            wire:model="directInvoiceData.country"
                            required
                            @if(in_array('directInvoiceData.country', $customErrors))style="border-color: red !important;" @endif
                        >
                    </div>
                </div>
            </div>

            <div class="form-row @if(optional($directInvoiceData)->invoicing_inactive) inactive @endif">
                <div class="form-group col-md-3 mb-0">
                    <div class="input-group col-12 p-0">
                        <div class="input-group-prepend">
                            <div class="input-group-text"
                                @if(in_array('directInvoiceData.postal_code', $customErrors))style="border-color: red !important;" @endif
                            >
                                {{__('company-edit.postal_code')}}:
                            </div>
                        </div>
                        <input type="text" wire:model="directInvoiceData.postal_code" required
                            @if(in_array('directInvoiceData.postal_code', $customErrors))style="border-color: red !important;" @endif
                        >
                    </div>
                </div>

                <div class="form-group col-md-4 mb-0">
                    <div class="input-group col-12 p-0">
                        <div class="input-group-prepend">
                            <div class="input-group-text"
                                @if(in_array('directInvoiceData.city', $customErrors))style="border-color: red !important;" @endif
                            >
                                {{__('crisis.city')}}:
                            </div>
                        </div>
                        <input type="text" wire:model="directInvoiceData.city" required
                            @if(in_array('directInvoiceData.city', $customErrors))style="border-color: red !important;" @endif
                        >
                    </div>
                </div>
            </div>

            <div class="form-row @if(optional($directInvoiceData)->invoicing_inactive) inactive @endif">
                <div class="form-group col-md-4 mb-0">
                    <div class="input-group col-12 p-0">
                        <div class="input-group-prepend">
                            <div class="input-group-text"
                                @if(in_array('directInvoiceData.street', $customErrors))style="border-color: red !important;" @endif
                            >
                                {{__('expert-data.street')}}:
                            </div>
                        </div>
                        <input type="text" wire:model="directInvoiceData.street" required
                            @if(in_array('directInvoiceData.street', $customErrors))style="border-color: red !important;" @endif
                        >
                    </div>
                </div>

                <div class="form-group col-md-3 mb-0">
                    <div class="input-group col-12 p-0">
                        <div class="input-group-prepend">
                            <div class="input-group-text"
                                @if(in_array('directInvoiceData.house_number', $customErrors))style="border-color: red !important;" @endif
                            >
                                {{__('expert-data.house_number')}}:
                            </div>
                        </div>
                        <input type="text" wire:model="directInvoiceData.house_number" required
                            @if(in_array('directInvoiceData.house_number', $customErrors))style="border-color: red !important;" @endif
                        >
                    </div>
                </div>
            </div>

            <div class="form-row @if(optional($directInvoiceData)->invoicing_inactive) inactive @endif">
                @if(optional($directInvoiceData)->is_po_number_changing)
                    <div class="form-group col-md-4 mb-0">
                        <div class="yellow-input-notification">
                            {{__('company-edit.po_number_changing')}}
                        </div>
                    </div>
                @else
                    <div class="form-group col-md-4 mb-0">
                        <div class="input-group col-12 p-0">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    {{__('company-edit.po_number')}}:
                                </div>
                            </div>
                            <input type="text" wire:model="directInvoiceData.po_number">
                        </div>
                    </div>
                @endif

                <div class="form-group col-md-3 mb-0">
                    <div class="input-group col-12 p-0 mb-0">
                        <label class="checkbox-container mt-0 w-100"
                            style="color: rgb(89,198,198); padding: 10px 0 10px 10px; border: 2px solid rgb(89,198,198) !important; font-size: 16px; margin-top: 8px;">
                            {{__('company-edit.changing')}}
                            <input type="checkbox" class="delete_later d-none" wire:model='directInvoiceData.is_po_number_changing'>
                            <span class="checkmark d-flex justify-content-center align-items-center"
                                style="left:auto; right: 0; height: 100%; width: 50px; border-left: 2px solid rgb(89,198,198) !important;">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="checked {{optional($directInvoiceData)->is_po_number_changing ? '' : 'd-none'}}"
                                    style="width: 25px; height: 25px; color: white" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="unchecked {{optional($directInvoiceData)->is_po_number_changing ? 'd-none' : ''}}"
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

            <div class="form-row @if(optional($directInvoiceData)->invoicing_inactive) inactive @endif">
                <div class="form-group col-md-7 mb-0">
                    <div class="input-group col-12 p-0">
                        <div class="input-group-prepend">
                            <div class="input-group-text"
                                @if(in_array('directInvoiceData.tax_number', $customErrors))style="border-color: red !important;" @endif
                            >
                                {{__('common.tax_number')}}:
                            </div>
                        </div>
                        <input type="text" wire:model="directInvoiceData.tax_number" required
                            @if(in_array('directInvoiceData.tax_number', $customErrors))style="border-color: red !important;" @endif
                        >
                    </div>
                </div>
            </div>

            <div class="form-row @if(optional($directInvoiceData)->invoicing_inactive) inactive @endif">
                <div class="form-group col-md-7 mb-0">
                    <div class="input-group col-12 p-0">
                        <div class="input-group-prepend">
                            <div class="input-group-text"
                                @if(in_array('directInvoiceData.community_tax_number', $customErrors))style="border-color: red !important;" @endif
                            >
                                {{__('common.community_tax_number')}}:
                            </div>
                        </div>
                        <input type="text" wire:model="directInvoiceData.community_tax_number" required
                            @if(in_array('directInvoiceData.community_tax_number', $customErrors))style="border-color: red !important;" @endif
                        >
                    </div>
                </div>
            </div>


            <div class="form-row @if(optional($directInvoiceData)->invoicing_inactive) inactive @endif">
                <div class="form-group col-md-7 mb-0">
                    <div class="input-group col-12 p-0">
                        <div class="input-group-prepend">
                            <div class="input-group-text"
                                @if(in_array('directInvoiceData.group_id', $customErrors))style="border-color: red !important;" @endif
                            >
                                {{__('common.group_id')}}:
                            </div>
                        </div>
                        <input type="text" wire:model="directInvoiceData.group_id"
                            @if(in_array('directInvoiceData.group_id', $customErrors))style="border-color: red !important;" @endif
                        >
                    </div>
                </div>
            </div>

            <div class="form-row @if(optional($directInvoiceData)->invoicing_inactive) inactive @endif">
                <div class="form-group col-md-6 mb-0">
                        <input type="text" disabled placeholder=" {{__('company-edit.payment_deadline')}}"
                            @if(in_array('directInvoiceData.payment_deadline', $customErrors))style="border-color: red !important;" @endif
                        >
                </div>

                <div class="form-group col-md-1 mb-0">
                        <input type="number" wire:model.lazy="directInvoiceData.payment_deadline" placeholder="{{__('task.day')}}" required
                            @if(in_array('directInvoiceData.payment_deadline', $customErrors))style="border-color: red !important;" @endif
                        >
                </div>
            </div>
        </div>
        {{-- DIRECT INVOICE DATA --}}
       @if(!empty($directInvoiceData) && !optional($directInvoiceData)->invoicing_inactive)
            <livewire:admin.direct-invoicing.billing-data
                :company="$company"
                :country="$country"
                :directInvoiceDataId="optional($directInvoiceData)->id"
                :wire:key="'billing-data-' . optional($directInvoiceData)->id"
            />

            <livewire:admin.direct-invoicing.invoice-item.index
                :company="$company"
                :country="$country"
                :directInvoiceDataId="optional($directInvoiceData)->id"
                :wire:key="'company-invoice-items-' . optional($directInvoiceData)->id"
            />


            <livewire:admin.direct-invoicing.invoice-note.index
                :company="$company"
                :country="$country"
                :directInvoiceDataId="optional($directInvoiceData)->id"
                :wire:key="'invoice-note-' . optional($directInvoiceData)->id"
            />

            <div class="row ml-0">
                <button type="button" style="padding-bottom: 14px; padding-left:0px;" class="text-center btn-radius d-flex" wire:click="$emitTo('admin.direct-invoicing.invoice-item.index','newInvoiceItem', {{optional($directInvoiceData)->id}})">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="width:20px; height:20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    <span style="margin-top: 3px;">
                        {{__('company-edit.add_new_invoice_item')}}
                    </span>
                </button>
                <button type="button" style="padding-bottom: 14px; padding-left:0px; --btn-min-width: " class="text-center btn-radius" wire:click="$emitTo('admin.direct-invoicing.invoice-note.index','newInvoiceNote', {{optional($directInvoiceData)->id}})">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="width:20px; height:20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    <span style="margin-top: 3px;">
                        {{__('company-edit.add_new_invoice_comment')}}
                    </span>
                </button>
            </div>

            <livewire:admin.direct-invoicing.comment.index
                :company="$company"
                :country="$country"
                :directInvoiceDataId="optional($directInvoiceData)->id"
                :wire:key="'invoice-comment-' . optional($directInvoiceData)->id"
            />
        @endif
    {{-- </div> --}}

    {{-- <div wire:loading.delay>
        <img style="width: 40px; height: 40px" src="{{asset('assets/img/spinner.svg')}}" alt="spinner" >
    </div> --}}
    @if($withSaveButton)
        <button onclick="showSuccessAlert()" type="button" class="text-center btn-radius mt-4" style="text-transform: uppercase;">
            <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="width:20px; height:20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
            </svg>
            <span style="margin-top: 3px;">
                {{__('common.save')}}
            </span>
        </button>
    @endif

    <script>
        $(`#invoicing_inactive_to_{{optional($directInvoiceData)->id}}`).datepicker({
            format: 'yyyy-mm-dd',
        }).change(function (event) {
            @this.set('directInvoiceData.invoicing_inactive_to', event.target.value);
        });
    </script>
</div>
