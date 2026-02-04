<?php

namespace App\Http\Livewire\Admin\Operator;

use App\Models\Country;
use App\Models\Language;
use App\Models\OperatorData;
use App\Models\User;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class Edit extends Component
{
    use WithFileUploads;

    public User $user;

    public OperatorData $operatorData;

    public $company_phones;

    public $files;

    public $newPassword;

    public $newPassowordConfirmation;

    protected $rules = [
        'user.name' => 'required|string',
        'user.username' => 'required|string',
        'user.password' => 'nullable|string',
        'user.country_id' => 'required|exists:countries,id',
        'user.language_id' => 'required|exists:languages,id',
        'user.connected_account' => 'string',
        'operatorData.position' => 'required',
        'operatorData.employment_type' => 'required',
        'operatorData.invoincing_name' => 'string',
        'operatorData.start_of_employment' => 'string',
        'operatorData.invoincing_post_code' => 'string',
        'operatorData.invoincing_country' => 'string',
        'operatorData.invoincing_city' => 'string',
        'operatorData.invoincing_street' => 'string',
        'operatorData.invoincing_house_number' => 'string',
        'operatorData.tax_number' => 'string',
        'operatorData.salary' => 'nullable|string',
        'operatorData.salary_currency' => 'string',
        'operatorData.eap_chat_username' => 'string',
        'operatorData.eap_chat_password' => 'string',
        'operatorData.private_email' => 'string',
        'operatorData.private_phone' => 'string',
        'operatorData.company_email' => 'string',
        'operatorData.company_phone' => 'string',
        'operatorData.language' => 'string',
        'operatorData.bank_account_number' => 'string',
        'company_phones.*.phone' => 'string',
        'files.*' => 'file|max:10240',
    ];

    public function mount(User $user): void
    {
        $this->user = $user;
        $this->operatorData = OperatorData::query()->firstOrCreate(['user_id' => $user->id]);
    }

    public function render()
    {
        $this->operatorData->load(['company_phones', 'files']);
        $this->company_phones = $this->operatorData->company_phones;
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

        return view('livewire.admin.operator.edit', ['countries' => $countries, 'languages' => $languages, 'users' => $users, 'positions' => $positions, 'employment_types' => $employment_types])->extends('layout.master');
    }

    public function updatedFiles(): void
    {
        $this->validate([
            'files.*' => 'file|max:10240',
        ]);

        foreach ($this->files as $file) {
            $this->operatorData->files()->create([
                'filename' => $file->getClientOriginalName(),
                'path' => $file->store('operator-data-files/'.$this->operatorData->id, 'private'),
            ]);
        }

        $this->operatorData->load(['files']);
    }

    public function deleteFile($id): void
    {
        $file = $this->operatorData->files()->findOrFail($id);
        $file->delete();
        $this->operatorData->load(['files']);
    }

    public function downloadFile($id)
    {
        $file = $this->operatorData->files()->findOrFail($id);

        return response()->download(storage_path('app/'.$file->path), $file->filename);
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

        if (Str::before($field, '.') === 'company_phones') {
            $model_id = $this->company_phones[Str::before(Str::after($field, '.'), '.')]['id'];
            $this->operatorData->company_phones()->where('id', $model_id)->update([
                'phone' => $value,
            ]);

            return;
        }
    }

    public function updatePassword(): void
    {
        $this->validate([
            'newPassword' => 'required|string',
            'newPassowordConfirmation' => 'required|string|same:newPassword',
        ]);

        $this->user->update([
            'password' => bcrypt($this->newPassword),
        ]);

        $this->newPassword = null;
        $this->newPassowordConfirmation = null;

        $this->emit('operatorUpdated');
    }

    public function addCompanyPhone(): void
    {
        $this->operatorData->company_phones()->create([
            'phone' => '',
        ]);

        $this->company_phones = $this->operatorData->company_phones;
        $this->operatorData->load('company_phones');
    }

    public function update(): void
    {
        $this->emit('operatorUpdated');
    }
}
