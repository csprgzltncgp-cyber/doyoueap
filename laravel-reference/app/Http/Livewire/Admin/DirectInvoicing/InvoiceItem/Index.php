<?php

namespace App\Http\Livewire\Admin\DirectInvoicing\InvoiceItem;

use Livewire\Component;

class Index extends Component
{
    public $company;

    public $country;

    public $invoiceItems;

    public $currency;

    public $directInvoiceDataId;

    protected $listeners = [
        'setInvoiceItemsToNull' => 'setInvoiceItemsToNull',
        'updateCountry' => 'updateCountry',
        'updatedCurrency' => 'updateCurrency',
        'updateInvoiceItems' => 'updateInvoiceItems',
        'newInvoiceItem' => 'addInvoiceItem',
        'updateDirectInvoiceDataId' => 'updateDirectInvoiceDataId',
    ];

    public function mount($company, $directInvoiceDataId, $country = null): void
    {
        $this->company = $company;
        $this->directInvoiceDataId = $directInvoiceDataId;
        $this->country = $country;
        $this->currency = optional($this->company->direct_billing_datas()->where('country_id', $this->country)->where('direct_invoice_data_id', $this->directInvoiceDataId)->first())->currency;
    }

    public function render()
    {
        $this->updateInvoiceItems();
        $this->updateCurrency();

        return view('livewire.admin.direct-invoicing.invoice-item.index');
    }

    public function addInvoiceItem($directInvoiceDataId): void
    {
        if ((int) $directInvoiceDataId !== (int) $this->directInvoiceDataId) {
            return;
        }

        $this->company->invoice_items()->create([
            'country_id' => $this->country,
            'direct_invoice_data_id' => $this->directInvoiceDataId,
            'with_timestamp' => true,
        ]);
    }

    public function setInvoiceItemsToNull(): void
    {
        $this->invoiceItems = null;
        $this->company->invoice_items()->where('direct_invoice_data_id', $this->directInvoiceDataId)->delete();
    }

    public function updateCountry($country): void
    {
        $this->country = $country;
    }

    public function updateInvoiceItems(): void
    {
        $this->invoiceItems = $this->company->invoice_items()->with(['volume', 'amount'])->where('direct_invoice_data_id', $this->directInvoiceDataId)->where('country_id', $this->country)->get();
    }

    public function updateCurrency(): void
    {
        $this->currency = optional($this->company->direct_billing_datas()->where('country_id', $this->country)->first())->currency;
    }

    public function updateDirectInvoiceDataId($directInvoiceDataId): void
    {
        $this->directInvoiceDataId = $directInvoiceDataId;
    }
}
