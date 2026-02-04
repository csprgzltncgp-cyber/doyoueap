@push('livewire_js')
<script src="{{asset('assets/js/datetime.js')}}"></script>
<script>
    $('#date_of_issue').datepicker({
        format: 'yyyy-mm-dd',
        weekStart: 1,
        startDate: '0d'
    }).on('changeDate', function(e){
        @this.set('invoice.date_of_issue', e.format('yyyy-mm-dd'));
        $('.date_of_issue').hide();
    });

    $('#uploadInvoice').click(function(){
            $('#uploadedInvoice').click();
    });

    Livewire.on('errorEvent', function(error){
        Swal.fire({
            title: error,
            text: '',
            icon: 'error',
            confirmButtonText: 'Ok'
        });
    });
</script>
@endpush

<div>
    <link rel="stylesheet" href="/assets/css/form.css?v={{time()}}">
    <link rel="stylesheet" href="/assets/css/perfix-input.css?v={{time()}}">
    <link rel="stylesheet" href="{{asset('assets/css/bordered-checkbox.css')}}?v={{time()}}">
    <link rel="stylesheet" href="{{asset('assets/css/cases/datetime.css')}}">
    <link rel="stylesheet" href="/assets/css/invoices.css?v={{time()}}">
    <link rel="stylesheet" href="{{asset('assets/css/cases/datetime.css')}}">

    <h1 class="w-100">{{__('common.add-new-invoice')}}</h1>
    <x-invoices.submenu />

    <form wire:submit.prevent='store' style="max-width: 1000px !important;">
        <h1 style="font-size: 20px;" class="w-100">{{__('invoice.invoice_data')}}</h1>

        <div class="input-group col-12 p-0">
            <div class="input-group-prepend">
                <div class="input-group-text">
                    {{__('common.name-of-supplier')}}:
                </div>
            </div>
            <input type="text" wire:model="invoiceDatas.name" required>
        </div>

        <div class="input-group col-12 p-0">
            <div class="input-group-prepend">
                <div class="input-group-text">
                    {{__('common.suppliers-email-address')}}:
                </div>
            </div>
            <input type="text" wire:model="invoiceDatas.email" required>
        </div>

        @if(Auth::user()->isHungarian())
            <div class="input-group col-12 p-0">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        {{__('common.tax_number')}}:
                    </div>
                </div>
                <input type="text" wire:model="invoiceDatas.tax_number" required>
            </div>
        @else
            <div class="input-group col-12 p-0">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        {{__('common.swift-code')}}:
                    </div>
                </div>
                <input type="text" wire:model="invoiceDatas.swift" required>
            </div>
        @endif

        <div class="form-row">
            <div class="form-group col-md-6 mb-0">
                <div class="input-group col-12 p-0">
                    <label class="checkbox-container mt-0 w-100"
                           style="color: rgb(89, 198, 198); padding: 10px 0 10px 10px; border: 2px solid rgb(89,198,198) !important; font-size: 16px; margin-top: 8px;">
                           {{__('common.international_tax_number')}}
                        <input type="checkbox" wire:model="hasInternatioanlTaxNumber"  class="delete_later d-none">
                        <span class="checkmark d-flex justify-content-center align-items-center"
                              style="left:auto; right: 0; height: 100%; width: 50px; border-left: 2px solid rgb(89,198,198) !important">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                 class="checked {{!$hasInternatioanlTaxNumber ? 'd-none' : ''}}"
                                 style="width: 25px; height: 25px; color: white" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                 <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                            <svg xmlns="http://www.w3.org/2000/svg"
                                 class="unchecked {{$hasInternatioanlTaxNumber ? 'd-none' : ''}}"
                                 style="width: 20px; height: 20px;" fill="none"
                                 viewBox="0 0 24 24"
                                 stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </span>
                    </label>
                </div>
            </div>

            <div class="form-group col-md-6 mb-0">
                <div class="input-group col-12 p-0">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            {{__('common.tax_number')}}:
                        </div>
                    </div>
                    <input type="text" wire:model="invoiceDatas.international_tax_number" required {{!$hasInternatioanlTaxNumber ? 'disabled' : ''}}>
                </div>
            </div>
        </div>

        <div class="input-group col-12 p-0">
            <div class="input-group-prepend">
                <div class="input-group-text">
                    @if(Auth::user()->isHungarian())
                        {{__('common.account-number')}}:
                    @else
                        {{__('common.iban-number')}}:
                    @endif
                </div>
            </div>
            <input type="text" wire:model='invoiceDatas.account_number' required>
        </div>

        <div class="input-group col-12 p-0">
            <div class="input-group-prepend">
                <div class="input-group-text">
                    {{__('common.name-of-bank')}}:
                </div>
            </div>
            <input type="text" wire:model="invoiceDatas.bank_name" required>
        </div>

        <div class="input-group col-12 p-0">
            <div class="input-group-prepend">
                <div class="input-group-text">
                    {{__('common.address-of-bank')}}:
                </div>
            </div>
            <input type="text" wire:model="invoiceDatas.bank_address" required>
        </div>

        <div class="input-group col-12 p-0">
            <div class="input-group-prepend">
                <div class="input-group-text">
                    {{__('common.country')}}:
                </div>
            </div>
            <select required name="currency" wire:model='invoiceDatas.destination_country'>
                <option>{{__('common.please-choose')}}</option>
                @foreach ($countries as $country)
                    <option value="{{$country->id}}">{{$country->name}}</option>
                @endforeach
            </select>
        </div>

        <div class="form-row">
            <div class="form-group col-md-9 mb-0">
                <input type="text" placeholder="{{__('invoice.currency')}}" disabled>
            </div>

            <div class="form-group col-md-3 mb-0">
                <select required name="currency" wire:model='invoiceDatas.currency'>
                    <option>{{__('common.please-choose')}}</option>
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
            </div>
        </div>

        <h1 style="font-size: 20px;" class="w-100">{{__('invoice.administrative_data')}}</h1>

        <div class="input-group col-12 p-0">
            <div class="input-group-prepend">
                <div class="input-group-text">
                    {{__('common.invoice-number')}}:
                </div>
            </div>
            <input type="text" wire:model="invoice.number" required>
        </div>

        <div class="input-group col-12 p-0">
            <div class="input-group-prepend">
                <div class="input-group-text">
                    {{__('common.date-of-issue')}}:
                </div>
            </div>
            <input type="text" name="date_of_issue" id="date_of_issue" value={{\Carbon\Carbon::parse($invoice->date_of_issue)->format('Y-m-d')}} required>
        </div>

        <div class="input-group col-12 p-0">
            <div class="input-group-prepend">
                <div class="input-group-text">
                    {{__('common.due-date')}}:
                </div>
            </div>
            <input type="text" wire:model='invoice.payment_deadline' disabled required>
        </div>

        @if ($this->invoiceDatas->invoicing_type !== \App\Enums\InvoicingType::TYPE_CUSTOM)
            @if($this->invoiceDatas->invoicing_type == \App\Enums\InvoicingType::TYPE_FIXED || in_array(auth()->user()->id, config('count-expert-consultations')))
                @if($consultation_count)
                    <div class="col-12 p-4 mb-4" style="border: 2px solid rgb(3, 87, 95)">
                        <h1 style="font-size: 20px;" class="w-100 pt-0 mb-0">{{__('common.month-name_'.\Carbon\Carbon::parse($invoice->date_of_issue)->subMonthNoOverflow()->format('m'))}} {{__('common.in_month_consultation')}}: {{$consultation_count->count}}</h1>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-8 mb-0">
                            <div class="input-group col-12 p-0"  style="border-color: rgb(3, 87, 95) !important;">
                                <div class="w-full col-12" style="padding: 12px 12px; {{$acceptCasesPrice ? 'background: rgb(3, 87, 95);' : 'background: rgb(3, 87, 95);'}} color:white">
                                    @if ($this->invoiceDatas->invoicing_type === \App\Enums\InvoicingType::TYPE_FIXED)
                                        {{__('invoice.fixed_wage')}}: {{$this->invoiceDatas->fixed_wage}} {{strtoupper($invoiceDatas->currency)}}
                                    @else
                                        {{__('invoice.consultations_sum')}} ({{$invoiceCaseDatasPeriods->implode(' , ')}}): {{$prices['casesPrice']}} {{strtoupper($invoiceDatas->currency)}}
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-md-4 mb-0">
                            <div class="input-group col-12 p-0">
                                <label class="checkbox-container mt-0 w-100"
                                    style="color: {{$acceptCasesPrice ? 'rgb(3, 87, 95);' : 'rgb(89,198,198);'}} ; padding: 10px 0 10px 10px; border: 2px solid {{$acceptCasesPrice ? 'rgb(3, 87, 95)' : 'rgb(89,198,198)'}}  !important; font-size: 16px; margin-top: 8px;">
                                    {{__('invoice.agree_with_the_amount')}}
                                    <input type="checkbox" class="delete_later d-none" wire:model='acceptCasesPrice'>
                                    <span class="checkmark d-flex justify-content-center align-items-center"
                                        style="left:auto; right: 0; height: 100%; width: 50px; border-left: 2px solid {{$acceptCasesPrice ? 'rgb(3, 87, 95)' : 'rgb(89,198,198)'}}  !important; {{$acceptCasesPrice ? 'background: rgb(3, 87, 95) !important;' : ''}}">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="checked {{!$acceptCasesPrice ? 'd-none' : ''}}"
                                            style="width: 25px; height: 25px; color: white" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="unchecked {{$acceptCasesPrice ? 'd-none' : ''}}"
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
            @else
                @if($grouppedInvoiceCaseDatas->count())
                    <div class="col-12 p-4 mb-4" style="border: 2px solid rgb(3, 87, 95)">
                        <h1 style="font-size: 20px;" class="w-100 pt-0 mb-4">{{__('invoice.administrative_items')}} </h1>
                        <div class="row container" style="gap: 1.0rem">
                            @foreach($grouppedInvoiceCaseDatas as $permission_id => $invoiceCaseDatas)
                                <h1 style="font-size: 20px;" class="w-100 pt-0 mb-1"> {{ $invoiceCaseDatasPeriodsByPermission->where('permission_id', $permission_id)->first()['permission_name'] }} - ({{$invoiceCaseDatasPeriodsByPermission->where('permission_id', $permission_id)->first()['period']->implode(' , ')}}):  {{$invoiceCaseDatas->sum('consultations_count')}} </h1>
                                @foreach ($invoiceCaseDatas as $invoiceCaseData)
                                    <div class="col-2" style="padding: 0">
                                        <div class="d-flex justify-content-center"
                                            style="border: 2px solid rgb(3, 87, 95); padding: 10px; color: black;">
                                            {{$invoiceCaseData->case_identifier}} / {{$invoiceCaseData->consultations_count}}
                                        </div>
                                    </div>
                                @endforeach
                            @endforeach
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-8 mb-0">
                            <div class="input-group col-12 p-0"  style="border-color: rgb(3, 87, 95) !important;">
                                <div class="w-full col-12" style="padding: 12px 12px; {{$acceptCasesPrice ? 'background: rgb(3, 87, 95);' : 'background: rgb(3, 87, 95);'}} color:white">
                                    @if ($this->invoiceDatas->invoicing_type === \App\Enums\InvoicingType::TYPE_FIXED)
                                        {{__('invoice.fixed_wage')}}: {{$this->invoiceDatas->fixed_wage}} {{strtoupper($invoiceDatas->currency)}}
                                    @else
                                        {{__('invoice.consultations_sum')}} ({{$invoiceCaseDatasPeriods->implode(' , ')}}): {{$prices['casesPrice']}} {{strtoupper($invoiceDatas->currency)}}
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-md-4 mb-0">
                            <div class="input-group col-12 p-0">
                                <label class="checkbox-container mt-0 w-100"
                                    style="color: {{$acceptCasesPrice ? 'rgb(3, 87, 95);' : 'rgb(89,198,198);'}} ; padding: 10px 0 10px 10px; border: 2px solid {{$acceptCasesPrice ? 'rgb(3, 87, 95)' : 'rgb(89,198,198)'}}  !important; font-size: 16px; margin-top: 8px;">
                                    {{__('invoice.agree_with_the_amount')}}
                                    <input type="checkbox" class="delete_later d-none" wire:model='acceptCasesPrice'>
                                    <span class="checkmark d-flex justify-content-center align-items-center"
                                        style="left:auto; right: 0; height: 100%; width: 50px; border-left: 2px solid {{$acceptCasesPrice ? 'rgb(3, 87, 95)' : 'rgb(89,198,198)'}}  !important; {{$acceptCasesPrice ? 'background: rgb(3, 87, 95) !important;' : ''}}">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="checked {{!$acceptCasesPrice ? 'd-none' : ''}}"
                                            style="width: 25px; height: 25px; color: white" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="unchecked {{$acceptCasesPrice ? 'd-none' : ''}}"
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
            @endif

            @if($invoiceWorkshopDatas->count())
                <div class="col-12 p-4 mb-4" style="border: 2px solid rgb(3, 87, 95)">
                    <h1 style="font-size: 20px;" class="w-100 pt-0 mb-4">{{__('invoice.workshops')}} ({{$invoiceWorkshopDatasPeriods->implode(' , ')}}): {{$invoiceWorkshopDatas->count()}}</h1>
                    <div class="row container" style="gap: 1.5rem">
                        @foreach ($invoiceWorkshopDatas as $invoiceWorkshopData)
                            <div class="col-2" style="padding: 0">
                                <div class="d-flex justify-content-center"
                                    style="border: 2px solid rgb(3, 87, 95); padding: 10px; color: black;">
                                    #{{$invoiceWorkshopData->activity_id}}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-8 mb-0">
                        <div class="input-group col-12 p-0"  style="border-color: rgb(3, 87, 95) !important;">
                            <div class="w-full col-12" style="padding: 12px 12px;{{$acceptWorkshopsPrice ? 'background: rgb(3, 87, 95);' : 'background: rgb(3, 87, 95);'}}; color:white">
                                {{__('invoice.workshops_sum')}} ({{$invoiceWorkshopDatasPeriods->implode(' , ')}}): {{$prices['workshopPrice']}} {{strtoupper($invoiceDatas->currency)}}
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-md-4 mb-0">
                        <div class="input-group col-12 p-0">
                            <label class="checkbox-container mt-0 w-100"
                                style="color: {{$acceptWorkshopsPrice ? 'rgb(3, 87, 95);' : 'rgb(89,198,198);'}} ; padding: 10px 0 10px 10px; border: 2px solid {{$acceptWorkshopsPrice ? 'rgb(3, 87, 95)' : 'rgb(89,198,198)'}}  !important; font-size: 16px; margin-top: 8px;">
                                {{__('invoice.agree_with_the_amount')}}
                                <input type="checkbox" class="delete_later d-none" wire:model='acceptWorkshopsPrice'>
                                <span class="checkmark d-flex justify-content-center align-items-center"
                                    style="left:auto; right: 0; height: 100%; width: 50px; border-left: 2px solid {{$acceptWorkshopsPrice ? 'rgb(3, 87, 95)' : 'rgb(89,198,198)'}}  !important; {{$acceptWorkshopsPrice ? 'background: rgb(3, 87, 95) !important;' : ''}}">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="checked {{!$acceptWorkshopsPrice ? 'd-none' : ''}}"
                                        style="width: 25px; height: 25px; color: white" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="unchecked {{$acceptWorkshopsPrice ? 'd-none' : ''}}"
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

            @if($invoiceCrisisDatas->count())
                <div class="col-12 p-4 mb-4" style="border: 2px solid rgb(3, 87, 95)">
                    <h1 style="font-size: 20px;" class="w-100 pt-0 mb-4"> {{__('invoice.crisises')}} ({{$invoiceCrisisDatasPeriods->implode(' , ')}}): {{$invoiceCrisisDatas->count()}}</h1>
                    <div class="row container" style="gap: 1.5rem">
                        @foreach ($invoiceCrisisDatas as $invoiceCrisisData)
                            <div class="col-2" style="padding: 0">
                                <div class="d-flex justify-content-center"
                                    style="border: 2px solid rgb(3, 87, 95); padding: 10px; color: black;">
                                    #{{$invoiceCrisisData->activity_id}}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-8 mb-0">
                        <div class="input-group col-12 p-0"  style="border-color: rgb(3, 87, 95) !important;">
                            <div class="w-full col-12" style="padding: 12px 12px; {{$acceptCrisisPrice ? 'background: rgb(3, 87, 95);' : 'background: rgb(3, 87, 95);'}}; color:white">
                                {{__('invoice.crisis_sum')}} ({{$invoiceCrisisDatasPeriods->implode(' , ')}}): {{$prices['crisisPrice']}} {{strtoupper($invoiceDatas->currency)}}
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-md-4 mb-0">
                        <div class="input-group col-12 p-0">
                            <label class="checkbox-container mt-0 w-100"
                                style="color: {{$acceptCrisisPrice ? 'rgb(3, 87, 95);' : 'rgb(89,198,198);'}} ; padding: 10px 0 10px 10px; border: 2px solid {{$acceptCrisisPrice ? 'rgb(3, 87, 95)' : 'rgb(89,198,198)'}}  !important; font-size: 16px; margin-top: 8px;">
                                {{__('invoice.agree_with_the_amount')}}
                                <input type="checkbox" class="delete_later d-none" wire:model='acceptCrisisPrice'>
                                <span class="checkmark d-flex justify-content-center align-items-center"
                                    style="left:auto; right: 0; height: 100%; width: 50px; border-left: 2px solid {{$acceptCrisisPrice ? 'rgb(3, 87, 95)' : 'rgb(89,198,198)'}}  !important; {{$acceptCrisisPrice ? 'background: rgb(3, 87, 95) !important;' : ''}}">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="checked {{!$acceptCrisisPrice ? 'd-none' : ''}}"
                                        style="width: 25px; height: 25px; color: white" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="unchecked {{$acceptCrisisPrice ? 'd-none' : ''}}"
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

            @if($invoiceOtherActivityDatas->count())
                <div class="col-12 p-4 mb-4" style="border: 2px solid rgb(3, 87, 95)">
                    <h1 style="font-size: 20px;" class="w-100 pt-0 mb-4"> {{__('invoice.other_activities')}} ({{$invoiceOtherActivityDatasPeriods->implode(' , ')}}): {{$invoiceOtherActivityDatas->count()}}</h1>
                    <div class="row container" style="gap: 1.5rem">
                        @foreach ($invoiceOtherActivityDatas as $invoiceOtherActivityData)
                            <div class="col-2" style="padding: 0">
                                <div class="d-flex justify-content-center"
                                    style="border: 2px solid rgb(3, 87, 95); padding: 10px; color: black;">
                                    {{$invoiceOtherActivityData->activity_id}}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-8 mb-0">
                        <div class="input-group col-12 p-0"  style="border-color: rgb(3, 87, 95) !important;">
                            <div class="w-full col-12" style="padding: 12px 12px; {{$acceptOtherActivityPrice ? 'background: rgb(3, 87, 95);' : 'background: rgb(3, 87, 95);'}}; color:white">
                                {{__('invoice.other_activity_sum')}} ({{$invoiceOtherActivityDatasPeriods->implode(' , ')}}): {{$prices['otherActivityPrice']}} {{strtoupper($invoiceDatas->currency)}}
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-md-4 mb-0">
                        <div class="input-group col-12 p-0">
                            <label class="checkbox-container mt-0 w-100"
                                style="color: {{$acceptOtherActivityPrice ? 'rgb(3, 87, 95);' : 'rgb(89,198,198);'}} ; padding: 10px 0 10px 10px; border: 2px solid {{$acceptOtherActivityPrice ? 'rgb(3, 87, 95)' : 'rgb(89,198,198)'}}  !important; font-size: 16px; margin-top: 8px;">
                                {{__('invoice.agree_with_the_amount')}}
                                <input type="checkbox" class="delete_later d-none" wire:model='acceptOtherActivityPrice'>
                                <span class="checkmark d-flex justify-content-center align-items-center"
                                    style="left:auto; right: 0; height: 100%; width: 50px; border-left: 2px solid {{$acceptOtherActivityPrice ? 'rgb(3, 87, 95)' : 'rgb(89,198,198)'}}  !important; {{$acceptOtherActivityPrice ? 'background: rgb(3, 87, 95) !important;' : ''}}">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="checked {{!$acceptOtherActivityPrice ? 'd-none' : ''}}"
                                        style="width: 25px; height: 25px; color: white" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="unchecked {{$acceptOtherActivityPrice ? 'd-none' : ''}}"
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

            @if($invoiceLiveWebinarDatas->count())
                <div class="col-12 p-4 mb-4" style="border: 2px solid rgb(3, 87, 95)">
                    <h1 style="font-size: 20px;" class="w-100 pt-0 mb-4">{{__('invoice.live_webinars')}} ({{$invoiceLiveWebinarDatasPeriods->implode(' , ')}}): {{$invoiceLiveWebinarDatas->count()}}</h1>
                    <div class="row container" style="gap: 1.5rem">
                        @foreach ($invoiceLiveWebinarDatas as $invoiceLiveWebinarData)
                            <div class="col-2" style="padding: 0">
                                <div class="d-flex justify-content-center"
                                    style="border: 2px solid rgb(3, 87, 95); padding: 10px; color: black;">
                                    #{{$invoiceLiveWebinarData->activity_id}}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-8 mb-0">
                        <div class="input-group col-12 p-0"  style="border-color: rgb(3, 87, 95) !important;">
                            <div class="w-full col-12" style="padding: 12px 12px;{{$acceptLiveWebinarPrice ? 'background: rgb(3, 87, 95);' : 'background: rgb(3, 87, 95);'}}; color:white">
                                {{__('invoice.live_webinars_sum')}} ({{$invoiceLiveWebinarDatasPeriods->implode(' , ')}}): {{$prices['liveWebinarPrice']}} {{strtoupper($invoiceDatas->currency)}}
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-md-4 mb-0">
                        <div class="input-group col-12 p-0">
                            <label class="checkbox-container mt-0 w-100"
                                style="color: {{$acceptLiveWebinarPrice ? 'rgb(3, 87, 95);' : 'rgb(89,198,198);'}} ; padding: 10px 0 10px 10px; border: 2px solid {{$acceptLiveWebinarPrice ? 'rgb(3, 87, 95)' : 'rgb(89,198,198)'}}  !important; font-size: 16px; margin-top: 8px;">
                                {{__('invoice.agree_with_the_amount')}}
                                <input type="checkbox" class="delete_later d-none" wire:model='acceptLiveWebinarPrice'>
                                <span class="checkmark d-flex justify-content-center align-items-center"
                                    style="left:auto; right: 0; height: 100%; width: 50px; border-left: 2px solid {{$acceptLiveWebinarPrice ? 'rgb(3, 87, 95)' : 'rgb(89,198,198)'}}  !important; {{$acceptLiveWebinarPrice ? 'background: rgb(3, 87, 95) !important;' : ''}}">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="checked {{!$acceptLiveWebinarPrice ? 'd-none' : ''}}"
                                        style="width: 25px; height: 25px; color: white" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="unchecked {{$acceptLiveWebinarPrice ? 'd-none' : ''}}"
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
        @endif

        @if ($custom_invoice_items->count() > 0)
            <h1 style="font-size: 20px;" class="w-100 pt-0">
                {{__('invoice.custom-item')}}
            </h1>
        @endif

        @foreach ($custom_invoice_items as $custom_invoice_item)

            <div class="form-row">
                <div class="form-group col-md-5 mb-0">
                    <div class="input-group col-12 p-0">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                {{__('invoice.custom-item-vars.item-name')}}:
                            </div>
                        </div>
                        <input type="text" value="{{$custom_invoice_item->name}}" disabled>
                    </div>
                </div>

                <div class="form-group col-md-4 mb-0">
                    <div class="input-group col-12 p-0">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                {{__('expert-data.country')}}:
                            </div>
                        </div>
                        <input type="text" value="{{$custom_invoice_item->country->name}}" disabled>
                    </div>
                </div>

                <div class="form-group col-md-3 mb-0">
                    <div class="input-group col-12 p-0">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                {{__('invoice.other_item_amount')}}:
                            </div>
                        </div>
                        <input type="text"  value="{{$custom_invoice_item->amount}}" disabled>
                    </div>
                </div>
            </div>
        @endforeach

        <h1 style="font-size: 20px;" class="w-100 pt-0">
            {{__('invoice.other-items')}}
        </h1>

        <div class="form-row">
            <div class="form-group col-md-5">
                <div class="input-group col-12 p-0">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            {{__('invoice.other_item_name')}}:
                        </div>
                    </div>
                    <input type="text" wire:model='newAdditionalInvoiceItemName'>
                </div>
            </div>

            <div class="form-group col-md-3 pr-0">
                <div class="input-group col-12 p-0">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            {{__('invoice.other_item_amount')}}:
                        </div>
                    </div>
                    <input type="text" wire:model='newAdditionalInvoiceItemPrice' placeholder="{{strtoupper($invoiceDatas->currency)}}">
                </div>
            </div>

            <div class="form-group col-md-4 pl-0">
                <button id="uploadCertificate" class="text-center btn-radius" type="button" style="--btn-height: 48px; --btn-margin-left: var(--btn-margin-x);" wire:click='addAdditionalInvoieItem'>
                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="width:20px; height:20px;"  fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                      </svg>
                    {{__('common.add')}}
                </button>
            </div>
        </div>

        @foreach ($additionalInvoiceItems as $additionalPriceItem)
            <div class="form-row">
                <div class="form-group col-md-5">
                    <div class="input-group col-12 p-0">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                {{__('invoice.other_item_name')}}:
                            </div>
                        </div>
                        <input type="text" wire:model='additionalInvoiceItems.{{ $loop->index }}.name'>
                    </div>
                </div>

                <div class="form-group col-md-3">
                    <div class="input-group col-12 p-0">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                {{__('invoice.other_item_amount')}}:
                            </div>
                        </div>
                        <input type="text" wire:model='additionalInvoiceItems.{{ $loop->index }}.price' placeholder="{{strtoupper($invoiceDatas->currency)}}">
                    </div>
                </div>

                <div style="margin-bottom: 20px;" class="col-1 d-flex justify-content-center align-items-center">
                    <svg wire:click="removeAdditionalInvoieItem({{$loop->index}})" xmlns="http://www.w3.org/2000/svg"
                            style="width: 25px; height: 25px; color: rgb(89,198,198); cursor: pointer; margin-bottom:15px !important" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>
            </div>
        @endforeach

        <div class="form-row" style="padding-top:30px">
            <div class="form-group col-md-8 mb-0">
                <div class="input-group col-12 p-0"  style="border-color: rgb(3, 87, 95) !important;">
                    <div class="w-full col-12" style="padding: 12px 12px; {{$acceptGrandTotalPrice ? 'background: rgb(3, 87, 95);' : 'background: rgb(3, 87, 95);'}} color:white">
                        {{__('invoice.total_amount')}}
                        ({{implode(' , ',array_unique(array_merge($invoiceCaseDatasPeriods->toArray(),$invoiceWorkshopDatasPeriods->toArray(), $invoiceCrisisDatasPeriods->toArray(), $invoiceOtherActivityDatasPeriods->toArray())))}}):
                        @if((!$invoiceCaseDatas->where('duration', null)->count() || auth()->user()->hasPermission(1)) || $casesPrice > 680)
                            {{$prices['grandTotal']}}
                        @endif
                        {{strtoupper($invoiceDatas->currency)}}
                    </div>
                 </div>
            </div>
            <div class="form-group col-md-4 mb-0">
                <div class="input-group col-12 p-0">
                    <label class="checkbox-container mt-0 w-100"
                        style="color: {{$acceptGrandTotalPrice ? 'rgb(3, 87, 95);' : 'rgb(89,198,198);'}} ; padding: 10px 0 10px 10px; border: 2px solid {{$acceptGrandTotalPrice ? 'rgb(3, 87, 95)' : 'rgb(89,198,198)'}}  !important; font-size: 16px; margin-top: 8px;">
                        {{__('invoice.agree_with_the_amount')}}
                        <input type="checkbox" class="delete_later d-none" wire:model='acceptGrandTotalPrice'>
                        <span class="checkmark d-flex justify-content-center align-items-center"
                            style="left:auto; right: 0; height: 100%; width: 50px; border-left: 2px solid {{$acceptGrandTotalPrice ? 'rgb(3, 87, 95)' : 'rgb(89,198,198)'}}  !important; {{$acceptGrandTotalPrice ? 'background: rgb(3, 87, 95) !important;' : ''}}">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="checked {{!$acceptGrandTotalPrice ? 'd-none' : ''}}"
                                style="width: 25px; height: 25px; color: white" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="unchecked {{$acceptGrandTotalPrice ? 'd-none' : ''}}"
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

        <div class="form-row" style="padding-top:30px">
            <div class="form-group col-md-8 mb-0 pr-0">
                <input type="text" class="col-12" placeholder="{{__('invoice.scanned_version_of_invoice')}}" disabled>
            </div>
            <div class="form-group col-md-4 mb-0 pl-0">
                <button id="uploadInvoice" type="button" style="--btn-height: 48px; --btn-margin-left: var(--btn-margin-x);" class="text-center btn-radius">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="width:20px; height:20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    {{__('common.upload')}}
                </button>
            </div>
        </div>

        @if($uploadedInvoice)
            <div class="form-row">
                <div class="form-group col-md-8">
                    <input type="text" class="col-12 dark" placeholder="{{$uploadedInvoice[0]->getClientOriginalName()}}" disabled>
                </div>
                <div class="form-group col-md-4">
                    <button wire:click="removeUploadedInvoice()" type="button" style="padding-bottom: 14px; background: rgb(0,87,95); padding-left: 0;" class="text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="width:20px; height:20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        {{__('common.cancel')}}
                    </button>
                </div>
            </div>
        @endif

        <div class="w-full mt-5 d-flex">
            <button type="submit" style="width:auto;" class="button btn-radius ml-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 mb-1" style="width: 20px; height:20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                </svg>
                {{__('common.save')}}
            </button>
        </div>

        <input id="uploadedInvoice" class="d-none" type="file" multiple wire:model='uploadedInvoice'>
    </form>
</div>
