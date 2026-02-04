<?php

namespace App\Models\EapOnline;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * App\Models\EapOnline\EapAnswer
 *
 * @property int $id
 * @property int $question_id
 * @property int $point
 * @property-read EapQuestion|null $eap_question
 * @property-read Collection<int, EapTranslation> $translations
 * @property-read int|null $translations_count
 *
 * @method static Builder|EapAnswer newModelQuery()
 * @method static Builder|EapAnswer newQuery()
 * @method static Builder|EapAnswer query()
 * @method static Builder|EapAnswer whereId($value)
 * @method static Builder|EapAnswer wherePoint($value)
 * @method static Builder|EapAnswer whereQuestionId($value)
 *
 * @mixin \Eloquent
 */
class EapAnswer extends Model
{
    protected $connection = 'mysql_eap_online';

    protected $table = 'answers';

    protected $guarded = [];

    public $timestamps = false;

    public static function boot(): void
    {
        parent::boot();

        self::deleting(function ($answer): void {
            $answer->translations()->delete();
        });
    }

    public function eap_question(): BelongsTo
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
