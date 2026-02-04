<?php

namespace App\Http\Livewire\Admin\InvoiceHelper\CompletionCertificate;

use App\Models\Company;
use App\Traits\InvoiceHelper\CompletionCertificateTrait;
use App\Traits\InvoiceHelper\SearchSortPaginateTrait;
use Illuminate\Support\Carbon;
use Livewire\Component;

class Month extends Component
{
    use CompletionCertificateTrait;
    use SearchSortPaginateTrait;

    public $date;

    public $perPage = 10;

    public $opened = false;

    public $opened_companies = [];

    public $opened_countries = [];

    public $search = '';

    public $sort = 'asc';

    protected $listeners = [
        'openDate' => 'toggleOpenSelf',
    ];

    public function mount($date): void
    {
        $this->date = Carbon::parse($date)->format('Y-m-d');
    }

    public function render()
    {
        $companies = Company::query()
            ->with(['country_differentiates', 'countries'])
            ->whereHas('direct_invoices', fn ($query) => $query
                ->whereDate('to', Carbon::parse($this->date)->endOfMonth()->format('Y-m-d'))
                ->has('completion_certificate'))
            ->when(! empty($this->search), fn ($query) => $query->where('name', 'like', "%{$this->search}%"))
            ->with(['direct_invoices' => fn ($query) => $query
                ->whereDate('to', Carbon::parse($this->date)->endOfMonth()->format('Y-m-d'))
                ->whereBetween('created_at', [Carbon::now()->startOfDay(), Carbon::now()->endOfDay()])
                ->has('completion_certificate'), 'direct_invoices.envelope'])
            ->orderBy('name', $this->sort)
            ->paginate($this->perPage);

        return view('livewire.admin.invoice-helper.completion-certificate.month', ['companies' => $companies]);
    }
}
