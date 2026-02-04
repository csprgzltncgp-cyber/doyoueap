<?php

namespace App\Http\Livewire\Admin\Expert;

use App\Enums\InvoicingType;
use App\Models\City;
use App\Models\Country;
use App\Models\CustomInvoiceItem;
use App\Models\EapOnlineData;
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
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithFileUploads;

class Create extends Component
{
    use WithFileUploads;

    public ExpertData $expertData;

    public InvoiceData $invoiceData;

    public EapOnlineData $eapOnlineData;

    public User $user;

    public $contracts = [];

    public $certificates = [];

    public $eapOnlinePhoto;

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

    public $showPsychologistData = false;

    public $custom_invoice_item;

    public $custom_invoice_items;

    public $hidden_prices = false;

    protected $messages = [];

    protected function rules(): array
    {
        $rules = [
            'user.name' => 'required',
            'user.email' => 'required|email',
            'user.username' => 'required',
            'user.language_id' => 'required',
            'expertCountries' => 'required|array',
            'expertCountries.*' => 'required|exists:countries,id',
            'expertCities' => 'array',
            'expertCities.*' => 'required|exists:cities,id',
            'expertOutsourceCountries' => 'nullable|array',
            'expertOutsourceCountries.*' => 'nullable|exists:countries,id',
            'expertData.phone_prefix' => 'required',
            'expertData.phone_number' => 'required',
            'expertData.is_cgp_employee' => 'nullable',
            'expertData.is_eap_online_expert' => 'nullable',
            'expertData.native_language' => 'required',
            'invoiceData.currency' => 'required',
            'invoiceData.invoicing_type' => 'nullable|string',
            'expertData.min_inprogress_cases' => 'required|numeric',
            'expertData.max_inprogress_cases' => 'required|numeric',
            'invoiceData.single_session_rate' => 'nullable|numeric',
        ];

        if ($this->expertData->is_eap_online_expert) {
            $rules['eapOnlineData.description'] = 'required|max:180';
            $rules['eapOnlineData.image'] = 'nullable';
        }

        if ($this->invoiceData->invoicing_type === InvoicingType::TYPE_FIXED) {
            $rules['invoiceData.fixed_wage'] = 'required';
            $rules['invoiceData.ranking_hourly_rate'] = 'required';
        }

        if ($this->invoiceData->invoicing_type === InvoicingType::TYPE_NORMAL) {
            $rules['invoiceData.hourly_rate_50'] = 'required';
            if (in_array(2, $this->expertPermissions) || in_array(3, $this->expertPermissions) || in_array(7, $this->expertPermissions)) {
                $rules['invoiceData.hourly_rate_30'] = 'required';
            }
        }

        if (in_array(17, $this->expertPermissions)) {
            $rules['invoiceData.single_session_rate'] = 'required|numeric';
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
            'expertData.is_eap_online_expert' => trans('expert-data.warnings.is_eap_online_expert'),
            'expertData.native_language' => trans('expert-data.warnings.native_language'),
            'invoiceData.fixed_wage' => trans('expert-data.warnings.language_skills_required'),
            'invoiceData.ranking_hourly_rate' => trans('expert-data.warnings.language_skills_required'),
        ];

        if (! $this->expertData->is_cgp_employee) {
            $additionalRules = [
                'expertData.post_code' => 'required',
                'expertData.city_id' => 'required',
                'expertData.country_id' => 'required',
                'expertData.street' => 'required',
                'expertData.street_suffix' => 'required',
                'expertData.house_number' => 'required',
                'contracts' => 'array|min:1|required',
                'contracts.*' => 'file|max:10240',
                'certificates' => 'array|min:1|required',
                'certificates.*' => 'required|file|max:10240',
            ];

            return array_merge($rules, $additionalRules);
        }

        return $rules;
    }

    public function mount(): void
    {
        $this->user = new User;
        $this->expertData = new ExpertData;
        $this->eapOnlineData = new EapOnlineData;
        $this->invoiceData = new InvoiceData;
        $this->custom_invoice_item = new CustomInvoiceItem;
    }

    public function render()
    {
        $this->checkPermissionChange();
        // $this->hidden_prices = $this->expertData->is_cgp_employee;
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

        return view('livewire.admin.expert.create', ['phonePrefixes' => $phonePrefixes, 'countries' => $countries, 'cities' => $cities, 'streetSuffixes' => $streetSuffixes, 'permissions' => $permissions, 'languages' => $languages, 'specializations' => $specializations, 'languageSkills' => $languageSkills])->extends('layout.master');
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

    public function store()
    {
        try {
            $this->validate();
        } catch (ValidationException $e) {
            $this->emit('errorEvent', collect($e->errors())->first()[0]);

            return null;
        }

        $this->user->type = 'expert';
        $this->user->password = bcrypt($this->user->username.'9872346');
        $this->user->save();

        if ($this->user->id !== 0 && ! empty($this->custom_invoice_items)) {
            foreach ($this->custom_invoice_items as $item) {
                $this->custom_invoice_item->create([
                    'user_id' => $this->user->id,
                    'name' => $item['name'],
                    'country_id' => $item['country_id'],
                    'amount' => filter_var($item['amount'], FILTER_SANITIZE_NUMBER_FLOAT),
                ]);
            }
        }

        $this->expertData->user_id = $this->user->id;
        $this->expertData->required_documents = false;
        $this->expertData->completed_first = true;
        $this->expertData->save();

        if ($this->expertData->is_eap_online_expert) {
            $this->eapOnlineData->user_id = $this->user->id;
            $this->eapOnlineData->image = $this->eapOnlinePhoto->store('expert-data-files/eap-online/'.$this->expertData->id, 'local');
            $this->eapOnlineData->save();
        }

        $this->invoiceData->user_id = $this->user->id;
        $this->invoiceData->save();

        $this->user->expertCountries()->sync($this->expertCountries);
        $this->user->expertCrisisCountries()->sync($this->expertCrisisCountries);
        $this->user->cities()->sync($this->expertCities);
        $this->user->outsource_countries()->sync($this->expertOutsourceCountries);
        $this->user->permission()->sync($this->expertPermissions);
        $this->user->specializations()->sync($this->expertSpecializations);
        $this->user->language_skills()->sync($this->expertLanguageSkills);
        $this->user->setCrisisPsychologist($this->crisisPsychologist);

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

        return redirect()->route('admin.experts.list');
    }

    public function checkPermissionChange(): void
    {
        $this->showPsychologistData = in_array('1', $this->expertPermissions);
    }

    public function show_custom_item_dialog(): void
    {
        $this->emit('extraItemDialogVisible', $this->invoiceData->currency);
    }

    public function add_custom_invoice_item(array $data): void
    {
        $this->custom_invoice_items[] = [
            'name' => $data['input_1'],
            'country_id' => $data['input_2'],
            'amount' => $data['input_3'],
        ];

        try {
            $this->validate([
                'custom_invoice_items.*.name' => ['required'],
                'custom_invoice_items.*.country_id' => ['required'],
                'custom_invoice_items.*.amount' => ['required'],
            ]);
        } catch (ValidationException) {
            array_pop($this->custom_invoice_items);
            $this->emit('errorEventCustomItem');

            return;
        }

        $this->emit('custom_item_added');
    }

    public function delete_custom_item($index): void
    {
        unset($this->custom_invoice_items[$index]);
    }
}
