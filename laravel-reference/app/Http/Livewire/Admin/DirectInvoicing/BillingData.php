<?php

namespace App\Http\Livewire\Admin\DirectInvoicing;

use App\Models\DirectBillingData;
use Illuminate\Support\Str;
use Livewire\Component;

class BillingData extends Component
{
    public $company;

    public $country;

    public $directBillingData;

    public $directBillingDataEmails;

    public $directInvoiceDataId;

    public $customErrors;

    protected $listeners = [
        'setDirectBillingDataToNull' => 'setDirectBillingDataToNull',
        'updateCountry' => 'updateCountry',
        'updateDirectInvoiceDataId' => 'updateDirectInvoiceDataId',
    ];

    protected $rules = [
        'directBillingData.billing_frequency' => ['nullable'],
        'directBillingData.invoice_language' => ['nullable'],
        'directBillingData.currency' => ['nullable'],
        'directBillingData.vat_rate' => ['nullable'],
        'directBillingData.inside_eu' => ['boolean'],
        'directBillingData.outside_eu' => ['boolean'],
        'directBillingData.send_invoice_by_post' => ['nullable', 'boolean'],
        'directBillingData.send_completion_certificate_by_post' => ['nullable', 'boolean'],
        'directBillingData.custom_email_subject' => ['nullable'],
        'directBillingData.post_code' => ['nullable'],
        'directBillingData.country' => ['nullable'],
        'directBillingData.city' => ['nullable'],
        'directBillingData.street' => ['nullable'],
        'directBillingData.house_number' => ['nullable'],
        'directBillingData.send_invoice_by_email' => ['nullable', 'boolean'],
        'directBillingData.send_completion_certificate_by_email' => ['nullable', 'boolean'],
        'directBillingData.upload_invoice_online' => ['nullable'],
        'directBillingData.invoice_online_url' => ['nullable'],
        'directBillingData.upload_completion_certificate_online' => ['nullable', 'boolean'],
        'directBillingData.completion_certificate_online_url' => ['nullable'],
        'directBillingData.contact_holder_name' => ['nullable'],
        'directBillingData.show_contact_holder_name_on_post' => ['nullable', 'boolean'],
        'directBillingDataEmails.*.email' => ['nullable'],
        'directBillingDataEmails.*.is_cc' => ['nullable', 'boolean'],

    ];

    public function mount($company, $directInvoiceDataId, $country = null): void
    {
        $this->directInvoiceDataId = $directInvoiceDataId;
        $this->company = $company;
        $this->country = $country;
    }

    public function render()
    {
        $this->directBillingData = $this->company->direct_billing_datas()
            ->where('country_id', $this->country)
            ->where('direct_invoice_data_id', $this->directInvoiceDataId)
            ->first();

        $this->directBillingDataEmails = $this->directBillingData ? $this->directBillingData->emails()->orderBy('id')->get() : null;

        if ($this->directBillingData && (empty($this->directBillingDataEmails) || ! $this->directBillingDataEmails->count())) {
            $this->addBillingDataEmail();
        }

        if ($this->directBillingDataEmails) {
            $this->directBillingDataEmails = $this->directBillingDataEmails->sortBy('id');
        }

        $this->customErrors = $this->directBillingData ? validate_direct_billing_data($this->directBillingData) : [];

        return view('livewire.admin.direct-invoicing.billing-data');
    }

    public function updatedDirectBillingData($value, $propertyName): void
    {
        $this->validateOnly($propertyName);

        $field = str_replace('directBillingData.', '', (string) $propertyName);

        if ($field == 'currency') {
            $this->emitTo('admin.direct-invoicing.invoice-item.index', 'updatedCurrency');
            $this->emitTo('admin.direct-invoicing.invoice-item.show', 'updatedCurrency', $value);
        }

        if ($field == 'inside_eu' || $field == 'outside_eu') {
            $this->directBillingData->vat_rate = null;
        }

        if ($this->directBillingData instanceof DirectBillingData) {
            $this->directBillingData->{$field} = $value;
            $this->directBillingData->save();
        } else {
            $this->directBillingData = $this->company->direct_billing_datas()->create([
                'country_id' => $this->country,
                'direct_invoice_data_id' => $this->directInvoiceDataId,
                $field => $value,
            ]);
        }

        $this->customErrors = validate_direct_billing_data($this->directBillingData);
    }

    public function updatedDirectBillingDataEmails($value, $propertyName): void
    {
        $email = Str::of((string) $value)->lower()->trim()->value();

        $model_id = $this->directBillingDataEmails[Str::before($propertyName, '.')]->id;
        $property = Str::after($propertyName, '.');

        // Check if email address is valid
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->directBillingData->emails()->where('id', $model_id)->update([
                $property => $email,
            ]);
        } else {
            $this->emit('directBillingDataEmailsError', __('company-edit.invalid_billing_email'));
        }
    }

    public function addBillingDataEmail(): void
    {
        $this->directBillingData->emails()->create();
        $this->directBillingDataEmails = $this->directBillingData->emails()->get();
    }

    public function deleteBillingDataEmail($id): void
    {
        $this->directBillingData->emails()->where('id', $id)->delete();
        $this->directBillingDataEmails = $this->directBillingData->emails()->get();
    }

    public function updateCountry($country): void
    {
        $this->country = $country;
    }

    public function setDirectBillingDataToNull(): void
    {
        if ($this->directBillingData instanceof DirectBillingData) {
            $this->directBillingData->delete();
        }

        $this->directBillingData = null;
    }

    public function updateDirectInvoiceDataId($directInvoiceDataId): void
    {
        $this->directInvoiceDataId = $directInvoiceDataId;
    }
}
