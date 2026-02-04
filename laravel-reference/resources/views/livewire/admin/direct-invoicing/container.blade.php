@push('livewire_js')
<script>
    function deleteDirectInvoiceData(id){
        Swal.fire({
            title: '{{__('common.are-you-sure-to-delete')}}',
            text: '{{__('common.operation-cannot-undone')}}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: '{{__('common.yes-delete-it')}}',
        }).then((result) => {
            if (result.value) {
                Livewire.emit('deleteDirectInvoiceData', id);
            }
        });
    }

    async function editDirectInvoiceAdminIdentifier(currentAdminIdentifier, id){
        const { value: newAdminIdentifier } = await Swal.fire({
            title: 'Új azonosító megadása',
            input: 'text',
            inputValue: currentAdminIdentifier,
            showCancelButton: true,
        });

        if(newAdminIdentifier){
            @this.editDirectInvoiceAdminIdentifier(id, newAdminIdentifier);
        }
    }
</script>
@endpush

<div>
    <link rel="stylesheet" href="{{asset('assets/css/list.css')}}?v={{time()}}">
    <style>
        .list-element button, .list-element a {
            margin-right: 10px;
            display: inline-block;
        }

        .list-element button.delete-button {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background: transparent;
            border: 0px solid black;
            color: #007bff;
            outline: none;
        }

        .list-element {
            cursor: pointer;
        }
    </style>
    {{-- <div wire:loading.delay.remove> --}}
        <div class="col-12 row" {{!$countryDifferentiates->invoicing ? 'style=display:none;' : ''}}>
            @foreach($company->countries as $country)
                <div class="case-list-in col-3 group mr-3"
                    wire:click="updateCurrentDirectInvoicingCountry({{$country->id}})"
                    @if($currentDirectInvoicingCountry == $country->id) style="background: rgb(0,87,95); color: white;" @endif
                >
                    <label style="margin: 0">{{$country->name}}</label>
                </div>
            @endforeach
        </div>

        @if($directInvoiceDatas->isNotEmpty() && $directInvoiceDatas->count() >= 2)
            @foreach ($directInvoiceDatas->sortBy('id') as $directInvoiceData)
                <div class="list-element col-12">
                    <span style="cursor: pointer; margin-bottom: 0px;" onclick="editDirectInvoiceAdminIdentifier('{{ $directInvoiceData->admin_identifier}}', {{$directInvoiceData->id}})">
                        {{$directInvoiceData->admin_identifier}}
                        <svg xmlns="http://www.w3.org/2000/svg" class="ml-1 mb-1" style="height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                    </span>
                    <a class="float-right" onclick="deleteDirectInvoiceData({{$directInvoiceData->id}})" style="color: #007bff; cursor: pointer;">
                        <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                                style="height:20px; margin-bottom: 3px" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        {{__('common.delete')}}
                    </a>

                    <a class="float-right" wire:click="openDirectInvoiceData({{$directInvoiceData->id}})" style="color: #007bff; cursor: pointer;">
                        <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                            style="height:20px; margin-bottom: 3px" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        {{__('common.edit')}}
                    </a>

                    @php
                        $label = [];

                        if($directInvoiceData->invoice_items()->where('input', App\Models\InvoiceItem::INPUT_TYPE_WORKSHOP)->exists()){
                            $label[] = 'WS';
                        }

                        if($directInvoiceData->invoice_items()->where('input', App\Models\InvoiceItem::INPUT_TYPE_CRISIS)->exists()){
                            $label[] = 'CI';
                        }

                        if($directInvoiceData->invoice_items()->where('input', App\Models\InvoiceItem::INPUT_TYPE_OTHER_ACTIVITY)->exists()){
                            $label[] = 'O';
                        }
                    @endphp
                    <span class="float-right mr-3" style="color:rgb(0,87,95);">
                        @if(count($label))
                            {{implode('/', $label)}}
                        @endif
                    </span>
                </div>

                <div class="mt-4 @if(!in_array($directInvoiceData->id, $openedDirectInvoiceDatas)) mb-4 d-none @endif">
                    @livewire(
                        'admin.direct-invoicing.invoice-data',
                        [
                            'company' => $company,
                            'country' => $currentDirectInvoicingCountry,
                            'modelId' => $directInvoiceData->id,
                            'withSaveButton' => $includeSaveButtonOnInvoiceData,
                        ],
                        key('direct-invoice-data-' . $directInvoiceData->id)
                    )
                </div>
            @endforeach
        @else
            @livewire(
                'admin.direct-invoicing.invoice-data',
                [
                    'company' => $company,
                    'country' => $currentDirectInvoicingCountry,
                    'withSaveButton' => $includeSaveButtonOnInvoiceData,
                ],
                key('direct-invoice-data-default')
            )
        @endif
    {{-- </div> --}}
{{--
    <div wire:loading.delay>
            <img style="width: 40px; height: 40px;" src="{{asset('assets/img/spinner.svg')}}" alt="spinner" >
    </div> --}}

    @if(in_array($company->id, array_column(App\Enums\ContractHolderCompany::cases(), 'value')))
        <button wire:click="createListFromDirectInvoiceDatas" style="text-transform: uppercase;" type="button" name="button" class="text-center btn-radius mt-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="height: 20px; width:20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
            <span class="mt-1">
                {{__('company-edit.create_list')}}
            </span>
        </button>
    @endif
</div>
