<?php

namespace App\Models\PrizeGame;

use App\Models\EapOnline\EapTranslation;
use App\Traits\Prizegame\TranslationTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * App\Models\PrizeGame\Question
 *
 * @property int $id
 * @property string $value
 * @property int $content_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Answer> $answers
 * @property-read int|null $answers_count
 * @property-read Content|null $content
 * @property-read Digit|null $digit
 *
 * @method static Builder|Question newModelQuery()
 * @method static Builder|Question newQuery()
 * @method static Builder|Question query()
 * @method static Builder|Question whereContentId($value)
 * @method static Builder|Question whereCreatedAt($value)
 * @method static Builder|Question whereId($value)
 * @method static Builder|Question whereUpdatedAt($value)
 * @method static Builder|Question whereValue($value)
 *
 * @property-read Collection<int, EapTranslation> $translations
 * @property-read int|null $translations_count
 *
 * @mixin \Eloquent
 */
class Question extends Model
{
    use TranslationTrait;

    protected $guarded = [];

    protected $connection = 'mysql_eap_online';

    protected $table = 'prizegame_questions';

    public static function boot(): void
    {
        parent::boot();

        self::deleting(function (self $question): void {
            $question->translations()->delete();
        });
    }

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }

    public function content(): BelongsTo
    {
        return $this->belongsTo(Content::class);
    }

    public function digit(): HasOne
    {
        return $this->hasOne(Digit::class);
    }
}
