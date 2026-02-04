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
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithFileUploads;

class Edit extends Component
{
    use WithFileUploads;

    public LiveWebinar $live_webinar;

    public Collection $companies;

    public Collection $languages;

    public Collection $countries;

    public Collection $experts;

    public Collection $permissions;

    public $image;

    public array $selected_companies;

    public array $selected_countries;

    public string $selected_date;

    public bool $all_company;

    public function mount(LiveWebinar $live_webinar): void
    {
        $this->live_webinar = $live_webinar;

        $this->selected_companies = $this->live_webinar->companies->pluck('id')->toArray();
        $this->selected_countries = $this->live_webinar->countries->pluck('id')->toArray();
        $this->selected_date = $this->live_webinar->from;

        if (empty($this->selected_companies)) {
            $this->all_company = true;
        }

        $this->companies = Company::query()->where('active', 1)->orderBy('name')->get();
        $this->languages = EapLanguage::query()->get();
        $this->countries = Country::query()->get();
        $this->experts = User::query()->where('type', 'expert')->where('locked', 0)->get();
    }

    public function render()
    {
        $this->permissions = Permission::query()->get();

        return view('livewire.admin.live-webinar.edit')->extends('layout.master');
    }

    protected function rules(): array
    {
        return [
            'selected_companies' => ['nullable', 'array'],
            'selected_companies.*' => ['exists:companies,id'],
            'selected_countries' => ['nullable', 'array'],
            'selected_countries.*' => ['exists:countries,id'],
            'live_webinar.language_id' => ['required'],
            'live_webinar.permission_id' => ['required', 'exists:permissions,id'],
            'live_webinar.user_id' => ['required', 'exists:users,id'],
            'live_webinar.topic' => ['required'],
            'selected_date' => ['required', 'date'],
            'live_webinar.duration' => ['required'],
            'live_webinar.description' => ['required', 'nullable'],
            'live_webinar.image' => ['nullable'],
            'live_webinar.currency' => ['required'],
            'live_webinar.price' => ['required', 'integer'],
        ];
    }

    public function updated($propertyName, $value): void
    {
        // Validate uploaded image
        if ($this->image) {
            try {
                $this->validate([
                    'image' => ['file', 'max:2048', 'mimes:jpeg,png,jpg'],
                ]);
            } catch (ValidationException) {
                $this->emit('errorEvent', __('eap-online.live-webinars.invalid_image'));

                return;
            }

            if ($propertyName == 'image') {
                $this->live_webinar->image = $this->image->store('live-webinar-images/'.$this->live_webinar->id, 'local');
                $this->live_webinar->save();
            }
        }

        if ($propertyName === 'all_company') {
            if ($value) {
                $this->selected_companies = [];
                $this->selected_countries = [];
            }
            $this->emit('show_company_country');
        }

        if ($propertyName === 'live_webinar.user_id') {
            $expert_data = InvoiceData::query()->where('user_id', $value)->first();
            if (! $expert_data) {
                $this->emit('show_invoice_data_error');
            }
            $this->live_webinar->currency = $expert_data->currency;
        }
    }

    public function update(): void
    {
        try {
            $this->validate();
        } catch (ValidationException $e) {
            $this->emit('errorEvent', $e->getMessage());

            return;
        }

        $this->live_webinar->companies()->sync($this->selected_companies);
        $this->live_webinar->countries()->sync($this->selected_countries);
        $this->live_webinar->from = Carbon::parse($this->selected_date);
        $this->live_webinar->to = Carbon::parse($this->selected_date)->addMinutes($this->live_webinar->duration);
        $this->live_webinar->save();

        $this->live_webinar = app(LiveWebinarService::class)->sync_zoom_meeting($this->live_webinar);

        $this->emit('save_succesfull');
    }

    public function delete_image(): void
    {
        if (Storage::disk('local')->exists($this->live_webinar->image)) {
            unlink(public_path('assets/'.$this->live_webinar->image));
        }

        $this->image = null;
        $this->live_webinar->image = null;
        $this->live_webinar->save();
    }
}
