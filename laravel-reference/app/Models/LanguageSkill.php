<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

/**
 * App\Models\LanguageSkill
 *
 * @property int $id
 * @property string $slug
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read Translation|null $translation
 *
 * @method static Builder|LanguageSkill newModelQuery()
 * @method static Builder|LanguageSkill newQuery()
 * @method static Builder|LanguageSkill query()
 * @method static Builder|LanguageSkill whereCreatedAt($value)
 * @method static Builder|LanguageSkill whereDeletedAt($value)
 * @method static Builder|LanguageSkill whereId($value)
 * @method static Builder|LanguageSkill whereSlug($value)
 * @method static Builder|LanguageSkill whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class LanguageSkill extends Model
{
    use HasFactory;

    public function translation(): MorphOne
    {
        $language_id = Auth::user() !== null ? Auth::user()->language_id : 3;

        // Check if tranlation exists in the user's language
        if ($this->translations->where('language_id', $language_id)->first()) {
            return $this->morphOne(Translation::class, 'translatable')->where('language_id', $language_id)->select('value');
        }

        // Return english translation
        return $this->morphOne(Translation::class, 'translatable')->where('language_id', 3)->select('value');
    }

    public function translations(): MorphMany
    {
        return $this->morphMany(Translation::class, 'translatable')->select('value', 'language_id');
    }
}
