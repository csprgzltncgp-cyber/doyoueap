<?php

namespace App\Http\Livewire\Admin;

use App\Models\CaseInput;
use App\Models\Language;
use App\Models\Translation;
use Illuminate\Support\Str;
use Livewire\Component;

class CaseInputComponent extends Component
{
    public $caseInput;

    public $company;

    public $languages;

    public $is_translations_open = false;

    protected function rules(): array
    {
        return [
            'caseInput.name' => ['required'],
            'caseInput.company_id' => ['nullable', 'integer'],
            'caseInput.delete_later' => ['boolean'],
            'caseInput.type' => ['required'],
        ];
    }

    public function mount($caseInput): void
    {
        $this->languages = Language::query()->get();
        $this->caseInput = $caseInput;
    }

    public function updated($propertyName, $value): void
    {
        if (Str::contains($propertyName, 'name')) {
            Translation::query()->updateOrCreate([
                'language_id' => 1,
                'translatable_type' => CaseInput::class,
                'translatable_id' => $this->caseInput->id,
            ], [
                'value' => $value,
            ]);
        }

        if (Str::contains($propertyName, 'company_id')) {
            $this->caseInput->company_id = $value ? $this->company->id : null;
        }

        $this->caseInput->save();
    }

    public function render()
    {
        return view('livewire.admin.case-input-component');
    }

    public function toggleTranslations(): void
    {
        $this->is_translations_open = ! $this->is_translations_open;
    }

    public function delete()
    {
        $this->is_translations_open = false;
        $this->caseInput->allTranslations()->delete();
        $this->caseInput->delete();

        return $this->emit('refreshCompanyInputs');
    }
}
