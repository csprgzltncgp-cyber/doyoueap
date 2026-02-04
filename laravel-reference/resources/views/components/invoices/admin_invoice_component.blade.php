<div class="list-element col-12 invoice-admin-component">
    <span
            class="data mr-0 @if($invoice->getRawOriginal('status') == 'listed_in_a_bank') listed_in_a_bank @endif @if($event && $event->event == 'invoice_expired_and_not_paid') invoice-expired-and-not-paid-event @endif @if($invoice->data_changes->count()) invoice-data-changed @endif">@if($invoice->data_changes->count())
            <svg xmlns="http://www.w3.org/2000/svg" class="mr-0" style="width:20px; height:20px" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
</svg>
        @endif #{{$invoice->number}} - {{Carbon\Carbon::parse($invoice->created_at)->format('Y-m-d')}} - {{$invoice->grand_total}} - {{optional($invoice->expert)->name}} - {{__('common.number_of_consultations')}}: {{$invoice->case_datas->sum('consultations_count')}}</span>
    <a class="edit-invoice btn-radius" style="--btn-margin-left: var(--btn-margin-x)" href="{{route('admin.invoices.view',['id' => $invoice->id])}}">
        <img src="{{asset('assets/img/select.svg')}}" class="mr-1" style="height: 20px; width: 20px" alt="">
        {{__('common.select')}}
    </a>

    <button class="invoice-list-action btn-radius admin-invoice-index-download @if($invoice->seen) downloaded-invoice @endif"
        onClick="toggleSeen({{$invoice->id}}, this)" style="--btn-min-width: var(--btn-func-width);">
        <svg xmlns="http://www.w3.org/2000/svg"  class="mr-0" style="width: 20px; height: 20px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
        </svg>
     </button>

    <a class="invoice-list-action btn-radius admin-invoice-index-download @if($invoice->downloaded_by) downloaded-invoice @endif"
       target="_blank" href="{{route('admin.invoices.downloadInvoice',['id' => $invoice->id])}}" style="--btn-min-width: var(--btn-func-width);">
        <svg xmlns="http://www.w3.org/2000/svg" class="mr-0" style="width: 20px; height: 20px" fill="none"
             viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
        </svg>
    </a>

    <button class="invoice-list-action btn-radius @if($invoice->getRawOriginal('status') == 'listed_in_a_bank') downloaded-invoice @endif" id="listed-in-a-bank"
        style="--btn-min-width: var(--btn-func-width);"
            @if($invoice->getRawOriginal('status') == 'listed_in_a_bank')
                onClick="setInvoiceStatus({{$invoice->id}},'created', this)"
            @else
                onClick="setInvoiceStatus({{$invoice->id}},'listed_in_a_bank', this)"
            @endif
            style="--btn-min-width: var(--btn-func-width);">
        <svg xmlns="http://www.w3.org/2000/svg" class="mr-0" style="width: 20px; height: 20px" fill="none"
                viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
    </button>

    <button id="calendar_button_{{$invoice->id}}" class="delete_invoice_admin invoice-list-action btn-radius invoice-delete-by-expert"
        style="--btn-min-width: var(--btn-func-width); @if( has_invoicing_opened($invoice->expert)) background-color: #eb7e30; @endif"
        onClick="openInvoicing({{$invoice->user_id}}, this)">
        <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height:20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
    </button>

    <button id="delete_button_{{$invoice->id}}" class="delete_invoice_admin invoice-list-action btn-radius invoice-delete-by-expert"
        style="--btn-min-width: var(--btn-func-width);"
        onClick="deleteInvoiceByAdmin({{$invoice->id}}, this)">
        <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
        </svg>
    </button>

    @if($event)
        @if($event->event == 'invoice_expired_and_not_paid')
            <button class="invoice-list-action expert-invoice-not-paid btn-radius" style="--btn-min-width: var(--btn-func-width);"
                    onClick="invoiceEventCreate('invoice_payment_sent', {{$invoice->id}}, this)"
                    style="--btn-min-width: var(--btn-func-width);">
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-0" style="width: 20px; height: 20px" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg> {{__('common.unpaid_invoice')}}
            </button>
        @elseif($event->event == 'invoice_paid')
            <button class="invoice-list-action expert-invoice-paid btn-radius" style="--btn-min-width: var(--btn-func-width);" onClick="revertInvoicePaid({{$invoice->id}},this)">
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-0" style="width: 20px; height: 20px" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg> {{__('common.paid_invoice')}}
            </button>
        @endif
    @endif

    @if($invoice->deleted_by_expert_at)
        <button class="invoice-list-action invoice-deleted-by-expert btn-radius" style="--btn-min-width: var(--btn-func-width);"
                onClick="deleteInvoiceByAdmin({{$invoice->id}}, this)">{{__('common.deleted_invoice')}}
        </button>
    @endif

    @if($invoice->getRawOriginal('status') == 'created' && Carbon\Carbon::parse($invoice->payment_deadline) < Carbon\Carbon::now())
        <span class="invoice-info payment-due">
            <svg xmlns="http://www.w3.org/2000/svg" class="mr-0" style="height: 20px; width: 20px" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            {{__('common.payment_deadline')}}
        </span>
    @endif

    @if($invoice->additional_invoice_items->count())
        <span class="invoice-info additional-invoice-items">
            {{__('common.additional_invoice_items')}}
        </span>
    @endif
</div>
