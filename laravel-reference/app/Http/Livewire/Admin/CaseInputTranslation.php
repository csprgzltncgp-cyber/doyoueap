<?php

namespace App\Http\Livewire\Admin;

use App\Models\CaseInput;
use App\Models\Language;
use App\Models\Translation;
use Livewire\Component;

class CaseInputTranslation extends Component
{
    public $caseInput;

    public $translation;

    public $language;

    protected $rules = [
        'translation.value' => ['string', 'nullable'],
    ];

    public function mount(CaseInput $caseInput, Language $language): void
    {
        $this->caseInput = $caseInput;
        $this->language = $language;
        $this->translation = Translation::query()->firstOrCreate([
            'language_id' => $language->id,
            'translatable_type' => CaseInput::class,
            'translatable_id' => $caseInput->id,
        ]);
    }

    public function updated(): void
    {
        $this->translation->save();
    }

    public function render()
    {
        return view('livewire.admin.case-input-translation');
    }
}
