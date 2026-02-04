<?php

namespace App\Models\EapOnline\Assessment;

use App\Models\EapOnline\EapTranslation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\EapOnline\Assessment\EapType
 *
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, EapQuestion> $eap_assessment_questions
 * @property-read int|null $eap_assessment_questions_count
 * @property-read Collection<int, EapResult> $eap_assessment_rersults
 * @property-read int|null $eap_assessment_rersults_count
 * @property-read Collection<int, EapTranslation> $translations
 * @property-read int|null $translations_count
 *
 * @method static Builder|EapType newModelQuery()
 * @method static Builder|EapType newQuery()
 * @method static Builder|EapType query()
 * @method static Builder|EapType whereCreatedAt($value)
 * @method static Builder|EapType whereId($value)
 * @method static Builder|EapType whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class EapType extends Model
{
    protected $connection = 'mysql_eap_online';

    protected $table = 'assessment_types';

    protected $guarded = [];

    public function translations(): MorphMany
    {
        return $this->morphMany(EapTranslation::class, 'translatable');
    }

    public function eap_assessment_rersults(): HasMany
    {
        return $this->hasMany(EapResult::class, 'type_id', 'id');
    }

    public function eap_assessment_questions(): HasMany
    {
        return $this->hasMany(EapQuestion::class, 'type_id', 'id');
    }

    public function hasTranslation($language_id)
    {
        return $this->morphOne(EapTranslation::class, 'translatable')->where('language_id', $language_id)->exists();
    }
}
