<div>
        <h1 style="font-size: 18px; color:black">{{__('company-edit.items_to_be_invoiced')}}:</h1>

        @foreach ($invoiceItems->sortBy('id')->whereNotIn('input', [App\Models\InvoiceItem::INPUT_TYPE_WORKSHOP, App\Models\InvoiceItem::INPUT_TYPE_CRISIS, App\Models\InvoiceItem::INPUT_TYPE_OTHER_ACTIVITY]) as $invoiceItem)
            @livewire('admin.direct-invoicing.invoice-item.show', ['invoiceItem' => $invoiceItem, 'currency' => $currency, 'company' => $company], key($loop->index . '-invoice-item-' . $directInvoiceDataId . '-' . $invoiceItem->id))
        @endforeach

        @foreach ($invoiceItems->sortBy('id')->whereIn('input', [App\Models\InvoiceItem::INPUT_TYPE_WORKSHOP, App\Models\InvoiceItem::INPUT_TYPE_CRISIS, App\Models\InvoiceItem::INPUT_TYPE_OTHER_ACTIVITY]) as $invoiceItem)
            @livewire('admin.direct-invoicing.invoice-item.show', ['invoiceItem' => $invoiceItem, 'currency' => $currency, 'company' => $company], key($loop->index . '-other-invoice-item-' . $directInvoiceDataId . '-' . $invoiceItem->id))
        @endforeach
</div>
