<?php

namespace App\Http\Livewire\Admin\Operator;

use App\Models\Country;
use App\Models\Language;
use App\Models\OperatorData;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithFileUploads;

class Create extends Component
{
    use WithFileUploads;

    public User $user;

    public OperatorData $operatorData;

    public $company_phones;

    public $files = [];

    public $temp_files = [];

    protected $rules = [
        'user.name' => 'sometimes|required|string',
        'user.username' => 'nullable|string',
        'user.password' => 'nullable|string',
        'user.country_id' => 'sometimes|required|exists:countries,id',
        'user.language_id' => 'sometimes|required|exists:languages,id',
        'user.connected_account' => 'nullable|string',
        'operatorData.position' => 'required',
        'operatorData.employment_type' => 'nullable',
        'operatorData.invoincing_name' => 'nullable|string',
        'operatorData.start_of_employment' => 'nullable|string',
        'operatorData.invoincing_post_code' => 'nullable|string',
        'operatorData.invoincing_country' => 'nullable|string',
        'operatorData.invoincing_city' => 'nullable|string',
        'operatorData.invoincing_street' => 'nullable|string',
        'operatorData.invoincing_house_number' => 'nullable|string',
        'operatorData.tax_number' => 'nullable|string',
        'operatorData.salary' => 'nullable',
        'operatorData.salary_currency' => 'nullable|string',
        'operatorData.eap_chat_username' => 'nullable|string',
        'operatorData.eap_chat_password' => 'nullable|string',
        'operatorData.private_email' => 'nullable|string',
        'operatorData.private_phone' => 'nullable|string',
        'operatorData.company_email' => 'nullable|string',
        'operatorData.company_phone' => 'nullable|string',
        'operatorData.language' => 'nullable|string',
        'operatorData.bank_account_number' => 'nullable|string',
        'company_phones.*.phone' => 'nullable|string',
        'files.*' => 'nullable|file|max:10240',
    ];

    public function mount(): void
    {
        $this->user = new User;
        $this->operatorData = new OperatorData;
    }

    public function render()
    {
        $countries = Country::query()->get();
        $languages = Language::query()->orderBy('name', 'asc')->get();
        $users = User::query()->where('type', 'operator')->where('connected_account', null)->orderBy('username', 'asc')->get();
        $positions = [
            OperatorData::POSITION_DAY => __('operator-data.position.'.OperatorData::POSITION_DAY),
            OperatorData::POSITION_NIGHT => __('operator-data.position.'.OperatorData::POSITION_NIGHT),
            OperatorData::POSITION_DAY_NIGHT => __('operator-data.position.'.OperatorData::POSITION_DAY_NIGHT),
        ];

        $employment_types = [
            OperatorData::EMPLOYMENT_TYPE_FULL_TIME => __('operator-data.employment_type.'.OperatorData::EMPLOYMENT_TYPE_FULL_TIME),
            OperatorData::EMPLOYMENT_TYPE_PART_TIME => __('operator-data.employment_type.'.OperatorData::EMPLOYMENT_TYPE_PART_TIME),
            OperatorData::EMPLOYMENT_TYPE_CASUAL_EMPLOYMENT => __('operator-data.employment_type.'.OperatorData::EMPLOYMENT_TYPE_CASUAL_EMPLOYMENT),
            OperatorData::EMPLOYMENT_TYPE_CONTRACT => __('operator-data.employment_type.'.OperatorData::EMPLOYMENT_TYPE_CONTRACT),
        ];

        return view('livewire.admin.operator.create', ['countries' => $countries, 'languages' => $languages, 'users' => $users, 'positions' => $positions, 'employment_types' => $employment_types])->extends('layout.master');
    }

    public function updated($field, $value): void
    {
        $this->validateOnly($field, $this->rules);

        if (Str::contains($field, 'password')) {
            return;
        }

        if (Str::beforeLast($field, '.') === 'user') {
            if (Str::afterLast($field, '.') === 'connected_account' && ! is_numeric($value)) {
                $value = null;
            }

            $this->user->update([
                Str::afterLast($field, '.') => $value,
            ]);

            return;
        }

        if (Str::beforeLast($field, '.') === 'operatorData') {
            $this->operatorData->update([
                Str::afterLast($field, '.') => $value,
            ]);

            return;
        }
    }

    public function store()
    {
        try {
            $this->validate();
        } catch (ValidationException $e) {
            $this->emit('errorEvent', collect($e->errors())->first()[0]);

            return null;
        }

        $this->user->type = 'operator';
        $this->user->password = bcrypt($this->user->password);

        if ($this->user->connected_account == 'null') {
            $this->user->connected_account = null;
        }

        if (empty($this->user->username) && ! empty($this->user->connected_account)) {
            $connected_username = User::query()->where('id', (int) $this->user->connected_account)->first()->username;
            $this->user->username = $connected_username;
        }

        $this->user->save();

        if ($this->user->connected_account != null) {
            return redirect()->route('admin.operators.list');
        }

        $this->operatorData->user_id = $this->user->id;
        $this->operatorData->save();

        foreach ($this->files as $file) {
            $this->operatorData->files()->create([
                'filename' => $file->getClientOriginalName(),
                'path' => $file->store('operator-data-files/'.$this->operatorData->id, 'private'),
            ]);
        }

        if (! empty($this->company_phones)) {
            foreach ($this->company_phones as $company_phone) {
                $this->operatorData->company_phones()->create([
                    'phone' => $company_phone['phone'],
                ]);
            }
        }

        return redirect()->route('admin.operators.list');
    }

    public function addCompanyPhone(): void
    {
        $this->company_phones[] = [
            'phone' => '',
        ];
    }

    public function deleteFile($id): void
    {
        unset($this->temp_files[$id]);
        $this->files = $this->temp_files;
    }
}
