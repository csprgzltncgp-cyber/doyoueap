<?php

namespace App\Http\Livewire\Admin\DirectInvoicing;

use App\Models\CountryDifferentiate;
use App\Models\InvoiceItem;
use Livewire\Component;

class Container extends Component
{
    public $company;

    public $countryDifferentiates;

    public $currentDirectInvoicingCountry;

    public $openedDirectInvoiceDatas;

    public $directInvoiceDatas;

    public $includeSaveButtonOnInvoiceData = false;

    protected $listeners = [
        'updatedCountryDifferentiates' => 'updateCountryDifferentiates',
        'loadCompanyRelations' => 'loadCompanyRelations',
        'createList' => 'createListFromDirectInvoiceDatas',
        'deleteDirectInvoiceData' => 'deleteDirectInvoiceData',
    ];

    public function mount($company, $countryDifferentiates, $includeSaveButtonOnInvoiceData = false): void
    {
        $this->company = $company;
        $this->countryDifferentiates = $countryDifferentiates;
        $this->openedDirectInvoiceDatas = [];
        $this->includeSaveButtonOnInvoiceData = $includeSaveButtonOnInvoiceData;

        $this->setupCurrentDirectInvoicingCountry();
    }

    public function render()
    {
        $this->directInvoiceDatas = $this->company->direct_invoice_datas()->where('country_id', $this->currentDirectInvoicingCountry)->get();

        return view('livewire.admin.direct-invoicing.container');
    }

    public function loadCompanyRelations(): void
    {
        $this->company->load(['direct_invoice_datas']);
    }

    public function updateCountryDifferentiates(CountryDifferentiate $countryDifferentiates): void
    {
        $this->directInvoiceDatas = [];

        $this->countryDifferentiates = $countryDifferentiates;

        $this->setupCurrentDirectInvoicingCountry();

        $this->emitTo('admin.direct-invoicing.invoice-data', 'setDirectInvoiceDataToNull');
        $this->emitTo('admin.direct-invoicing.billing-data', 'setDirectBillingDataToNull');
        $this->emitTo('admin.direct-invoicing.invoice-item.index', 'setInvoiceItemsToNull');
        $this->emitTo('admin.direct-invoicing.invoice-note.index', 'setInvoiceNotesToNull');
        $this->emitTo('admin.direct-invoicing.comment.index', 'setInvoiceCommentsToNull');
    }

    public function updateCurrentDirectInvoicingCountry($value): void
    {
        $this->currentDirectInvoicingCountry = $value;

        $this->emitTo('admin.direct-invoicing.invoice-data', 'updateCountry', $value);
        $this->emitTo('admin.direct-invoicing.billing-data', 'updateCountry', $value);
        $this->emitTo('admin.direct-invoicing.invoice-item.index', 'updateCountry', $value);
        $this->emitTo('admin.direct-invoicing.invoice-note.index', 'updateCountry', $value);
        $this->emitTo('admin.direct-invoicing.comment.index', 'updateCountry', $value);
    }

    public function createListFromDirectInvoiceDatas(): void
    {
        $newDirectInvoiceData = $this->company->direct_invoice_datas()->create([
            'country_id' => $this->currentDirectInvoicingCountry,
            'name' => __('company-edit.new-invoice-data'),
        ]);

        InvoiceItem::query()->create([
            'direct_invoice_data_id' => $newDirectInvoiceData->id,
            'company_id' => $this->company->id,
            'country_id' => $this->currentDirectInvoicingCountry,
            'name' => 'Workshops',
            'input' => 2,
            'is_activity_id_shown' => true,
        ]);

        InvoiceItem::query()->create([
            'direct_invoice_data_id' => $newDirectInvoiceData->id,
            'company_id' => $this->company->id,
            'country_id' => $this->currentDirectInvoicingCountry,
            'name' => 'Crisis Interventions',
            'input' => 3,
            'is_activity_id_shown' => true,
        ]);

        InvoiceItem::query()->create([
            'direct_invoice_data_id' => $newDirectInvoiceData->id,
            'company_id' => $this->company->id,
            'country_id' => $this->currentDirectInvoicingCountry,
            'name' => 'Other Activities',
            'input' => 4,
            'is_activity_id_shown' => true,
        ]);

        $this->openedDirectInvoiceDatas = [];
        $this->openedDirectInvoiceDatas[] = $newDirectInvoiceData->id;
    }

    public function openDirectInvoiceData($id): void
    {
        if (in_array($id, $this->openedDirectInvoiceDatas)) {
            $this->openedDirectInvoiceDatas = array_diff($this->openedDirectInvoiceDatas, [$id]);
        } else {
            $this->openedDirectInvoiceDatas[] = $id;
        }
    }

    public function deleteDirectInvoiceData($id): void
    {
        $this->company->direct_billing_datas()->where('direct_invoice_data_id', $id)->delete();
        $this->company->invoice_items()->where('direct_invoice_data_id', $id)->delete();
        $this->company->invoice_notes()->where('direct_invoice_data_id', $id)->delete();
        $this->company->invoice_comments()->where('direct_invoice_data_id', $id)->delete();
        $this->company->direct_invoice_datas()->where('id', $id)->delete();
    }

    public function editDirectInvoiceAdminIdentifier($id, $newAdminIdentifier): void
    {
        $this->company->direct_invoice_datas()->where('id', $id)->update(['admin_identifier' => $newAdminIdentifier]);

    }

    private function setupCurrentDirectInvoicingCountry(): void
    {
        $country = $this->countryDifferentiates->invoicing ? optional($this->company->countries->first())->id : null;
        $this->updateCurrentDirectInvoicingCountry($country);
    }
}
