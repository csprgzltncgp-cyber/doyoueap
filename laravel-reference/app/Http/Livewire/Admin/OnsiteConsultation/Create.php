<?php

namespace App\Http\Livewire\Admin\OnsiteConsultation;

use App\Models\Company;
use App\Models\EapOnline\EapLanguage;
use App\Models\EapOnline\OnsiteConsultationPlace;
use App\Services\OnsiteConsultationService;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

class Create extends Component
{
    public Collection $companies;

    public ?Collection $countries = null;

    public ?Collection $permissions = null;

    public ?Collection $places = null;

    public ?Collection $languages = null;

    public int $selected_company;

    public int $selected_country;

    public int $selected_permission;

    public int $selected_place;

    public string $selected_type;

    public array $selected_languages;

    public bool $allow_related_selection = false;

    protected $rules = [
        'selected_company' => ['required', 'numeric', 'exists:companies,id'],
        'selected_country' => ['required', 'numeric', 'exists:countries,id'],
        'selected_permission' => ['required', 'numeric', 'exists:permissions,id'],
        'selected_place' => ['required', 'numeric', 'exists:mysql_eap_online.onsite_consultation_places,id'],
        'selected_languages' => ['required', 'array', 'min:1', 'exists:mysql_eap_online.languages,id'],
        'selected_type' => ['required'],
    ];

    public function mount(): void
    {
        $this->companies = Company::query()->where('active', 1)->orderBy('name')->get();
        $this->places = OnsiteConsultationPlace::query()->get();
        $this->languages = EapLanguage::query()->get();
    }

    public function render()
    {
        return view('livewire.admin.onsite-consultation.create')->extends('layout.master');
    }

    public function updatedSelectedCompany(int $company_id): void
    {
        $company = Company::query()->where('id', $company_id)->first();

        $this->countries = $company->countries;
        $this->permissions = $company->permissions;

        $this->allow_related_selection = true;
    }

    public function save(OnsiteConsultationService $onsite_consultation_service): void
    {
        $this->validate();

        $onsite_consultation_service->store_consultation([
            'company_id' => $this->selected_company,
            'country_id' => $this->selected_country,
            'permission_id' => $this->selected_permission,
            'onsite_consultation_place_id' => $this->selected_place,
            'type' => $this->selected_type,
        ], $this->selected_languages);

        $this->emit('save_succesfull');
    }
}
