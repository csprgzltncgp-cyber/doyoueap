<?php

namespace App\Http\Livewire\Admin\InvoiceHelper\DirectInvoicing;

use App\Models\Company;
use App\Models\Scopes\ContractHolderCompanyScope;
use App\Traits\InvoiceHelper\SearchSortPaginateTrait;
use Illuminate\Support\Carbon;
use Livewire\Component;

class Month extends Component
{
    use SearchSortPaginateTrait;

    public $date;

    public $contractHolderCompany;

    public $perPage = 10;

    public $opened = false;

    public $opened_companies = [];

    public $opened_countries = [];

    public $opened_direct_invoices = [];

    public $search = '';

    public $sort = 'asc';

    protected $listeners = [
        'openDate' => 'toggleOpenSelf',
    ];

    public function mount($date, $contractHolderCompany = null): void
    {
        $this->date = Carbon::parse($date)->format('Y-m-d');
        $this->contractHolderCompany = $contractHolderCompany;
    }

    public function render()
    {
        $companies = Company::query()
            ->with([
                'country_differentiates',
                'countries',
                'direct_invoices',
            ])
            ->whereHas('direct_invoices', fn ($query) => $query->whereDate('to', Carbon::parse($this->date)->endOfMonth()->format('Y-m-d'))
                ->orWhereDate('to', Carbon::parse($this->date)->endOfMonth()->subDay()->format('Y-m-d')))
            ->with(['direct_invoices' => fn ($query) => $query->whereDate('to', Carbon::parse($this->date)->endOfMonth()->format('Y-m-d'))
                ->orWhereDate('to', Carbon::parse($this->date)->endOfMonth()->subDay()->format('Y-m-d'))], 'direct_invoice_datas')
            ->when(! empty($this->search), fn ($query) => $query->where('name', 'like', "%{$this->search}%"))
            ->when(! is_null($this->contractHolderCompany), fn ($query) => $query->withoutGlobalScope(ContractHolderCompanyScope::class)->where('id', (int) $this->contractHolderCompany))
            ->orderBy('name', $this->sort)
            ->paginate($this->perPage);

        return view('livewire.admin.invoice-helper.direct-invoicing.month', ['companies' => $companies]);
    }
}
