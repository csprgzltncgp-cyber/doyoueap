<?php

namespace App\Http\Livewire\Admin\DirectInvoicing;

use App\Models\DirectBillingData;
use App\Models\DirectInvoiceData;
use App\Models\InvoiceItem;
use Carbon\Carbon;
use Livewire\Component;

class InvoiceData extends Component
{
    public $company;

    public $country;

    public $directInvoiceData;

    public $modelId;

    public $customErrors;

    public $withSaveButton;

    public $inactivity_date_required;

    public bool $insideEu = false;

    protected $listeners = [
        'setDirectInvoiceDataToNull' => 'setDirectInvoiceDataToNull',
        'updateCountry' => 'updateCountry',
    ];

    protected $rules = [
        'directInvoiceData.name' => ['nullable'],
        'directInvoiceData.is_name_shown' => ['nullable', 'boolean'],
        'directInvoiceData.country' => ['nullable'],
        'directInvoiceData.postal_code' => ['nullable'],
        'directInvoiceData.city' => ['nullable'],
        'directInvoiceData.street' => ['nullable'],
        'directInvoiceData.house_number' => ['nullable'],
        'directInvoiceData.is_address_shown' => ['nullable', 'boolean'],
        'directInvoiceData.po_number' => ['nullable'],
        'directInvoiceData.is_po_number_shown' => ['nullable', 'boolean'],
        'directInvoiceData.is_po_number_changing' => ['nullable', 'boolean'],
        'directInvoiceData.is_po_number_required' => ['nullable', 'boolean'],
        'directInvoiceData.tax_number' => ['nullable'],
        'directInvoiceData.community_tax_number' => ['nullable'],
        'directInvoiceData.is_tax_number_shown' => ['nullable', 'boolean'],
        'directInvoiceData.group_id' => ['nullable'],
        'directInvoiceData.payment_deadline' => ['nullable'],
        'directInvoiceData.is_payment_deadlie_shown' => ['nullable', 'boolean'],
        'directInvoiceData.invoicing_inactive' => ['nullable', 'boolean'],
        'directInvoiceData.invoicing_inactive_to' => ['nullable', 'date:Y-m-d'],
    ];

    public function mount($company, $country = null, $modelId = null, $withSaveButton = false): void
    {
        $this->modelId = $modelId;
        $this->company = $company;
        $this->country = $country;
        $this->withSaveButton = $withSaveButton;
        $this->inactivity_date_required = false;
    }

    public function render()
    {
        if (! empty($this->modelId)) {
            $this->directInvoiceData = $this->company->direct_invoice_datas()->where('id', $this->modelId)->first();
        } else {
            $this->directInvoiceData = $this->company->direct_invoice_datas()->where('country_id', $this->country)->first();
        }

        if ($this->directInvoiceData) {
            $this->insideEu = (bool) DirectBillingData::query()->where(['direct_invoice_data_id' => $this->directInvoiceData->id, 'country_id' => $this->country])->first()?->inside_eu;
        }

        $this->customErrors = validate_direct_invoice_data($this->directInvoiceData, $this->insideEu);

        // $this->emitTo('admin.direct-invoicing.invoice-item.index', 'updateDirectInvoiceDataId', optional($this->directInvoiceData)->id);
        // $this->emitTo('admin.direct-invoicing.invoice-note.index', 'updateDirectInvoiceDataId', optional($this->directInvoiceData)->id);
        // $this->emitTo('admin.direct-invoicing.billing-data', 'updateDirectInvoiceDataId', optional($this->directInvoiceData)->id);

        return view('livewire.admin.direct-invoicing.invoice-data');
    }

    public function updated($propertyName, $value): void
    {
        $this->validateOnly($propertyName);

        $field = str_replace('directInvoiceData.', '', (string) $propertyName);

        if ($this->directInvoiceData instanceof DirectInvoiceData) {
            $this->directInvoiceData->{$field} = $value;
            $this->directInvoiceData->save();
        } else {
            $this->directInvoiceData = $this->company->direct_invoice_datas()->create([
                'country_id' => $this->country,
                $field => $value,
            ]);

            InvoiceItem::query()->create([
                'direct_invoice_data_id' => $this->directInvoiceData->id,
                'company_id' => $this->company->id,
                'country_id' => $this->country,
                'name' => 'Workshop',
                'input' => 2,
                'is_activity_id_shown' => true,
            ]);

            InvoiceItem::query()->create([
                'direct_invoice_data_id' => $this->directInvoiceData->id,
                'company_id' => $this->company->id,
                'country_id' => $this->country,
                'name' => 'Krízisintervenció',
                'input' => 3,
                'is_activity_id_shown' => true,
            ]);

            InvoiceItem::query()->create([
                'direct_invoice_data_id' => $this->directInvoiceData->id,
                'company_id' => $this->company->id,
                'country_id' => $this->country,
                'name' => 'Orientáció',
                'input' => 4,
                'is_activity_id_shown' => true,
            ]);
        }

        if ($field == 'is_po_number_required' && $value == 1) {
            $this->directInvoiceData->is_po_number_shown = 1;
            $this->directInvoiceData->save();
        }

        if ($field == '') {
            $this->validateOnly($field, $this->rules);
        }

        if ($field == 'invoicing_inactive') {
            if (! $value) {
                $this->directInvoiceData->update([
                    'invoicing_inactive_from' => null,
                    'invoicing_inactive_to' => null,
                ]);
                $this->inactivity_date_required = false;
            }
            $this->emit('disable_inputs', $value);
        }

        $this->emitTo('admin.direct-invoicing.container', 'loadCompanyRelations');
    }

    public function updateCountry($country): void
    {
        $this->country = $country;
    }

    public function setDirectInvoiceDataToNull(): void
    {
        if ($this->directInvoiceData instanceof DirectInvoiceData) {
            $this->directInvoiceData->delete();
        }

        $this->directInvoiceData = null;
    }

    public function save_invoicing_inactive(): void
    {
        $this->inactivity_date_required = false;

        if ($this->directInvoiceData->invoicing_inactive && ! $this->directInvoiceData->invoicing_inactive_to) {
            $this->inactivity_date_required = true;
            $this->emit("invoicing_inactive_date_missing_{$this->directInvoiceData->id}");

            return;
        }

        $this->directInvoiceData->update([
            'invoicing_inactive_from' => Carbon::now(),
            'invoicing_inactive_to' => $this->directInvoiceData->invoicing_inactive_to,
        ]);

        $this->emit('invoicing_inactive_saved');
    }
}
