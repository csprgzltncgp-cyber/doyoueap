<?php

namespace App\Http\Livewire\Admin;

use App\Models\CaseInput;
use App\Models\Company;
use Livewire\Component;

class CompanyInputEditPage extends Component
{
    public $inputs;

    public $company;

    protected $listeners = ['refreshCompanyInputs' => 'refreshCompanyInputs'];

    public function mount(Company $company): void
    {
        $this->company = $company;
        $this->inputs = $company->caseInputs;
    }

    public function render()
    {
        return view('livewire.admin.company-input-edit-page')->extends('layout.master');
    }

    public function addNew(): void
    {
        CaseInput::query()->create();
        $this->refreshCompanyInputs();
    }

    public function save(): void
    {
        $this->emit('companyInputsSaved');
    }

    public function refreshCompanyInputs(): void
    {
        $this->inputs = $this->company->caseInputs()->get();
    }
}
