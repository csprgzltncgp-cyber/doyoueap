<?php

namespace App\Http\Livewire\Admin\OtherActivity;

use App\Enums\OtherActivityType;
use App\Models\City;
use App\Models\Company;
use App\Models\Country;
use App\Models\InvoiceData;
use App\Models\OtherActivity;
use App\Models\Permission;
use App\Models\User;
use App\Traits\ActivityIdPrefixTrait;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class CreatePage extends Component
{
    use ActivityIdPrefixTrait;

    public $step = 0;

    public $max_step = 19;

    public $is_free_for_company = 0;

    public $is_free_for_user = 0;

    public $title;

    public $otherActivity;

    public $selected_country;

    protected function rules(): array
    {
        return [
            'otherActivity.type' => ['required', 'integer'],
            'otherActivity.permission_id' => ['required', 'integer', 'exists:permissions,id'],
            'otherActivity.company_id' => ['required', 'integer', 'exists:companies,id'],
            'otherActivity.contract_holder_id' => ['required', 'integer', 'exists:contract_holders,id'],
            'otherActivity.user_id' => ['nullable', 'integer', 'exists:users,id'],
            'otherActivity.country_id' => ['nullable', 'integer', 'exists:countries,id'],
            'otherActivity.city_id' => ['nullable', 'integer', 'exists:cities,id'],
            'otherActivity.activity_id' => ['required', 'string'],
            'otherActivity.company_price' => ['nullable', 'integer'],
            'otherActivity.company_currency' => ['nullable', 'string'],
            'otherActivity.company_email' => ['nullable', 'email'],
            'otherActivity.company_phone' => ['nullable', 'string'],
            'otherActivity.user_price' => ['nullable', 'integer'],
            'otherActivity.user_currency' => ['nullable', 'string'],
            'otherActivity.user_phone' => ['nullable', 'string'],
            'otherActivity.language' => ['nullable', 'string'],
            'otherActivity.participants' => ['nullable', 'integer', 'min:0'],
            'otherActivity.date' => ['nullable', 'string'],
            'otherActivity.start_time' => ['nullable', 'string'],
            'otherActivity.end_time' => ['nullable', 'string'],
        ];
    }

    public function mount(): void
    {
        $this->otherActivity = new OtherActivity;
        $this->otherActivity->type = null;
        $this->otherActivity->permission_id = -1;
        $this->otherActivity->country_id = -1;
        $this->otherActivity->company_id = -1;
        $this->otherActivity->city_id = -1;
        $this->otherActivity->user_id = -1;
        $this->title = $this->getTitle();
    }

    public function render()
    {
        $currencies = [
            'chf' => 'CHF',
            'czk' => 'CZK',
            'eur' => 'EUR',
            'huf' => 'HUF',
            'mdl' => 'MDL',
            'oal' => 'OAL',
            'pln' => 'PLN',
            'ron' => 'RON',
            'rsd' => 'RSD',
            'usd' => 'USD',
        ];

        $invoiceData = InvoiceData::query()->where('user_id', $this->otherActivity->user_id)->first();

        if (! empty($invoiceData->currency)) {
            $currencies = array_filter($currencies, fn ($key): bool => $key === $invoiceData->currency, ARRAY_FILTER_USE_KEY);
        }

        $permissions = Permission::query()->get();

        return view('livewire.admin.other-activity.create-page', [
            'users' => User::query()->where(['type' => 'expert'])->whereHas('outsource_countries', fn ($query) => $query->where('country_id', $this->selected_country))->orderBy('name')->get(),
            'countries' => Country::query()->has('companies')->has('cities')->orderBy('name')->get(),
            'cities' => City::query()->orderBy('name')->get(),
            'companies' => Company::query()->whereHas('countries', fn ($query) => $query->where('id', $this->otherActivity->country_id))->orderBy('name')->get(),
            'currencies' => $currencies,
            'permissions' => $permissions,
        ])->extends('layout.master');
    }

    public function updated($propertyName, $value): void
    {
        if (Str::contains($propertyName, 'country_id') && ! empty($value)) {
            $this->otherActivity->city_id = -1;
            $this->otherActivity->user_id = -1;
            $this->otherActivity->company_id = -1;
            $this->selected_country = $value;
        }

        if (Str::contains($propertyName, 'company_id') && ! empty($value)) {
            $contract_holder = optional($this->otherActivity->company->org_datas()->where('country_id', $this->otherActivity->country_id)->first())->contract_holder_id;

            $this->otherActivity->contract_holder_id = $contract_holder ?? 2;
            $id = optional(OtherActivity::query()->latest('id')->first())->id + 1;

            // Set activity id first character based on the activity type
            $prefix = match ($this->otherActivity->type) {
                OtherActivityType::TYPE_ORIENTATION => '#o',
                OtherActivityType::TYPE_HEALTH_DAY => '#h',
                OtherActivityType::TYPE_EXPERT_OUTPLACEMENT => '#e',
                default => '#o',
            };

            $this->otherActivity->activity_id = $prefix.$this->getActivityIdPref($contract_holder ?? 2).$id;
        }

        if (Str::contains($propertyName, 'is_free_for_user') && $value) {
            $this->otherActivity->user_price = null;
            $this->otherActivity->user_currency = null;
        }

        if (Str::contains($propertyName, 'is_free_for_company') && $value) {
            $this->otherActivity->company_price = null;
            $this->otherActivity->company_currency = null;
        }
    }

    public function save()
    {
        if ($this->otherActivity->user_id == -1) {
            $this->otherActivity->user_id = null;
        }

        if ($this->otherActivity->city_id == -1) {
            $this->otherActivity->city_id = null;
        }

        try {
            $this->validate();
        } catch (ValidationException $e) {
            $this->emit('validationError', ['message' => $e->getMessage()]);

            return null;
        }

        $this->otherActivity->save();

        return redirect()->route('admin.other-activities.index');
    }

    public function nextStep(): void
    {
        // If expert is cgp employee then set is_free_for_user to true
        $user = User::query()->with('expert_data')->find($this->otherActivity->user_id);

        if ($user && $user->expert_data->is_cgp_employee) {
            $this->is_free_for_user = true;
        }

        if ($this->step >= $this->max_step) {
            return;
        }

        // Type needs to be selected
        if ($this->step == 0 && $this->otherActivity->type == -1) {
            return;
        }

        // Permission needs to be selected
        if ($this->step == 1 && $this->otherActivity->permission_id == -1) {
            return;
        }

        // Country needs to be selected
        if ($this->step == 2 && $this->otherActivity->country_id == -1) {
            return;
        }

        // Company needs to be selected
        if ($this->step == 3 && $this->otherActivity->company_id == -1) {
            return;
        }

        // If otherActivity is free for company skip the price & currency inputs
        if ($this->step == 6 && $this->is_free_for_company) {
            $this->step += 2;
            $this->title = $this->getTitle();

            return;
        }

        // If otherActivity is free for expert skip the price & currency inputs
        if ($this->step == 12 && $this->is_free_for_user) {
            $this->step += 2;
            $this->title = $this->getTitle();

            return;
        }

        $this->step++;
        $this->title = $this->getTitle();
    }

    public function prevStep(): void
    {
        if ($this->step <= 0) {
            return;
        }

        // If otherActivity is free for company skip the price & currency inputs
        if ($this->step == 6 && $this->is_free_for_company) {
            $this->step -= 2;
            $this->title = $this->getTitle();

            return;
        }

        // If otherActivity is free for expert skip the price & currency inputs
        if ($this->step == 12 && $this->is_free_for_user) {
            $this->step -= 2;
            $this->title = $this->getTitle();

            return;
        }

        $this->step--;
        $this->title = $this->getTitle();
    }

    private function getTitle()
    {
        return [
            __('other-activity.type'),
            __('other-activity.permission_id'),
            __('common.country'),
            __('workshop.company_name'),
            __('workshop.activity_id'),
            __('workshop.city'),
            __('other-activity.is_free_for_company'),
            __('workshop.contract_price'),
            __('workshop.company_phone'),
            __('workshop.company_email'),
            __('workshop.expert'),
            __('workshop.expert_phone'),
            __('other-activity.is_free_for_expert'),
            __('workshop.expert_out_price'),
            __('workshop.language'),
            __('workshop.number_of_participants'),
            __('workshop.date'),
            __('workshop.start_time'),
            __('workshop.end_time'),
            __('workshop.full_time'),
            __('otherActivity.save'),
        ][$this->step];
    }
}
