<?php

namespace App\Http\Livewire\Admin\InvoiceHelper\CompanyProfiles;

use App\Models\Company;
use App\Traits\InvoiceHelper\SearchSortPaginateTrait;
use Livewire\Component;

class Index extends Component
{
    use SearchSortPaginateTrait;

    public $perPage = 5;

    public $opened_companies = [];

    public $search = '';

    public $sort = 'asc';

    public $js_companies;

    protected $listeners = [
        'openDate' => 'toggleOpenSelf',
    ];

    public function render()
    {
        $companies = Company::query()
            ->when(! empty($this->search), fn ($query) => $query->where('name', 'like', "%{$this->search}%"))
            ->whereHas('org_datas', function ($query): void {
                $query->where('contract_holder_id', 2);
            })->orderBy('name', $this->sort)
            ->paginate($this->perPage);

        $this->js_companies = data_get($companies->toArray(), 'data');

        return view('livewire.admin.invoice-helper.company-profiles.index', ['companies' => $companies]);
    }
}
