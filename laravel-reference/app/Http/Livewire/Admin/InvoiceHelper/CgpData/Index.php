<?php

namespace App\Http\Livewire\Admin\InvoiceHelper\CgpData;

use App\Models\CgpData;
use Illuminate\Support\Str;
use Livewire\Component;

class Index extends Component
{
    public CgpData $data;

    public $accountNumbers;

    protected $rules = [
        'data.company_name' => 'required',
        'data.country' => 'required',
        'data.post_code' => 'required',
        'data.city' => 'required',
        'data.street' => 'required',
        'data.house_number' => 'required',
        'data.vat_number' => 'required',
        'data.eu_vat_number' => 'required',
        'data.swift' => 'required',
        'data.email' => 'required',
        'data.website' => 'required',
        'accountNumbers.*.account_number' => 'required',
        'accountNumbers.*.currency' => 'required',
        'accountNumbers.*.iban' => 'required',
    ];

    public function mount(): void
    {
        $this->data = CgpData::query()->with('account_numbers')->firstOrCreate(['id' => 1]);
        $this->accountNumbers = $this->data->account_numbers;

        if ($this->accountNumbers->count() == 0) {
            $this->addNewAccountNumber();
        }
    }

    public function render()
    {
        return view('livewire.admin.invoice-helper.cgp-data.index');
    }

    public function updated($name, $value): void
    {
        $this->validateOnly($name);

        if (Str::startsWith($name, 'data.')) {
            $this->data->save();
        }

        if (Str::startsWith($name, 'accountNumbers.')) {
            $this->data->account_numbers()->saveMany($this->accountNumbers);
        }
    }

    public function addNewAccountNumber(): void
    {
        $this->accountNumbers[] = $this->data->account_numbers()->create([]);
    }

    public function deleteAccountNumber($index): void
    {

        $this->accountNumbers[$index]->delete();
        unset($this->accountNumbers[$index]);
    }

    public function update(): void
    {
        $this->emit('cgpDataUpdated');
    }
}
