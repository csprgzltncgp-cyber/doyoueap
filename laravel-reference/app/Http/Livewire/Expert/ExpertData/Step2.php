<?php

namespace App\Http\Livewire\Expert\ExpertData;

use App\Models\City;
use App\Models\Country;
use App\Models\ExpertCurrencyChange;
use App\Models\ExpertData;
use App\Models\ExpertFile;
use App\Models\InvoiceData;
use App\Models\LanguageSkill;
use App\Models\Specialization;
use App\Models\User;
use App\Scopes\CountryScope;
use App\Scopes\LanguageScope;
use Livewire\Component;
use Livewire\WithFileUploads;

class Step2 extends Component
{
    use WithFileUploads;

    public $currentUrl;

    public ExpertData $expertData;

    public InvoiceData $invoiceData;

    public ExpertCurrencyChange $expertCurrencyChange;

    public User $user;

    public $expertCurrencyChangeDocument;

    public $contracts = [];

    public $certificates = [];

    public $temp_contracts = [];

    public $temp_certificates = [];

    public $existing_contracts = [];

    public $existing_certificates = [];

    public $expertSpecializations = [];

    public $expertLanguageSkills = [];

    protected $listeners = [
        'deleteExistingFile' => 'deleteExistingFile',
        'deleteExpertCurrencyChangeDocument' => 'deleteExpertCurrencyChangeDocument',
        'save',
    ];

    protected function rules(): array
    {
        if ($this->expertData->required_documents && ! $this->expertData->completed_first) {
            $documentRules = [
                'contracts' => 'array|min:1|required',
                'certificates' => 'array|min:1|required',
                'contracts.*' => 'required|file|max:5120',
                'certificates.*' => 'required|file|max:5120',
            ];
        } else {
            $documentRules = [
                'contracts.*' => 'file|max:5120',
                'certificates.*' => 'file|max:5120',
            ];
        }

        // if expert is insider then hourly rate can be 0
        if (in_array(auth()->user()->id, [830, 878, 632, 826, 999, 809, 989, 39, 835, 834, 837, 877])) {
            $hourlyRateRules = [
                'invoiceData.currency' => 'required',
                'invoiceData.hourly_rate_50' => 'required',
            ];

            if ($this->user->hasPermission(2) || $this->user->hasPermission(3) || $this->user->hasPermission(7)) {
                $hourlyRateRules['invoiceData.hourly_rate_30'] = 'required';
            }
        } else {
            $hourlyRateRules = [
                'invoiceData.currency' => 'required|not_in:null',
                'invoiceData.hourly_rate_50' => 'required|not_in:0',
            ];

            if ($this->user->hasPermission(2) || $this->user->hasPermission(3) || $this->user->hasPermission(7)) {
                $hourlyRateRules['invoiceData.hourly_rate_30'] = 'required|not_in:0';
            }
        }

        $rules = [
            'user.email' => 'required|email',
            'expertData.phone_prefix' => 'required',
            'expertData.phone_number' => 'required',
            'expertData.post_code' => 'required',
            'expertData.city_id' => 'required|exists:cities,id',
            'expertData.country_id' => 'required|exists:countries,id',
            'expertData.street' => 'required',
            'expertData.street_suffix' => 'required|in:1,2,3',
            'expertData.house_number' => 'required',
            'expertLanguageSkills' => 'required|array',
            'expertLanguageSkills.*' => 'required|exists:language_skills,id',
        ];

        if ($this->user->hasPermission(1)) {
            $rules['expertSpecializations'] = 'required|array';
            $rules['expertSpecializations.*'] = 'required|exists:specializations,id';
        }

        return array_merge($rules, $documentRules, $hourlyRateRules);
    }

    public function mount(): void
    {
        $this->currentUrl = url()->current();

        $this->user = auth()->user();

        $this->expertData = ExpertData::query()->firstOrCreate([
            'user_id' => $this->user->id,
        ]);

        $this->invoiceData = InvoiceData::query()->firstOrCreate([
            'user_id' => $this->user->id,
        ]);

        $this->expertCurrencyChange = ExpertCurrencyChange::query()->where('user_id', $this->user->id)->first() ?? new ExpertCurrencyChange;

        $this->expertSpecializations = $this->user->specializations->pluck('id')->toArray();
        $this->expertLanguageSkills = $this->user->language_skills->pluck('id')->toArray();
    }

    public function render()
    {
        $this->existing_contracts = ExpertFile::query()->where('expert_data_id', $this->expertData->id)
            ->where('type', ExpertFile::TYPE_CONTRACT)
            ->get();

        $this->existing_certificates = ExpertFile::query()->where('expert_data_id', $this->expertData->id)
            ->where('type', ExpertFile::TYPE_CERTIFICATE)
            ->get();

        $this->expertData->load('country');

        $this->expertCurrencyChangeDocument = optional($this->expertCurrencyChange)->document;

        $phonePrefixes = collect(json_decode(file_get_contents(public_path('/assets/phone-prefixes.json')), true, 512, JSON_THROW_ON_ERROR))->sortBy('code')->toArray();
        $countries = Country::query()->withoutGlobalScopes([CountryScope::class, LanguageScope::class])->orderBy('name')->get();
        $cities = City::query()->withoutGlobalScope(CountryScope::class)->where('country_id', $this->expertData->country_id)->orderBy('name')->get();
        $specializations = Specialization::query()->get();
        $languageSkills = LanguageSkill::query()->get();
        $streetSuffixes = [
            ['id' => ExpertData::STREET_SUFFIX_STREET, 'name' => __('expert-data.street_suffix.'.ExpertData::STREET_SUFFIX_STREET)],
            ['id' => ExpertData::STREET_SUFFIX_SQUARE, 'name' => __('expert-data.street_suffix.'.ExpertData::STREET_SUFFIX_SQUARE)],
            ['id' => ExpertData::STREET_SUFFIX_ROAD, 'name' => __('expert-data.street_suffix.'.ExpertData::STREET_SUFFIX_ROAD)],
        ];

        $missingData = isset($_REQUEST['missingData']);

        return view('livewire.expert.expert-data.step2', ['phonePrefixes' => $phonePrefixes, 'countries' => $countries, 'cities' => $cities, 'streetSuffixes' => $streetSuffixes, 'specializations' => $specializations, 'languageSkills' => $languageSkills, 'missingData' => $missingData])->extends('layout.master');
    }

    public function updated($propertyName, $value): void
    {
        $this->validateOnly($propertyName);

        if ($this->user->isDirty()) {
            $this->user->save();
        }

        if ($this->expertData->isDirty()) {
            $this->expertData->save();
        }

        if ($this->invoiceData->isDirty()) {
            $this->invoiceData->save();
        }

        if ($propertyName == 'expertLanguageSkills') {
            $this->user->language_skills()->sync($this->expertLanguageSkills);
            $this->user->save();
        }

        if ($propertyName == 'expertSpecializations') {
            $this->user->specializations()->sync($this->expertSpecializations);
            $this->user->save();
        }

        if ($propertyName == 'expertData.country_id') {
            $this->expertData->city_id = null;
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

    public function save(): void
    {
        $this->validate();

        $this->expertData->save();
        $this->invoiceData->save();
        $this->user->specializations()->sync($this->expertSpecializations);
        $this->user->language_skills()->sync($this->expertLanguageSkills);
        $this->user->save();

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
        if ($this->expertData->files->where('type', ExpertFile::TYPE_CONTRACT)->count() > 0 && $this->expertData->files->where('type', ExpertFile::TYPE_CERTIFICATE)->count() > 0) {
            $this->expertData->required_documents = false;
            $this->expertData->save();
        }
        $this->emit('expertDataUpdated');
    }
}
