<?php

namespace App\Http\Livewire\Expert\CurrencyChange;

use App\Helpers\CurrencyCached;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class Create extends Component
{
    public string $companyName = '';

    public string $registeredSeat = '';

    public string $registrationNumber = '';

    public string $taxNumber = '';

    public string $representedBy = '';

    public User $expert;

    public array $converted_prices = [];

    public string $hourly_rate_30 = '';

    public string $hourly_rate_50 = '';

    public function rules(): array
    {
        $rules = [
            'companyName' => ['required', 'string', 'max:255'],
            'registeredSeat' => ['required', 'string', 'max:255'],
            'registrationNumber' => ['required', 'string', 'max:255'],
            'taxNumber' => ['required', 'string', 'max:255'],
            'representedBy' => ['required', 'string', 'max:255'],
        ];

        if (! empty($this->expert->invoice_datas->hourly_rate_30) && ! empty($this->expert->invoice_datas->currency)) {
            $rules['hourly_rate_30'] = ['required', 'string', 'in:USD,EUR'];
        }

        if (! empty($this->expert->invoice_datas->hourly_rate_50) && ! empty($this->expert->invoice_datas->currency)) {
            $rules['hourly_rate_50'] = ['required', 'string', 'in:USD,EUR'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'companyName.required' => __('currency-change.error-message-input'),
            'companyName.string' => __('currency-change.error-message-input'),
            'companyName.max' => __('currency-change.error-message-input'),
            'registeredSeat.required' => __('currency-change.error-message-input'),
            'registeredSeat.string' => __('currency-change.error-message-input'),
            'registeredSeat.max' => __('currency-change.error-message-input'),
            'registrationNumber.required' => __('currency-change.error-message-input'),
            'registrationNumber.string' => __('currency-change.error-message-input'),
            'registrationNumber.max' => __('currency-change.error-message-input'),
            'taxNumber.required' => __('currency-change.error-message-input'),
            'taxNumber.string' => __('currency-change.error-message-input'),
            'taxNumber.max' => __('currency-change.error-message-input'),
            'representedBy.required' => __('currency-change.error-message-input'),
            'representedBy.string' => __('currency-change.error-message-input'),
            'representedBy.max' => __('currency-change.error-message-input'),
            'hourly_rate_30.required' => __('currency-change.error-message-radio'),
            'hourly_rate_30.string' => __('currency-change.error-message-radio'),
            'hourly_rate_30.in' => __('currency-change.error-message-radio'),
            'hourly_rate_50.required' => __('currency-change.error-message-radio'),
            'hourly_rate_50.string' => __('currency-change.error-message-radio'),
            'hourly_rate_50.in' => __('currency-change.error-message-radio'),
        ];
    }

    public function mount(): void
    {
        $this->expert = auth()->user();
        $this->expert->load('invoice_datas');

        $converter = new CurrencyCached;

        $converted_prices = [];

        if (! empty($this->expert->invoice_datas->hourly_rate_30) && ! empty($this->expert->invoice_datas->currency)) {
            $converted_prices['hourly_rate_30_usd'] = round($converter->convert((int) str_replace(' ', '', (string) $this->expert->invoice_datas->hourly_rate_30), 'USD', strtoupper((string) $this->expert->invoice_datas->currency)));
            $converted_prices['hourly_rate_30_eur'] = round($converter->convert((int) str_replace(' ', '', (string) $this->expert->invoice_datas->hourly_rate_30), 'EUR', strtoupper((string) $this->expert->invoice_datas->currency)));
        }

        if (! empty($this->expert->invoice_datas->hourly_rate_50) && ! empty($this->expert->invoice_datas->currency)) {
            $converted_prices['hourly_rate_50_usd'] = round($converter->convert((int) str_replace(' ', '', (string) $this->expert->invoice_datas->hourly_rate_50), 'USD', strtoupper((string) $this->expert->invoice_datas->currency)));
            $converted_prices['hourly_rate_50_eur'] = round($converter->convert((int) str_replace(' ', '', (string) $this->expert->invoice_datas->hourly_rate_50), 'EUR', strtoupper((string) $this->expert->invoice_datas->currency)));
        }

        $this->converted_prices = $converted_prices;
    }

    public function render()
    {
        return view('livewire.expert.currency-change.create', [
            'current_date' => Carbon::now()->format('Y.m.d'),
        ]);
    }

    public function updated($field, string $value): void
    {
        if (! empty($this->expert->invoice_datas->hourly_rate_30) && ! empty($this->expert->invoice_datas->currency) && $field === 'hourly_rate_50') {
            $this->hourly_rate_30 = $value;
        }
        if (empty($this->expert->invoice_datas->hourly_rate_50)) {
            return;
        }
        if (empty($this->expert->invoice_datas->currency)) {
            return;
        }
        if ($field === 'hourly_rate_30') {
            $this->hourly_rate_50 = $value;
        }
    }

    public function save_data()
    {
        try {
            $this->validate();
        } catch (ValidationException $e) {
            $this->emit('errorEvent', collect($e->errors())->first()[0]);

            return null;
        }

        $data = [
            'company_name' => $this->companyName,
            'registered_seat' => $this->registeredSeat,
            'registration_number' => $this->registrationNumber,
            'tax_number' => $this->taxNumber,
            'represented_by' => $this->representedBy,
        ];

        if (! empty($this->expert->invoice_datas->hourly_rate_30) && ! empty($this->expert->invoice_datas->currency)) {
            $data['hourly_rate_30'] = $this->converted_prices['hourly_rate_30_'.strtolower($this->hourly_rate_30)];
            $data['hourly_rate_30_currency'] = strtolower($this->hourly_rate_30);

            $this->expert->invoice_datas->update([
                'hourly_rate_30' => $data['hourly_rate_30'],
                'currency' => $data['hourly_rate_30_currency'],
            ]);
        }

        if (! empty($this->expert->invoice_datas->hourly_rate_50) && ! empty($this->expert->invoice_datas->currency)) {
            $data['hourly_rate_50'] = $this->converted_prices['hourly_rate_50_'.strtolower($this->hourly_rate_50)];
            $data['hourly_rate_50_currency'] = strtolower($this->hourly_rate_50);

            $this->expert->invoice_datas->update([
                'hourly_rate_50' => $data['hourly_rate_50'],
                'currency' => $data['hourly_rate_50_currency'],
            ]);
        }

        $this->expert->expert_currency_changes()->create($data);

        return redirect()->route('expert.currency-change.index');
    }
}
