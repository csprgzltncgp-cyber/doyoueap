<?php

namespace App\Http\Livewire\Expert\CurrencyChange;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithFileUploads;

class Index extends Component
{
    use WithFileUploads;

    public $document;

    public $currency_change;

    public function rules(): array
    {
        return [
            'document' => 'required|file|mimes:pdf|max:5120',
        ];
    }

    public function mount()
    {
        if (auth()->user()->expert_currency_changes()->exists()) {
            $this->currency_change = auth()->user()->expert_currency_changes;
        } else {
            return redirect()->route('expert.currency-change.index');
        }

        return null;
    }

    public function render()
    {
        return view('livewire.expert.currency-change.index');
    }

    public function download()
    {
        $html = view('expert.currency-change.document', [
            'company_name' => $this->currency_change->company_name,
            'registered_seat' => $this->currency_change->registered_seat,
            'registration_number' => $this->currency_change->registration_number,
            'tax_number' => $this->currency_change->tax_number,
            'represented_by' => $this->currency_change->represented_by,
            'hourly_rate_30_currency' => $this->currency_change->hourly_rate_30_currency,
            'hourly_rate_50_currency' => $this->currency_change->hourly_rate_50_currency,
            'hourly_rate_30' => $this->currency_change->hourly_rate_30,
            'hourly_rate_50' => $this->currency_change->hourly_rate_50,
            'date' => now()->format('Y.m.d'),
        ])->render();

        $this->currency_change->update([
            'downloaded_at' => now(),
        ]);

        return response()->streamDownload(function () use ($html): void {
            echo Pdf::loadHTML($html)->setPaper('a4', 'portrait')->output();
        }, auth()->user()->name.'.pdf');
    }

    public function updated($field, $value): void
    {
        if ($field !== 'document') {
            return;
        }

        try {
            $this->validate();
        } catch (ValidationException $e) {
            $this->emit('errorEvent', collect($e->errors())->first()[0]);

            return;
        }

        $filename = $this->document->store('currency-changes', 'private');

        $this->currency_change->update([
            'document' => $filename,
        ]);

        $this->emit('successEvent', __('currency-change.success'));
    }
}
