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
 * App\Models\EapOnline\Assessment\EapQuestion
 *
 * @property int $id
 * @property int $type
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, EapAnswer> $eap_assessment_answers
 * @property-read int|null $eap_assessment_answers_count
 * @property-read Collection<int, EapTranslation> $translations
 * @property-read int|null $translations_count
 *
 * @method static Builder|EapQuestion newModelQuery()
 * @method static Builder|EapQuestion newQuery()
 * @method static Builder|EapQuestion query()
 * @method static Builder|EapQuestion whereCreatedAt($value)
 * @method static Builder|EapQuestion whereId($value)
 * @method static Builder|EapQuestion whereType($value)
 * @method static Builder|EapQuestion whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class EapQuestion extends Model
{
    protected $connection = 'mysql_eap_online';

    protected $table = 'assessment_questions';

    protected $guarded = [];

    public function eap_assessment_answers(): HasMany
    {
        return $this->hasMany(EapAnswer::class, 'question_id', 'id');
    }

    public function translations(): MorphMany
    {
        return $this->morphMany(EapTranslation::class, 'translatable');
    }

    public function hasTranslation($language_id)
    {
        return $this->morphOne(EapTranslation::class, 'translatable')->where('language_id', $language_id)->exists();
    }
}
