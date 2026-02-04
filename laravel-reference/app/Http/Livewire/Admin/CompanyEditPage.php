<?php

namespace App\Http\Livewire\Admin;

use App\Models\Company;
use App\Models\Country;
use Livewire\Component;

class CompanyEditPage extends Component
{
    public $company;

    public $name;

    public $active;

    public $countries;

    protected $rules = [
        'name' => ['required'],
        'active' => ['required'],
        'countries' => ['required'],
    ];

    public function mount(Company $company): void
    {
        $this->company = $company;
        $this->name = $company->name;
        $this->active = $company->active;
        $this->countries = $company->countries()->get()->pluck('id')->toArray();
    }

    public function render()
    {
        $this->emit('companyEditRendered');

        return view('livewire.admin.company-edit-page', [
            'all_country' => Country::all(),
        ])->extends('layout.master');
    }

    public function updatedCountries(): void
    {
        $this->company->countries()->sync($this->countries);
    }

    public function saveCompanyData(): void
    {
        $this->company->update([
            'name' => $this->name,
            'active' => $this->active,
        ]);

        $this->emit('closeAll');
    }
}
