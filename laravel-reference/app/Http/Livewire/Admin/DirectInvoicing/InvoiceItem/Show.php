<?php

namespace App\Http\Livewire\Admin\DirectInvoicing\InvoiceItem;

use App\Models\InvoiceItem as InvoiceItemModel;
use App\Traits\InvoiceHelper\ContractHolderTrait;
use Illuminate\Support\Str;
use Livewire\Component;

class Show extends Component
{
    use ContractHolderTrait;

    public $invoiceItem;

    public $currency;

    public $company;

    public $amount;

    public $volume;

    public $isCommentShown;

    public $customErrors;

    protected $listeners = [
        'updatedCurrency' => 'updateCurrency',
    ];

    protected $rules = [
        'invoiceItem.name' => ['required'],
        'invoiceItem.input' => ['required', 'numeric'],
        'invoiceItem.comment' => ['nullable', 'string'],
        'invoiceItem.data_request_email' => ['nullable', 'email'],
        'invoiceItem.data_request_salutation' => ['nullable', 'string'],
        'invoiceItem.is_activity_id_shown' => ['sometimes', 'nullable', 'boolean'],
        'invoiceItem.with_timestamp' => ['sometimes', 'nullable', 'boolean'],
        'invoiceItem.shown_by_item' => ['sometimes', 'nullable', 'boolean'],
        'amount.name' => ['nullable'],
        'amount.value' => ['nullable'],
        'amount.is_changing' => ['sometimes', 'nullable', 'boolean'],
        'volume.name' => ['nullable'],
        'volume.value' => ['nullable'],
        'volume.is_changing' => ['sometimes', 'nullable', 'boolean'],
    ];

    public function mount($invoiceItem, $currency, $company): void
    {
        $this->invoiceItem = $invoiceItem;
        $this->currency = $currency;
        $this->company = $company;
        $this->isCommentShown = ! is_null($this->invoiceItem->comment);

    }

    public function render()
    {
        $this->invoiceItem->load(['amount', 'volume']);
        $this->amount = $this->invoiceItem->amount;
        $this->volume = $this->invoiceItem->volume;

        $invoiceItemTypes = InvoiceItemModel::getInputTypes($this->company);

        $this->customErrors = validate_invoice_item($this->invoiceItem);

        return view('livewire.admin.direct-invoicing.invoice-item.show', ['invoiceItemTypes' => $invoiceItemTypes]);
    }

    public function updated($propertyName, $value): void
    {
        $this->validateOnly($propertyName);

        if ($propertyName == 'isCommentShown' && ! $value) {
            $this->invoiceItem->comment = null;
            $this->invoiceItem->save();
        }

        if (Str::contains($propertyName, 'invoiceItem')) {
            $this->invoiceItem->save();

            if (Str::afterLast($propertyName, '.') == 'input') {
                switch ((int) $value) {
                    case InvoiceItemModel::INPUT_TYPE_AMOUNT:
                        $this->invoiceItem->amount()->delete();
                        $this->invoiceItem->volume()->delete();

                        $this->invoiceItem->amount()->create();
                        $this->invoiceItem->with_timestamp = true;
                        $this->invoiceItem->shown_by_item = false;
                        break;

                    case InvoiceItemModel::INPUT_TYPE_MULTIPLICATION:
                        $this->invoiceItem->amount()->delete();
                        $this->invoiceItem->volume()->delete();

                        $this->invoiceItem->amount()->create();
                        $this->invoiceItem->volume()->create();
                        $this->invoiceItem->with_timestamp = true;
                        $this->invoiceItem->shown_by_item = false;
                        break;

                    case InvoiceItemModel::INPUT_TYPE_OPTUM_PSYCHOLOGY_CONSULTATIONS:
                    case InvoiceItemModel::INPUT_TYPE_OPTUM_LAW_CONSULTATIONS:
                    case InvoiceItemModel::INPUT_TYPE_OPTUM_FINANCE_CONSULTATIONS:
                    case InvoiceItemModel::INPUT_TYPE_COMPSYCH_PSYCHOLOGY_CONSULTATIONS:
                    case InvoiceItemModel::INPUT_TYPE_COMPSYCH_LAW_CONSULTATIONS:
                    case InvoiceItemModel::INPUT_TYPE_COMPSYCH_FINANCE_CONSULTATIONS:
                        $this->invoiceItem->amount()->delete();
                        $this->invoiceItem->volume()->delete();

                        $this->invoiceItem->volume()->create([
                            'name' => 'Consultations number',
                            'value' => '-',
                        ]);

                        $this->invoiceItem->amount()->create([
                            'name' => 'Unit price',
                        ]);

                        $this->invoiceItem->shown_by_item = true;
                        $this->invoiceItem->with_timestamp = false;

                        break;

                    default:
                        $this->invoiceItem->with_timestamp = false;
                        $this->invoiceItem->shown_by_item = false;
                        $this->invoiceItem->amount()->delete();
                        $this->invoiceItem->volume()->delete();
                }

                $this->invoiceItem->save();

                $this->emitUp('updateInvoiceItems');
            }
        }

        if (Str::contains($propertyName, 'amount')) {
            $this->amount->save();
        }

        if (Str::contains($propertyName, 'volume')) {
            $this->volume->save();
        }

        $this->customErrors = validate_invoice_item($this->invoiceItem);
    }

    public function delete(): void
    {
        $this->invoiceItem->amount()->delete();
        $this->invoiceItem->volume()->delete();
        $this->invoiceItem->delete();
        $this->emitUp('updateInvoiceItems');
    }

    public function updateCurrency($currency): void
    {
        $this->currency = $currency;
    }
}
