<?php

namespace App\Http\Livewire\Admin\DirectInvoicing\InvoiceNote;

use App\Models\InvoiceNote;
use Livewire\Component;

class Index extends Component
{
    public $company;

    public $country;

    public $invoiceNotes;

    public $directInvoiceDataId;

    protected $listeners = [
        'setInvoiceNotesToNull' => 'setInvoiceNotesToNull',
        'updateCountry' => 'updateCountry',
        'invoiceNoteDeleted' => 'updateInvoiceNotes',
        'newInvoiceNote' => 'addInvoiceNote',
        'updateDirectInvoiceDataId' => 'updateDirectInvoiceDataId',
    ];

    public function mount($company, $directInvoiceDataId, $country = null): void
    {
        $this->directInvoiceDataId = $directInvoiceDataId;
        $this->company = $company;
        $this->country = $country;
    }

    public function render()
    {
        $this->updateInvoiceNotes();

        return view('livewire.admin.direct-invoicing.invoice-note.index');
    }

    public function addInvoiceNote($directInvoiceDataId): void
    {
        if ((int) $directInvoiceDataId !== (int) $this->directInvoiceDataId) {
            return;
        }

        $this->company->invoice_notes()->create([
            'country_id' => $this->country,
            'direct_invoice_data_id' => $this->directInvoiceDataId,
        ]);
    }

    public function setInvoiceNotesToNull(): void
    {
        foreach ($this->invoiceNotes as $invoiceNote) {
            if ($invoiceNote instanceof InvoiceNote) {
                $invoiceNote->delete();
            }
        }
        $this->invoiceNotes = null;
    }

    public function updateInvoiceNotes(): void
    {
        $this->invoiceNotes = $this->company->invoice_notes()->where('country_id', $this->country)->where('direct_invoice_data_id', $this->directInvoiceDataId)->get();
    }

    public function updateCountry($country): void
    {
        $this->country = $country;
    }

    public function updateDirectInvoiceDataId($directInvoiceDataId): void
    {
        $this->directInvoiceDataId = $directInvoiceDataId;
    }
}
