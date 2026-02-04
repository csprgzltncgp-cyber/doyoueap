<?php

namespace App\Models\EapOnline\WellBeing;

use App\Models\EapOnline\EapTranslation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\EapOnline\WellBeing\EapAnswer
 *
 * @property int $id
 * @property int $point
 * @property int $question_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read EapQuestion|null $eap_assessment_question
 * @property-read Collection<int, EapTranslation> $translations
 * @property-read int|null $translations_count
 *
 * @method static Builder|EapAnswer newModelQuery()
 * @method static Builder|EapAnswer newQuery()
 * @method static Builder|EapAnswer query()
 * @method static Builder|EapAnswer whereCreatedAt($value)
 * @method static Builder|EapAnswer whereId($value)
 * @method static Builder|EapAnswer wherePoint($value)
 * @method static Builder|EapAnswer whereQuestionId($value)
 * @method static Builder|EapAnswer whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class EapAnswer extends Model
{
    protected $connection = 'mysql_eap_online';

    protected $table = 'well_being_answers';

    protected $guarded = [];

    public function eap_assessment_question(): BelongsTo
    {
        return $this->belongsTo(EapQuestion::class, 'question_id', 'id');
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
