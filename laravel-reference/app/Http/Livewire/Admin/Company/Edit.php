<?php

namespace App\Http\Livewire\Admin\Company;

use App\Enums\UserTypeEnum;
use App\Models\ActivityPlan;
use App\Models\Company;
use App\Models\ContractDateReminderEmail;
use App\Models\ContractHolder;
use App\Models\Country;
use App\Models\User;
use App\Scopes\CountryScope;
use App\Scopes\LanguageScope;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Component;

class Edit extends Component
{
    public $company;

    public $countries;

    public $orgDatas;

    public $clientUser;

    public $countryDifferentiates;

    public $connectCompanies;

    public $companyConnected;

    public $disabled_breadcrumb;

    // ORG data related
    public $contractHolder;

    public $orgId;

    public $contractDate;

    public $contractDateEnd;

    public $contractDateReminderEmail;

    public $activity_plan_user;

    public ActivityPlan $activity_plan;

    protected $rules = [
        'company.name' => ['required'],
        'company.active' => ['required'],
        'countries' => ['required'],
        'countryDifferentiates.contract_holder' => ['required', 'boolean'],
        'countryDifferentiates.org_id' => ['required', 'boolean'],
        'countryDifferentiates.contract_date' => ['required', 'boolean'],
        'countryDifferentiates.reporting' => ['required', 'boolean'],
        'countryDifferentiates.invoicing' => ['required', 'boolean'],
        'countryDifferentiates.contract_date_reminder_email' => ['required', 'boolean'],
        'clientUser.username' => ['sometimes', 'required'],
        'clientUser.password' => ['sometimes', 'required'],
        'clientUser.language_id' => ['sometimes', 'required'],
        'contractDateReminderEmail' => ['email'],
    ];

    public function mount(Company $company, $disabled_breadcrumb = false): void
    {
        $this->disabled_breadcrumb = $disabled_breadcrumb;

        $this->activity_plan = ActivityPlan::query()->where('company_id', $company->id)->firstOrCreate([
            'company_id' => $company->id,
        ]);

        $this->activity_plan_user = optional($this->activity_plan->user)->id;

        $this->company = $company;
        $this->countries = $company->countries()->get()->pluck('id')->toArray();
        $this->orgDatas = $company->org_datas()->get();
        $this->countryDifferentiates = $company->country_differentiates()->firstOrCreate();
        $this->contractHolder = $this->countryDifferentiates->contract_holder ? null : optional($this->company->org_datas()->first())->contract_holder_id;
        $this->orgId = $this->countryDifferentiates->org_id ? null : optional($this->company->org_datas()->first())->org_id;
        $this->contractDate = $this->countryDifferentiates->contract_date ? null : optional($this->company->org_datas()->first())->contract_date;
        $this->contractDateEnd = $this->countryDifferentiates->contract_date ? null : optional($this->company->org_datas()->first())->contract_date_end;
        $this->clientUser = $this->countryDifferentiates->reporting ? null : $this->company->clientUsers->first();
        $this->contractDateReminderEmail = $this->countryDifferentiates->contract_date_reminder_email ? null : optional($this->company->contract_date_reminder_emails()->whereNull('country_id')->first())->value;
        $this->companyConnected = $this->clientUser && $this->clientUser->connected_account != '';
        $this->connectCompanies = Company::with('clientUsers')
            ->whereHas('org_datas', function ($query): void {
                $query->where('contract_holder_id', 2); // (2) contract holder is CGP
            })
            ->where('id', '!=', $this->company->id)
            ->get()->filter(function ($company): bool {
                if (! $company->clientuser->first()) {
                    return true;
                }

                return is_null($company->clientuser->first()->connected_account);
            })->sortBy('name');
    }

    public function render()
    {
        $allCountries = Country::query()->withoutGlobalScopes([CountryScope::class, LanguageScope::class])->orderBy('name')->get();
        $contractHolders = ContractHolder::all();
        $account_admins = User::query()->where('type', UserTypeEnum::ACCOUNT_ADMIN->value)->orderBy('name')->get();

        return view('livewire.admin.company.edit', ['allCountries' => $allCountries, 'contractHolders' => $contractHolders, 'account_admins' => $account_admins])->extends('layout.master');
    }

    public function updatedActivityPlanUser($value): void
    {
        $this->activity_plan->user_id = $value;
        $this->activity_plan->save();
    }

    public function updatedCountries(): void
    {
        $existing_countries = $this->company->countries()->get()->pluck('id')->toArray();
        $country_to_add = array_diff($this->countries, $existing_countries);

        if ($country_to_add !== []) {
            $attrutes = [
                'country_id' => collect($country_to_add)->first(),
            ];

            if (! $this->countryDifferentiates->contract_holder) {
                $attrutes['contract_holder_id'] = optional($this->company->org_datas()->first())->contract_holder_id;
            }

            if (! $this->countryDifferentiates->org_id) {
                $attrutes['org_id'] = optional($this->company->org_datas()->first())->org_id;
            }

            if (! $this->countryDifferentiates->contract_date) {
                $attrutes['contract_date'] = optional($this->company->org_datas()->first())->contract_date;
                $attrutes['contract_date_end'] = optional($this->company->org_datas()->first())->contract_date_end;
            }

            $this->company->org_datas()->create($attrutes);
        } else {
            $country_to_remove = array_diff($existing_countries, $this->countries);
            $this->company->org_datas()->where('country_id', collect($country_to_remove)->first())->delete();
        }

        $this->company->countries()->sync($this->countries);

        $this->countries = $this->company->countries()->get()->pluck('id')->toArray();
        $this->company->refresh();
    }

    public function updatedCountryDifferentiates($value, string $propertyName): void
    {
        $this->validateOnly('countryDifferentiates.'.$propertyName);

        if ($propertyName === 'contract_holder' && $value) {
            $this->company->org_datas()->update(['contract_holder_id' => null]);
            $this->contractHolder = null;
        }

        if ($propertyName === 'org_id' && $value) {
            $this->company->org_datas()->update(['org_id' => null]);
            $this->orgId = null;
        }

        if ($propertyName === 'contract_date') {
            $this->company->org_datas()->update(['contract_date' => null, 'contract_date_end' => null]);
            $this->contractDate = null;
            $this->contractDateEnd = null;
            $this->emitTo('admin.company-country-component', 'setContractDateToNull');
        }

        if ($propertyName === 'reporting') {
            $this->clientUser = null;

            $client_user_ids = $this->company->clientUsers()->pluck('users.id')->toArray();
            $this->company->clientUsers()->detach();

            foreach ($client_user_ids as $client_user_id) {
                $user = User::query()->where('id', $client_user_id)->first();
                if ($user) {
                    $user->delete();
                }
            }

            $this->emitTo('admin.company-country-component', 'setClientUserToNull');
        }

        if ($propertyName === 'contract_date_reminder_email') {
            $this->company->contract_date_reminder_emails()->delete();
            $this->contractDateReminderEmail = null;
            $this->emitTo('admin.company-country-component', 'setContractDateReminderEmailToNull');
        }

        if ($propertyName === 'invoicing') {
            $this->emitTo('admin.direct-invoicing.container', 'updatedCountryDifferentiates', $this->countryDifferentiates);
        }

        $this->countryDifferentiates->save();

        $this->emitTo('admin.company-country-component', '$refresh');
    }

    public function updatedCompany($value, string $propertyName): void
    {
        $this->validateOnly('company.'.$propertyName);

        if ($propertyName === 'active') {
            $this->company->active = filter_var($value, FILTER_VALIDATE_BOOLEAN);
        }

        $this->company->save();
    }

    public function updatedContractHolder($value): void
    {
        $this->company->org_datas()->update(['contract_holder_id' => $value]);
        $this->company->country_differentiates()->update([
            'org_id' => false,
            'reporting' => false,
            'contract_date' => false,
        ]);

        $this->countryDifferentiates->refresh();

        $this->emitTo('admin.company.direct-invoice-data', '$refresh');
    }

    public function updatedOrgId($value): void
    {
        $this->company->org_datas()->update(['org_id' => $value]);
        $this->emitTo('admin.company.direct-invoice-data', '$refresh');
    }

    public function updatedContractDate($value): void
    {
        $this->company->org_datas()->update(['contract_date' => $value]);
        $this->emitTo('admin.company.direct-invoice-data', '$refresh');
    }

    public function updatedContractDateEnd($value): void
    {
        $this->company->org_datas()->update(['contract_date_end' => $value]);
        $this->emitTo('admin.company.direct-invoice-data', '$refresh');
    }

    public function updatedClientUser($value, string $propertyName): void
    {
        $this->validateOnly('clientUser.'.$propertyName);

        $user = $this->company->clientUsers()->first();

        if ($user) {
            $user->update([
                $propertyName => ($propertyName === 'password') ? Hash::make($value) : $value,
                'all_country' => true,
            ]);
        } else {
            $data = [
                $propertyName => ($propertyName === 'password') ? Hash::make($value) : $value,
                'all_country' => true,
                'country_id' => $this->company->countries()->first()->id,
                'email' => strtolower((string) $this->company->countries()->first()->code).'@cgpeu.com',
                'language_id' => 1,
                'type' => 'client',
            ];

            $user = User::query()->make();
            $user->fill($data);
            $user->save();
        }

        if ($propertyName === 'language_id') {
            $quarters = [1, 2, 3, 4, null]; // 4 quarters and null as possible value

            $countries = $this->company->countries;

            foreach ($quarters as $quarter) {
                Cache::forget('riport-'.$this->company->id.'--total');
                Cache::forget('riport-'.$this->company->id.'-'.$quarter.'-total');

                foreach ($countries as $country) {
                    Cache::forget('riport-'.$quarter.'-'.$country->id.'-'.$this->company->id);
                }
            }
        }

        $this->company->clientUsers()->sync($user);
    }

    public function updatedContractDateReminderEmail($value): void
    {
        $this->validateOnly('contractDateReminderEmail');

        $contract_date_reminder_email = $this->company->contract_date_reminder_emails()->where('country_id', null)->first();

        if ($contract_date_reminder_email) {
            $contract_date_reminder_email->update([
                'value' => $value,
            ]);
        } else {
            $data = [
                'value' => $value,
                'company_id' => $this->company->id,
                'country_id' => null,
            ];

            $contract_date_reminder_email = ContractDateReminderEmail::query()->make();
            $contract_date_reminder_email->fill($data);
            $contract_date_reminder_email->save();
        }
    }

    public function update_connected_company($connected_company_id): void
    {
        $clientUser = $this->company->clientUsers->first();

        if ($connected_company_id != '') {
            $connected_company = Company::with('clientUser')->where('id', $connected_company_id)->first();

            if ($connected_company->clientUser->first()) {
                $clientUser = $this->company->clientUser->first();
                $clientUser->connected_account = $connected_company->clientUser->first()->id;
            } else {
                $name = preg_replace(
                    '/[^A-Za-z0-9\-]/',
                    '',
                    Str::lower($connected_company->name)
                );

                $language_id = $this->company->clientUser->first()->language_id;

                /** @var Country $country */
                $country = $connected_company->countries()->first();

                $data = [
                    'name' => $name,
                    'all_country' => true,
                    'country_id' => $country->id,
                    'email' => strtolower((string) $country->code).'@cgpeu.com',
                    'language_id' => $language_id,
                    'type' => 'client',
                ];

                $user = User::query()->make();
                $user->fill($data);
                $user->save();
                $connected_company->clientUsers()->sync($user);

                $connected_company = Company::with('clientUser')->where('id', $connected_company_id)->first();
                $this->company->refresh();
                $clientUser = $this->company->clientUsers->first();
                $clientUser->connected_account = $connected_company->clientUser->first()->id;
            }
            $this->companyConnected = true;
        } else {
            $clientUser->connected_account = null;
            $this->companyConnected = false;
        }

        $clientUser->save();
    }

    public function update(): void
    {
        Cache::forget('missing-company-information-'.$this->company->id);

        foreach ($this->company->countries()->get()->pluck('id')->toArray() as $country_id) {
            Cache::forget('missing-company-information-'.$this->company->id.'-'.$country_id);
        }

        $this->emit('companyUpdated');
    }

    public function emitNewPasswordEvent(): void
    {
        $this->emit('setNewPassword');
    }
}
