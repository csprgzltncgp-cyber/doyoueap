@extends('layout.master')

@section('title')
    Expert Dashboard
@endsection

@section('extra_js')
    <script>
        const are_you_sure_you_want_to_delete_your_invoice = '{{__("common.are-you-sure-you-want-to-delete-your-invoice")}}';
        const operation_isnt_reversible = '{{__("common.operation-is-not-reversible")}}';
        const yes_delete_it = '{{__("common.yes-delete-it")}}';
        const cancel = '{{__("common.cancel")}}';
        const deletion_was_unsuccessful = '{{__("common.deletion-was-unsuccessful")}}';
        const error = '{{__("common.error")}}';
        const editing_was_unsuccessful = '{{__("common.editing-your-invoice-was-unsuccessful")}}';
        const are_you_sure_you_want_to_delete_caseid = '{{__("common.are-you-sure-to-delete-caseid")}}';
        const deleting_your_case_id_was_unsuccessful = '{{__("common.deleting-your-case-id-was-unsuccessfu")}}';
        const system_message = '{{__("common.system-message")}}';
        const invoice_due_date_if_you_1 = '{{__("common.invoice-due-date-if-you-1")}}';
        const invoice_due_date_if_you_2 = '{{__("common.invoice-due-date-if-you-2")}}';
        const are_you_sure_your_invoice_is_settled = '{{__("common.are-you-sure-your-invoice-is-settled")}}';
        const are_you_sure_your_invoice_is_unsettled = '{{__("common.are-you-sure-your-invoice-is-unsettled")}}';
        const are_you_sure_you_want_to_delete_this = '{{__("common.are-you-sure-you-want-to-delete-this")}}';
        const yes = '{{__("common.yes")}}';
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9.7.1/dist/sweetalert2.all.min.js"></script>
    <script src="/assets/js/invoice/master.js?v={{time()}}" charset="utf-8"></script>
@endsection

@section('extra_css')
    <link rel="stylesheet" href="/assets/css/list.css?v={{time()}}">
    <link rel="stylesheet" href="/assets/css/invoices.css?v={{time()}}">
@endsection

@section('content')
    <div class="row m-0 w-100">
        <h1 class="col-12 pl-0">{{__('common.list-of-invoices')}}</h1>
        <x-invoices.submenu />
        @foreach($invoices as $invoice)
            <div class="list-element col-12">
                <span class="data mr-0">{{$invoice->number}}</span>
                <a class="edit-invoice btn-radius" style="--btn-margin-left: var(--btn-margin-x);"
                   href="{{route('expert.invoices.view',['id' => $invoice->id])}}">
                   <img src="{{asset('assets/img/select.svg')}}" class="mr-1" style="height: 20px; width: 20px" alt="">
                   {{__('common.select')}}
                </a>
                <button class="event btn-radius @if($invoice->last_event() && $invoice->last_event()->event == 'invoice_paid') active @endif"
                        onClick="invoiceEventCreateExpert('invoice_paid', {{$invoice->id}}, this)" id="invoice_paid">
                        <svg xmlns="http://www.w3.org/2000/svg" class="ml-1" style="width:20px; height:20px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                          </svg>
                    {{__('common.invoice-settled')}}!
                </button>
                <button
                    class="event btn-radius @if($invoice->last_event() && $invoice->last_event()->event == 'invoice_expired_and_not_paid') active @endif"
                    onClick="invoiceEventCreateExpert('invoice_expired_and_not_paid', {{$invoice->id}}, this, {{$invoice->afterPaymentDeadline()}}, '{{$invoice->payment_deadline}}')"
                    id="invoice_expired_and_not_paid">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="height:20px; width:20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    {{__('common.unsettled-invoice')}}!
                </button>
                @if($invoice->status != 'listed_in_a_bank')
                    <button onClick="deleteInvoice({{$invoice->id}}, this)" class="invoice-delete-by-expert btn-radius">
                        <img src="{{asset('assets/img/delete.svg')}}" class="mr-1" style="height: 20px; width: 20px" alt="">
                        <span class="pt-2" style="color: white;">{{__('common.delete')}}</span>
                    </button>
                @endif
                @if($invoice->last_event() && $invoice->last_event()->event == 'invoice_payment_sent')
                    <button class="event active" id="invoice_payment_sent btn-radius">
                        {{__('common.transfer-in-progress')}}!
                    </button>
                @endif
            </div>

        @endforeach
    </div>
@endsection
