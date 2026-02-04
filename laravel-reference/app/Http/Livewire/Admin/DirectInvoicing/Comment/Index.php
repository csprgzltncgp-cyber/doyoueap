<?php

namespace App\Http\Livewire\Admin\DirectInvoicing\Comment;

use App\Models\InvoiceComment;
use Livewire\Component;

class Index extends Component
{
    public $company;

    public $country;

    public $invoiceComments;

    public $directInvoiceDataId;

    protected $listeners = [
        'setInvoiceCommentsToNull' => 'setInvoiceCommentsToNull',
        'updateCountry' => 'updateCountry',
        'invoiceCommentDeleted' => 'updateInvoiceComments',
    ];

    public function mount($company, $directInvoiceDataId, $country = null): void
    {
        $this->directInvoiceDataId = $directInvoiceDataId;
        $this->company = $company;
        $this->country = $country;
    }

    public function render()
    {
        $this->updateInvoiceComments();

        return view('livewire.admin.direct-invoicing.comment.index');
    }

    public function addInvoiceComment(): void
    {
        $this->company->invoice_comments()->create([
            'country_id' => $this->country,
            'direct_invoice_data_id' => $this->directInvoiceDataId,
        ]);
    }

    public function setInvoiceCommentsToNull(): void
    {
        foreach ($this->invoiceComments as $invoiceComment) {
            if ($invoiceComment instanceof InvoiceComment) {
                $invoiceComment->delete();
            }
        }

        $this->invoiceComments = null;
    }

    public function updateInvoiceComments(): void
    {
        $this->invoiceComments = $this->company->invoice_comments()->where('direct_invoice_data_id', $this->directInvoiceDataId)->where('country_id', $this->country)->get();
    }

    public function updateCountry($country): void
    {
        $this->country = $country;
    }
}
