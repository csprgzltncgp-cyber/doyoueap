<?php

namespace App\Http\Livewire\Admin\Expert;

use App\Enums\InvoicingType;
use App\Models\City;
use App\Models\Country;
use App\Models\CustomInvoiceItem;
use App\Models\EapOnlineData;
use App\Models\ExpertCurrencyChange;
use App\Models\ExpertData;
use App\Models\ExpertFile;
use App\Models\InvoiceData;
use App\Models\Language;
use App\Models\LanguageSkill;
use App\Models\Permission;
use App\Models\Specialization;
use App\Models\User;
use App\Scopes\CountryScope;
use App\Scopes\LanguageScope;
use App\Traits\MoreCasesTrait;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithFileUploads;

class Edit extends Component
{
    use MoreCasesTrait;
    use WithFileUploads;

    public ExpertData $expertData;

    public InvoiceData $invoiceData;

    public EapOnlineData $eapOnlineData;

    public ExpertCurrencyChange $expertCurrencyChange;

    public User $user;

    public $contracts = [];

    public $certificates = [];

    public $eapOnlinePhoto;

    public $expertCurrencyChangeDocument;

    public $expertCountries = [];

    public $expertCrisisCountries = [];

    public $expertCities = [];

    public $expertOutsourceCountries = [];

    public $expertPermissions = [];

    public $expertSpecializations = [];

    public $expertLanguageSkills = [];

    public $crisisPsychologist;

    public $temp_contracts = [];

    public $temp_certificates = [];

    public $existing_contracts = [];

    public $existing_certificates = [];

    public $showPsychologistData = false;

    public $custom_invoice_item;

    public $custom_invoice_items;

    public $hidden_prices = false;

    public bool $single_session_rate_required = false;

    protected $listeners = [
        'deleteExistingFile' => 'deleteExistingFile',
        'deleteExpertCurrencyChangeDocument' => 'deleteExpertCurrencyChangeDocument',
    ];

    protected $messages = [];

    protected function rules(): array
    {
        if ($this->expertData->is_cgp_employee) {
            $hourlyRateRules = [
                'invoiceData.currency' => 'nullable',
                'invoiceData.hourly_rate_50' => 'nullable',
            ];

            if ($this->user->hasPermission(2) || $this->user->hasPermission(3) || $this->user->hasPermission(7) || $this->user->hasPermission(16)) {
                $hourlyRateRules['invoiceData.hourly_rate_30'] = 'nullable';
                $hourlyRateRules['invoiceData.hourly_rate_15'] = 'nullable';
            }
        } else {
            $hourlyRateRules = [
                'invoiceData.currency' => 'nullable',
                'invoiceData.hourly_rate_50' => 'nullable',
            ];

            if ($this->user->hasPermission(2) || $this->user->hasPermission(3) || $this->user->hasPermission(7) || $this->user->hasPermission(16)) {
                $hourlyRateRules['invoiceData.hourly_rate_30'] = 'nullable';
                $hourlyRateRules['invoiceData.hourly_rate_15'] = 'nullable';
            }

            $additional_rules = [
                'expertData.post_code' => 'nullable',
                'expertData.city_id' => 'nullable',
                'expertData.country_id' => 'nullable',
                'expertData.street' => 'nullable',
                'expertData.street_suffix' => 'nullable',
                'expertData.house_number' => 'nullable',
            ];

            if ($this->expertData->files()->where('type', ExpertFile::TYPE_CONTRACT)->count() == 0) {
                $additional_rules['contracts'] = 'sometimes|array';
                $additional_rules['contracts.*'] = 'sometimes|file|max:10240';
            }

            if ($this->expertData->files()->where('type', ExpertFile::TYPE_CERTIFICATE)->count() == 0) {
                $additional_rules['certificates'] = 'sometimes|array';
                $additional_rules['certificates.*'] = 'sometimes|file|max:10240';
            }

            $hourlyRateRules = array_merge($hourlyRateRules, $additional_rules);
        }

        $rules = [
            'user.name' => 'nullable',
            'user.email' => 'nullable|email',
            'user.username' => 'nullable',
            'user.language_id' => 'nullable',
            'expertCountries' => 'nullable|array',
            'expertCountries.*' => 'nullable|exists:countries,id',
            'expertCities' => 'nullable|array',
            'expertCities.*' => 'nullable|exists:cities,id',
            'expertOutsourceCountries' => 'nullable|array',
            'expertOutsourceCountries.*' => 'nullable|exists:countries,id',
            'expertPermissions' => 'nullable|array',
            'expertPermissions.*' => 'nullable|exists:permissions,id',
            'expertData.phone_prefix' => 'nullable',
            'expertData.phone_number' => 'nullable',
            'eapOnlineData.description' => 'nullable|max:180',
            'expertData.is_cgp_employee' => 'sometimes|boolean',
            'expertData.is_eap_online_expert' => 'sometimes|boolean',
            'expertData.native_language' => 'nullable',
            'invoiceData.invoicing_type' => 'nullable|string',
            'custom_invoice_item.name' => 'nullable|string',
            'custom_invoice_item.country_id' => 'nullable|exists:countries,id',
            'custom_invoice_item.amount' => 'nullable',
            'expertData.min_inprogress_cases' => 'nullable|numeric',
            'expertData.max_inprogress_cases' => 'nullable|numeric',
            'invoiceData.single_session_rate' => 'nullable|numeric',
        ];

        if ($this->user->hasPermission(1) || $this->user->hasPermission(2)) {
            $rules['expertSpecializations'] = 'nullable|array';
            $rules['expertSpecializations.*'] = 'nullable|exists:specializations,id';
        }

        if ($this->invoiceData->invoicing_type === InvoicingType::TYPE_FIXED) {
            $rules['invoiceData.fixed_wage'] = 'required';
            $rules['invoiceData.ranking_hourly_rate'] = 'required';
        }

        $this->messages = [
            'invoiceData.currency' => trans('expert-data.warnings.currency_required'),
            'invoiceData.hourly_rate_50' => trans('expert-data.warnings.hourly_rate_50_required'),
            'invoiceData.hourly_rate_30' => trans('expert-data.warnings.hourly_rate_30_required'),
            'expertData.post_code' => trans('expert-data.warnings.post_code_required'),
            'expertData.city_id' => trans('expert-data.warnings.city_id_required'),
            'expertData.country_id' => trans('expert-data.warnings.country_id_required'),
            'expertData.street' => trans('expert-data.warnings.street_required'),
            'expertData.street_suffix' => trans('expert-data.warnings.street_suffix_required'),
            'expertData.house_number' => trans('expert-data.warnings.house_number_required'),
            'user.name.required' => trans('expert-data.warnings.name_required'),
            'user.email.required' => trans('expert-data.warnings.email_required'),
            'user.username.required' => trans('expert-data.warnings.username_required'),
            'user.language_id.required' => trans('expert-data.warnings.language_required'),
            'expertCountries.required' => trans('expert-data.warnings.country_required'),
            'expertCountries.*.required' => trans('expert-data.warnings.country_required'),
            'expertPermissions.required' => trans('expert-data.warnings.permissions_required'),
            'expertData.max_inprogress_cases.required' => trans('expert-data.warnings.max_inprogress_cases_required'),
            'expertData.min_inprogress_cases.required' => trans('expert-data.warnings.max_inprogress_cases_required'),
            'expertData.min_inprogress_cases.numeric' => trans('expert-data.warnings.min_inprogress_cases_num'),
            'expertData.max_inprogress_cases.numeric' => trans('expert-data.warnings.max_inprogress_cases_num'),
            'expertData.phone_prefix.required' => trans('expert-data.warnings.phone_prefix_required'),
            'expertData.phone_number.required' => trans('expert-data.warnings.phone_number_required'),
            'contracts' => trans('expert-data.warnings.contracts_required'),
            'certificates' => trans('expert-data.warnings.certificates_required'),
            'expertData.is_cgp_employee' => trans('expert-data.warnings.is_cgp_employee'),
            'expertData.native_language' => trans('expert-data.warnings.native_language'),
            'expertSpecializations' => trans('expert-data.warnings.specializations_required'),
            'expertLanguageSkills' => trans('expert-data.warnings.language_skills_required'),
            'invoiceData.fixed_wage' => trans('expert-data.warnings.language_skills_required'),
            'invoiceData.ranking_hourly_rate' => trans('expert-data.warnings.language_skills_required'),
        ];

        return array_merge($rules, $hourlyRateRules);
    }

    public function mount(User $user): void
    {
        $this->user = $user;

        $this->expertData = ExpertData::query()->firstOrCreate([
            'user_id' => $this->user->id,
        ]);

        $this->invoiceData = InvoiceData::query()->firstOrCreate([
            'user_id' => $this->user->id,
        ]);

        $this->eapOnlineData = EapOnlineData::query()->firstOrCreate([
            'user_id' => $this->user->id,
        ]);

        $this->expertCurrencyChange = ExpertCurrencyChange::query()->where('user_id', $this->user->id)->first() ?? new ExpertCurrencyChange;

        $this->custom_invoice_items = CustomInvoiceItem::query()->where('user_id', $this->user->id)->get();

        $this->expertCountries = $this->user->expertCountries->pluck('id')->toArray();
        $this->expertCrisisCountries = $this->user->expertCrisisCountries->pluck('id')->toArray();
        $this->expertCities = $this->user->cities->pluck('id')->toArray();
        $this->expertOutsourceCountries = $this->user->outsource_countries->pluck('id')->toArray();
        $this->expertPermissions = $this->user->permission->pluck('id')->toArray();
        $this->expertSpecializations = $this->user->specializations->pluck('id')->toArray();
        $this->expertLanguageSkills = $this->user->language_skills->pluck('id')->toArray();
    }

    public function render()
    {
        // $this->hidden_prices = $this->expertData->is_cgp_employee;

        $this->existing_contracts = ExpertFile::query()->where('expert_data_id', $this->expertData->id)
            ->where('type', ExpertFile::TYPE_CONTRACT)
            ->get();

        $this->existing_certificates = ExpertFile::query()->where('expert_data_id', $this->expertData->id)
            ->where('type', ExpertFile::TYPE_CERTIFICATE)
            ->get();

        $this->eapOnlinePhoto = $this->eapOnlineData->image;

        $this->expertCurrencyChangeDocument = optional($this->expertCurrencyChange)->document;

        $this->crisisPsychologist = $this->user->expert_data->crisis_psychologist;
        $this->checkPermissionChange();

        $this->expertData->load('country');
        $this->user->load(['expertCountries', 'expertCrisisCountries', 'cities', 'permission']);

        $phonePrefixes = collect(json_decode(file_get_contents(public_path('/assets/phone-prefixes.json')), true, 512, JSON_THROW_ON_ERROR))->sortBy('code')->toArray();
        $countries = Country::query()->withoutGlobalScopes([CountryScope::class, LanguageScope::class])->orderBy('name')->get();
        $cities = City::query()->withoutGlobalScope(CountryScope::class)->orderBy('name')->get();
        $permissions = Permission::query()->get();
        $languages = Language::query()->orderBy('name', 'asc')->get();
        $specializations = Specialization::query()->get();
        $languageSkills = LanguageSkill::query()->get();
        $streetSuffixes = [
            ['id' => ExpertData::STREET_SUFFIX_STREET, 'name' => __('expert-data.street_suffix.'.ExpertData::STREET_SUFFIX_STREET)],
            ['id' => ExpertData::STREET_SUFFIX_SQUARE, 'name' => __('expert-data.street_suffix.'.ExpertData::STREET_SUFFIX_SQUARE)],
            ['id' => ExpertData::STREET_SUFFIX_ROAD, 'name' => __('expert-data.street_suffix.'.ExpertData::STREET_SUFFIX_ROAD)],
        ];

        return view('livewire.admin.expert.edit', ['phonePrefixes' => $phonePrefixes, 'countries' => $countries, 'cities' => $cities, 'streetSuffixes' => $streetSuffixes, 'permissions' => $permissions, 'languages' => $languages, 'specializations' => $specializations, 'languageSkills' => $languageSkills])->extends('layout.master');
    }

    public function updated($propertyName, $value): void
    {
        $this->validateOnly($propertyName);

        if ($propertyName == 'eapOnlinePhoto') {
            $this->eapOnlineData->image = $this->eapOnlinePhoto->store('expert-data-files/eap-online/'.$this->expertData->id, 'local');
            $this->eapOnlineData->save();
        }

        if ($propertyName == 'contracts') {
            foreach ($value as $tempFile) {
                $this->temp_contracts[] = $tempFile;
            }

            $this->contracts = $this->temp_contracts;
        }

        if ($propertyName == 'certificates') {
            foreach ($value as $tempFile) {
                $this->temp_certificates[] = $tempFile;
            }

            $this->certificates = $this->temp_certificates;
        }

        if ($propertyName == 'crisisPsychologist') {
            $this->user->setCrisisPsychologist($value);
            $this->expertData->crisis_psychologist = $value;
        }
    }

    public function removeFileFromTempContracts($id): void
    {
        unset($this->temp_contracts[$id]);
        $this->contracts = $this->temp_contracts;
    }

    public function removeFileFromTempCertificates($id): void
    {
        unset($this->temp_certificates[$id]);
        $this->certificates = $this->temp_certificates;
    }

    public function downloadExistingFile($id)
    {
        $file = ExpertFile::query()->find($id);

        return response()->download(storage_path('app/'.$file->path));
    }

    public function downloadExpertCurrencyChangeDocument()
    {
        return response()->download(storage_path('app/'.$this->expertCurrencyChange->document));
    }

    public function deleteEapOnlinePhoto(): void
    {
        $this->eapOnlinePhoto = null;
        unlink(public_path('assets/'.$this->eapOnlineData->image));

        $this->eapOnlineData->image = null;
        $this->eapOnlineData->save();
    }

    public function deleteExpertCurrencyChangeDocument(): void
    {
        $this->expertCurrencyChangeDocument = null;
        unlink(storage_path('app/'.$this->expertCurrencyChange->document));

        $this->expertCurrencyChange->document = null;
        $this->expertCurrencyChange->save();

        $this->expertCurrencyChange->delete();
    }

    public function deleteExistingFile($id): void
    {
        $file = ExpertFile::query()->find($id);
        $file->delete();
        $this->existing_contracts = ExpertFile::query()->where('expert_data_id', $this->expertData->id)
            ->where('type', ExpertFile::TYPE_CONTRACT)
            ->get();

        $this->existing_certificates = ExpertFile::query()->where('expert_data_id', $this->expertData->id)
            ->where('type', ExpertFile::TYPE_CERTIFICATE)
            ->get();
    }

    public function checkPermissionChange(): void
    {
        if (in_array('1', $this->expertPermissions)) {
            $this->showPsychologistData = true;
        } else {
            $this->user->setCrisisPsychologist(false);
            $this->showPsychologistData = false;
        }

        $this->single_session_rate_required = in_array('17', $this->expertPermissions);
    }

    public function update(): void
    {
        try {
            $this->validate();
        } catch (ValidationException $e) {
            $this->emit('errorEvent', collect($e->errors())->first()[0]);

            return;
        }

        if ($this->expertData->isDirty('max_inprogress_cases') || $this->expertData->isDirty('min_inprogress_cases')) {
            $this->expertData->save();
            $this->setCanAcceptMoreCases($this->user);
        }

        // Reset invoice hourly rate and/or fixed wage depending on invoicing type
        if ($this->invoiceData->invoicing_type === InvoicingType::TYPE_FIXED) {
            $this->invoiceData->hourly_rate_50 = null;
            $this->invoiceData->hourly_rate_30 = null;
            $this->invoiceData->hourly_rate_15 = null;
        }

        if ($this->invoiceData->invoicing_type === InvoicingType::TYPE_CUSTOM) {
            $this->invoiceData->hourly_rate_50 = null;
            $this->invoiceData->hourly_rate_30 = null;
            $this->invoiceData->hourly_rate_15 = null;
            $this->invoiceData->fixed_wage = null;
            $this->invoiceData->ranking_hourly_rate = null;
        }

        if ($this->invoiceData->invoicing_type === InvoicingType::TYPE_NORMAL) {
            $this->invoiceData->fixed_wage = null;
            $this->invoiceData->ranking_hourly_rate = null;
        }

        if (! $this->single_session_rate_required) {
            $this->invoiceData->single_session_rate = null;
        }

        $this->eapOnlineData->save();
        $this->expertData->save();
        $this->invoiceData->save();
        $this->user->save();
        $this->user->expertCountries()->sync($this->expertCountries);
        $this->user->expertCrisisCountries()->sync($this->expertCrisisCountries);
        $this->user->cities()->sync($this->expertCities);
        $this->user->outsource_countries()->sync($this->expertOutsourceCountries);
        $this->user->permission()->sync($this->expertPermissions);

        $this->user->specializations()->sync($this->expertSpecializations);
        $this->user->language_skills()->sync($this->expertLanguageSkills);

        foreach ($this->contracts as $contract) {
            $this->expertData->files()->create([
                'filename' => $contract->getClientOriginalName(),
                'path' => $contract->store('expert-data-files/contracts/'.$this->expertData->id, 'private'),
                'type' => ExpertFile::TYPE_CONTRACT,
            ]);
        }

        foreach ($this->certificates as $certificate) {
            $this->expertData->files()->create([
                'filename' => $certificate->getClientOriginalName(),
                'path' => $certificate->store('expert-data-files/certificates/'.$this->expertData->id, 'private'),
                'type' => ExpertFile::TYPE_CERTIFICATE,
            ]);
        }

        $this->expertData->refresh();
        $this->expertData->load(['files', 'country', 'city']);
        $this->temp_contracts = [];
        $this->contracts = [];
        $this->temp_certificates = [];
        $this->certificates = [];

        $this->emit('expertDataUpdated');

    }

    public function show_custom_item_dialog(): void
    {
        $this->emit('extraItemDialogVisible', $this->invoiceData->currency);
    }

    public function add_custom_invoice_item(array $data): void
    {
        $this->custom_invoice_item = CustomInvoiceItem::query()->create([
            'user_id' => $this->user->id,
            'name' => $data['input_1'],
            'country_id' => $data['input_2'],
            'amount' => filter_var($data['input_3'], FILTER_SANITIZE_NUMBER_FLOAT),
        ]);

        try {
            $this->validate([
                'custom_invoice_item.name' => ['required'],
                'custom_invoice_item.country_id' => ['required'],
                'custom_invoice_item.amount' => ['required'],
            ]);
        } catch (ValidationException) {
            $this->custom_invoice_item->delete();
            array_pop($this->custom_invoice_items);
            $this->emit('errorEventCustomItem');

            return;
        }

        $this->custom_invoice_items = CustomInvoiceItem::query()->where('user_id', $this->user->id)->get();

        $this->emit('custom_item_added');
    }

    public function delete_custom_item($index): void
    {
        $this->custom_invoice_items[$index]->delete();
        unset($this->custom_invoice_items[$index]);
    }
}
