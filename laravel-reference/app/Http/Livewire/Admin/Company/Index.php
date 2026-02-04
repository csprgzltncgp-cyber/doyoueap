<?php

namespace App\Http\Livewire\Admin\Company;

use App\Models\Company;
use App\Models\Country;
use App\Traits\InvoiceHelper\SearchSortPaginateTrait;
use Livewire\Component;

class Index extends Component
{
    use SearchSortPaginateTrait;

    public $search = '';

    public $sort = 'asc';

    public function render()
    {
        $countries = Country::query()->get();
        $companies = Company::query()
            ->with('countries')
            ->when(! empty($this->search), fn ($query) => $query->where('name', 'like', "%{$this->search}%"))
            ->orderBy('name', $this->sort)
            ->get();

        $companies->map(function ($company): void {
            $company->setAttribute('is_connected', $company->clientUser->where('connected_account', '!=', null)->count() > 0);
        });

        return view('livewire.admin.company.index', ['countries' => $countries, 'companies' => $companies]);
    }
}
