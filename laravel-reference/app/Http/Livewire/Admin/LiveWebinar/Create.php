<?php

namespace App\Http\Livewire\Admin\LiveWebinar;

use App\Models\Company;
use App\Models\Country;
use App\Models\EapOnline\EapLanguage;
use App\Models\InvoiceData;
use App\Models\LiveWebinar;
use App\Models\Permission;
use App\Models\User;
use App\Services\LiveWebinarService;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;
use Livewire\WithFileUploads;

class Create extends Component
{
    use WithFileUploads;

    public LiveWebinar $live_webinar;

    public Collection $companies;

    public Collection $countries;

    public Collection $languages;

    public Collection $experts;

    public Collection $permissions;

    public array $selected_companies = [];

    public array $selected_countries = [];

    public array $selected_languages;

    public string $selected_date;

    public bool $all_company = false;

    public $image;

    public function render()
    {
        $this->permissions = Permission::query()->get();

        return view('livewire.admin.live-webinar.create')->extends('layout.master');
    }

    public function mount(): void
    {
        $this->live_webinar = new LiveWebinar;

        $this->companies = Company::query()->where('active', 1)->orderBy('name')->get();
        $this->countries = Country::query()->get();
        $this->languages = EapLanguage::query()->get();
        $this->experts = User::query()->where('type', 'expert')->where('locked', 0)->get();
    }

    protected function rules(): array
    {
        return [
            'selected_companies' => ['nullable', 'array'],
            'selected_companies.*' => ['exists:companies,id'],
            'selected_countries' => ['nullable', 'array'],
            'selected_countries.*' => ['exists:countries,id'],
            'live_webinar.permission_id' => ['required', 'exists:permissions,id'],
            'live_webinar.language_id' => ['required'],
            'live_webinar.user_id' => ['required', 'exists:users,id'],
            'live_webinar.topic' => ['required'],
            'selected_date' => ['required', 'date_format:"Y-m-d H:i'],
            'live_webinar.duration' => ['required'],
            'live_webinar.description' => ['required'],
            'live_webinar.image' => ['nullable'],
            'live_webinar.currency' => ['required'],
            'live_webinar.price' => ['required', 'integer'],
            'image' => ['file', 'max:2048', 'mimes:jpeg,png,jpg'],
        ];
    }

    public function updated($input, $value): void
    {
        if ($input === 'image') {
            try {
                $this->validateOnly('image');
            } catch (Exception) {
                $this->image = null;
                $this->emit('show_form_error', __('eap-online.live-webinars.invalid_image'));

                return;
            }
        }

        if ($input === 'all_company') {
            $this->emit('show_company_country');

            if ($value) {
                $this->selected_companies = [];
                $this->selected_countries = [];
            }
        }

        if ($input === 'live_webinar.user_id') {
            $expert_data = InvoiceData::query()->where('user_id', $value)->first();
            if (! $expert_data) {
                $this->emit('show_invoice_data_error');
            }
            $this->live_webinar->currency = $expert_data->currency;
        }
    }

    public function save(): void
    {
        try {
            $this->validate();
        } catch (Exception $e) {
            $this->image = null;
            $this->emit('show_form_error', $e->getMessage());

            return;
        }

        $this->live_webinar->from = Carbon::parse($this->selected_date);
        $this->live_webinar->to = Carbon::parse($this->selected_date)->addMinutes($this->live_webinar->duration);

        $this->live_webinar->save();

        $this->live_webinar->companies()->attach($this->selected_companies);
        $this->live_webinar->countries()->attach($this->selected_countries);

        if ($this->image) {
            $this->live_webinar->image = $this->image->store('live-webinar-images/'.$this->live_webinar->id, 'local');
        }

        $this->live_webinar->save();

        $this->live_webinar = app(LiveWebinarService::class)->sync_zoom_meeting($this->live_webinar);

        $this->emit('save_succesfull');
    }

    public function delete_image(): void
    {
        $this->image = null;
    }
}
