<?php

namespace App\Http\Livewire\Admin\DirectInvoicing\InvoiceNote;

use Livewire\Component;

class Show extends Component
{
    public $invoiceNote;

    protected $rules = [
        'invoiceNote.value' => ['required', 'string'],
    ];

    public function mount($invoiceNote): void
    {
        $this->invoiceNote = $invoiceNote;
    }

    public function render()
    {
        return view('livewire.admin.direct-invoicing.invoice-note.show');
    }

    public function updated(): void
    {
        $this->validate();
        $this->invoiceNote->save();
    }

    public function delete(): void
    {
        $this->invoiceNote->delete();
        $this->emitUp('invoiceNoteDeleted');
    }
}
