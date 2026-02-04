<?php

namespace App\Http\Livewire\Admin\Company;

use App\Enums\ContractHolderEnum;
use App\Enums\UserTypeEnum;
use App\Models\ActivityPlan;
use App\Models\Company;
use App\Models\ContractDateReminderEmail;
use App\Models\ContractHolder;
use App\Models\Country;
use App\Models\CountryDifferentiate;
use App\Models\EapOnline\EapMenuItem;
use App\Models\User;
use App\Scopes\CountryScope;
use App\Scopes\LanguageScope;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class Create extends Component
{
    public $company;

    public $countries;

    public $clientUser;

    public $countryDifferentiates;

    // ORG data related
    public $contractHolder;

    public $orgId;

    public $contractDate;

    public $contractDateEnd;

    public $contractDateReminderEmail;

    public $activityPlanUser;

    protected $rules = [
        'company.name' => ['required'],
        'company.active' => ['required'],
        'countries' => ['required', 'array', 'min:1'],
        'countryDifferentiates.contract_holder' => ['required', 'boolean'],
        'countryDifferentiates.org_id' => ['required', 'boolean'],
        'countryDifferentiates.contract_date' => ['required', 'boolean'],
        'countryDifferentiates.reporting' => ['required', 'boolean'],
        'countryDifferentiates.invoicing' => ['required', 'boolean'],
        'countryDifferentiates.contract_date_reminder_email' => ['required', 'boolean'],
        'clientUser.username' => ['sometimes', 'required'],
        'clientUser.password' => ['sometimes', 'required'],
        'clientUser.language_id' => ['sometimes', 'required'],
        'activityPlanUser' => ['nullable', 'exists:users,id'],
    ];

    public function mount(): void
    {
        $this->company = new Company;
        $this->company->active = true;
        $this->countries = [];
        $this->countryDifferentiates = new CountryDifferentiate([
            'contract_holder' => false,
            'org_id' => false,
            'contract_date' => false,
            'reporting' => false,
            'invoicing' => false,
            'contract_date_reminder_email' => false,
        ]);
    }

    public function render()
    {
        $allCountries = Country::query()->withoutGlobalScopes([CountryScope::class, LanguageScope::class])->orderBy('name')->get();
        $contractHolders = ContractHolder::all();
        $account_admins = User::query()->where('type', UserTypeEnum::ACCOUNT_ADMIN->value)->orderBy('name')->get();

        return view('livewire.admin.company.create', ['allCountries' => $allCountries, 'contractHolders' => $contractHolders, 'account_admins' => $account_admins])->extends('layout.master');
    }

    public function updatedCountryDifferentiates($value, string $propertyName): void
    {
        $this->validateOnly('countryDifferentiates.'.$propertyName);

        if ($propertyName === 'contract_holder' && $value) {
            $this->contractHolder = null;
        }

        if ($propertyName === 'org_id' && $value) {
            $this->orgId = null;
        }

        if ($propertyName === 'contract_date' && $value) {
            $this->contractDate = null;
            $this->contractDateEnd = null;
        }

        if ($propertyName === 'reporting' && $value) {
            $this->clientUser = null;
        }
        if ($propertyName !== 'contract_date_reminder_email') {
            return;
        }
        if (! $value) {
            return;
        }
        $this->contractDateReminderEmail = null;
    }

    public function store()
    {
        try {
            $this->validate();
        } catch (ValidationException $e) {
            $this->emit('errorEvent', collect($e->errors())->first()[0]);

            return null;
        }

        // IF contract holder is CGP set customer_satisfaction_index to true
        if ($this->contractHolder == ContractHolderEnum::CGP->value) {
            $this->company->customer_satisfaction_index = true;
        }

        $this->company->save();

        // Save country differentiates
        $this->countryDifferentiates->company_id = $this->company->id;
        $this->countryDifferentiates->save();

        // Save countries
        $this->company->countries()->sync($this->countries);

        // Save org data
        foreach ($this->countries as $countryId) {
            $attributes = [
                'country_id' => $countryId,
            ];

            if (! $this->countryDifferentiates->contract_holder) {
                $attributes['contract_holder_id'] = $this->contractHolder;
            }

            if (! $this->countryDifferentiates->org_id) {
                $attributes['org_id'] = $this->orgId;
            }

            if (! $this->countryDifferentiates->contract_date) {
                $attributes['contract_date'] = $this->contractDate;
                $attributes['contract_date_end'] = $this->contractDateEnd;
            }

            $this->company->org_datas()->create($attributes);
        }

        // contaract date reminder email
        if (! empty($this->contractDateReminderEmail) && ! $this->countryDifferentiates->contract_date_reminder_email) {
            ContractDateReminderEmail::query()->create([
                'company_id' => $this->company->id,
                'country_id' => null,
                'value' => $this->contractDateReminderEmail,
            ]);
        }

        // client user
        if (! empty($this->clientUser) && ! $this->countryDifferentiates->reporting) {
            $data = [
                'all_country' => true,
                'country_id' => $this->company->countries()->first()->id,
                'email' => strtolower((string) $this->company->countries()->first()->code).'@cgpeu.com',
                'language_id' => array_key_exists('language_id', $this->clientUser) ? $this->clientUser['language_id'] : 1,
                'username' => array_key_exists('username', $this->clientUser) ? $this->clientUser['username'] : optional($this->company)->name,
                'password' => array_key_exists('password', $this->clientUser) ? Hash::make($this->clientUser['password']) : Hash::make('password'),
                'type' => 'client',
            ];

            $user = User::query()->make();
            $user->fill($data);
            $user->save();

            $this->company->clientUsers()->sync($user);
        }

        // eap online menu visibilities
        if ((int) $this->contractHolder != 1) {
            EapMenuItem::query()
                ->whereNotIn('id', [9, 11]) // 9 - Old articles, 11 - prizegame
                ->get()->each(function (EapMenuItem $item): void {
                    DB::connection('mysql_eap_online')->table('company_menu_item')->insert([
                        'company_id' => $this->company->id,
                        'menu_item_id' => $item->id,
                    ]);
                });
        }

        // activity plan
        if (! empty($this->activityPlanUser)) {
            ActivityPlan::query()->where('company_id', $this->company->id)->firstOrCreate([
                'company_id' => $this->company->id,
                'user_id' => $this->activityPlanUser,
            ]);
        }

        return redirect()->route(auth()->user()->type.'.companies.edit', ['company' => $this->company->id]);
    }
}
