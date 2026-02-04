@extends('layout.master')

@section('title', 'Admin Dashboard')

@section('extra_css')
    <link rel="stylesheet" href="{{ asset('/assets/css/invoice-helper/invoicing.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/invoice-helper/cgp-card.css') }}">
    <link rel="stylesheet" href="{{asset('assets/css/list.css')}}?v={{time()}}">
    <link rel="stylesheet" href="{{asset('assets/css/invoices.css')}}?v={{time()}}">
    <link rel="stylesheet" href="/assets/css/form.css?v={{time()}}">
    <link rel="stylesheet" href="/assets/css/perfix-input.css?v={{time()}}">
    <link rel="stylesheet" href="/assets/css/cases/datetime.css?t={{ time() }}">
    <style>
        .lis-element-div {
            width: 100%;
            display: none;
        }

        .lis-element-div.active {
            display: block !important;
        }

        .lis-element-div-c {
            display: none;
        }

        .lis-element-div-c.active {
            display: block !important;
        }

        .list-element.col-12 {
            display: inline-block !important;
        }

        .swal2-overflow {
            overflow-x: visible;
            overflow-y: visible;
        }

        </style>
@endsection

@section('extra_js')
    <script src="/assets/js/datetime.js" charset="utf-8"></script>

    <script>
        document.querySelectorAll('.date-toggle').forEach(function (element) {
            element.addEventListener('click', function (e) {
                e.preventDefault();
                let svg = e.target.closest('.date-toggle').querySelector('.date-svg');
                svg.style.transform = svg.style.transform === 'rotateZ(180deg)' ? '' : 'rotateZ(180deg)';
            });
        });
    </script>
@endsection

@push('livewire_js')
    <script>
        const spinner = document.getElementById('invoices_loading_spinner');
        const months_container = document.getElementById('months_container');
        const year = "{{ $selected_year }}";
        const redirect_url = '{{route('admin.invoice-helper.direct-invoicing.index')}}';
        
        Livewire.on('show-pdf', function(data){
            let pdfWindow = window.open("")
                pdfWindow.document.write(
                    "<iframe width='100%' height='100%' src='data:application/pdf;base64, " +
                    encodeURI(data.file) + "'></iframe>"
            )
        });

        Livewire.on('alert', function(data){
            Swal.fire({
                title: data.message,
                text: '',
                icon: data.type,
                confirmButtonText: 'Ok'
            });
        });

        async function showInvoicePaidPopup(componentId, paidAt, paidAmount){
            const { value: formValues } = await Swal.fire({
                title: '{{__('invoice.currency_title')}}',
                html:
                    `
                    <label style="float: left; font-size:15px; margin-top:15px" for="paid_at">{{__('invoice-helper.invoice-paid-at')}}</label>
                    <div data-content="">
                        <input value="${paidAt}" style="margin-top:0" id="paid_at" class="swal2-input swal2-overflow" type="text" required/>
                    </div>

                    <label style="float: left; font-size:15px; margin-top:15px" for="paid_amount">{{__('invoice-helper.invoice-paid-amount')}}</label>
                    <div data-content="">
                        <input value="${paidAmount ? paidAmount : ''}" style="margin-top:0" id="paid_amount" class="swal2-input swal2-overflow" type="text" required/>
                    </div>
                    `,
                focusConfirm: false,
                onOpen: () => {
                    $('#paid_at').datepicker({
                        format: 'yyyy-mm-dd',
                        defaultDate: paidAt ? new Date(paidAt) : new Date(),
                    });

                    Swal.getConfirmButton().focus();
                },
                preConfirm: () => {
                    return {
                        paid_at: document.getElementById('paid_at').value,
                        paid_amount: document.getElementById('paid_amount').value ?? paidAmount,
                    }
                }
            });

            if (formValues) {
                Livewire.find(componentId).update_invoice_paid(formValues.paid_at, formValues.paid_amount);
            }
        }
    </script>
@endpush

@section('content')
<div>
    {{ Breadcrumbs::render('invoices.direct-invoices') }}

    <h1>
        {{__('invoice-helper.invoice-helper')}}
    </h1>

    <x-invoice-helper.menu :contractHolderCompany="$contract_holder_company" :invoicing_years="$invoicing_years" :selected_year="$selected_year" />

    <img id="invoices_loading_spinner" class="d-none spinner" src="{{asset('assets/img/spinner.svg')}}"
    alt="spinner">

    <div id="months_container">
        @foreach(collect($dates)->reverse() as $date)
            <div class="case-list-in col-12 group date-toggle d-flex justify-content-between align-items-center" onclick="Livewire.emit('openDate', '{{$date->format('Y-m-d')}}')">
                <div class="d-flex">
                    {{-- YELLOW ALERT --}}
                    @if(has_missing_information_on_direct_invoices(get_direct_invoices_in_month($date, $contract_holder_company)))
                        <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height:20px; color:#ffc208; margin-right:5px;" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    @endif
                    {{-- YELLOW ALERT --}}

                    {{-- RED ALERT --}}
                    @if(has_companies_missing_information(get_companies_in_month($date,  $contract_holder_company)))
                        <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height:20px; color:#f70000; margin-right:5px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    @endif
                    {{-- RED ALERT --}}

                    {{-- GREEN ALERT --}}
                    @if(has_invoice_with_no_missing_company_and_direct_invoice_information(get_direct_invoices_in_month($date, $contract_holder_company)))
                        <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height:20px; color:#91b752; margin-right:5px;" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M3 12v3c0 1.657 3.134 3 7 3s7-1.343 7-3v-3c0 1.657-3.134 3-7 3s-7-1.343-7-3z" />
                            <path d="M3 7v3c0 1.657 3.134 3 7 3s7-1.343 7-3V7c0 1.657-3.134 3-7 3S3 8.657 3 7z" />
                            <path d="M17 5c0 1.657-3.134 3-7 3S3 6.657 3 5s3.134-3 7-3 7 1.343 7 3z" />
                        </svg>
                    @endif
                    {{-- GREEN ALERT --}}

                    {{-- PURPLE ALERT --}}
                    @if(has_invoice_with_done_status(get_direct_invoices_in_month($date, $contract_holder_company)))
                        <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height:20px; color:#a33095; margin-right:5px;" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    @endif
                    {{-- PURPLE ALERT --}}

                    <span>{{$date->format('Y-m')}}</span>
                </div>

                <button class="caret-left float-right">
                    <svg class="date-svg" id="d{{$date->format('Y-m-d')}}" xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px;"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
            </div>
            @livewire('admin.invoice-helper.direct-invoicing.month', ['date' => $date->format('Y-m-d'), 'contractHolderCompany' => $contract_holder_company], key('date-' . $date->format('Y-m-d')))
        @endforeach
    </div>
</div>
@endsection
