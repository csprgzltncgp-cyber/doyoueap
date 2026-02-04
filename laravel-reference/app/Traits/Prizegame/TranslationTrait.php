<?php

namespace App\Traits\Prizegame;

use App\Models\EapOnline\EapLanguage;
use App\Models\EapOnline\EapTranslation;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait TranslationTrait
{
    public function translations(): MorphMany
    {
        return $this->morphMany(EapTranslation::class, 'translatable');
    }

    public function get_translation(EapLanguage $language)
    {
        if ($this->translations->count()) {
            return $this->translations->where('language_id', $language->id)->first()->value;
        }

        return $this->translations()->where('language_id', $language->id)->first()->value;
    }

    public function has_translation(EapLanguage $language): bool
    {
        if ($this->translations->count()) {
            return $this->translations->where('language_id', $language->id)->count() && $this->translations->where('language_id', $language->id)->first()->value !== '';
        }

        return $this->translations()->where('language_id', $language->id)->exists() && $this->translations()->where('language_id', $language->id)->first()->value !== '';
    }
}
