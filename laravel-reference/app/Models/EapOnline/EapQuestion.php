<?php

namespace App\Models\EapOnline;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * App\Models\EapOnline\EapQuestion
 *
 * @property int $id
 * @property int $quiz_id
 * @property-read Collection<int, EapAnswer> $eap_answers
 * @property-read int|null $eap_answers_count
 * @property-read EapQuiz|null $eap_quiz
 * @property-read Collection<int, EapTranslation> $translations
 * @property-read int|null $translations_count
 *
 * @method static Builder|EapQuestion newModelQuery()
 * @method static Builder|EapQuestion newQuery()
 * @method static Builder|EapQuestion query()
 * @method static Builder|EapQuestion whereId($value)
 * @method static Builder|EapQuestion whereQuizId($value)
 *
 * @mixin \Eloquent
 */
class EapQuestion extends Model
{
    protected $connection = 'mysql_eap_online';

    protected $table = 'questions';

    protected $guarded = [];

    public $timestamps = false;

    public static function boot(): void
    {
        parent::boot();

        self::deleting(function ($question): void {
            $question->eap_answers()->each(function ($answer): void {
                $answer->delete();
            });
            $question->translations()->delete();
        });
    }

    public function eap_quiz(): BelongsTo
    {
        return $this->belongsTo(EapQuiz::class, 'quiz_id', 'id');
    }

    public function eap_answers(): HasMany
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
