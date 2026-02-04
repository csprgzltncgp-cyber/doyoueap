<?php

namespace App\Models\EapOnline;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * App\Models\EapOnline\EapResult
 *
 * @property int $id
 * @property int $quiz_id
 * @property int $from
 * @property int $to
 * @property-read EapQuiz|null $eap_quiz
 * @property-read Collection<int, EapTranslation> $translations
 * @property-read int|null $translations_count
 *
 * @method static Builder|EapResult newModelQuery()
 * @method static Builder|EapResult newQuery()
 * @method static Builder|EapResult query()
 * @method static Builder|EapResult whereFrom($value)
 * @method static Builder|EapResult whereId($value)
 * @method static Builder|EapResult whereQuizId($value)
 * @method static Builder|EapResult whereTo($value)
 *
 * @mixin \Eloquent
 */
class EapResult extends Model
{
    protected $connection = 'mysql_eap_online';

    protected $table = 'results';

    protected $guarded = [];

    public $timestamps = false;

    public static function boot(): void
    {
        parent::boot();

        self::deleting(function ($result): void {
            $result->translations()->delete();
        });
    }

    public function eap_quiz(): BelongsTo
    {
        return $this->belongsTo(EapQuiz::class, 'quiz_id', 'id');
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
