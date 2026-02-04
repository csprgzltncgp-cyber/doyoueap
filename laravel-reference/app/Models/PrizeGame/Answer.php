<?php

namespace App\Models\PrizeGame;

use App\Models\EapOnline\EapTranslation;
use App\Traits\Prizegame\TranslationTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\PrizeGame\Answer
 *
 * @property int $id
 * @property string $value
 * @property int $question_id
 * @property bool $correct
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Question|null $question
 *
 * @method static Builder|Answer newModelQuery()
 * @method static Builder|Answer newQuery()
 * @method static Builder|Answer query()
 * @method static Builder|Answer whereCorrect($value)
 * @method static Builder|Answer whereCreatedAt($value)
 * @method static Builder|Answer whereId($value)
 * @method static Builder|Answer whereQuestionId($value)
 * @method static Builder|Answer whereUpdatedAt($value)
 * @method static Builder|Answer whereValue($value)
 *
 * @property-read Collection<int, EapTranslation> $translations
 * @property-read int|null $translations_count
 *
 * @mixin \Eloquent
 */
class Answer extends Model
{
    use TranslationTrait;

    protected $guarded = [];

    protected $connection = 'mysql_eap_online';

    protected $table = 'prizegame_answers';

    protected $casts = [
        'correct' => 'boolean',
    ];

    public static function boot(): void
    {
        parent::boot();

        self::deleting(function (self $answer): void {
            $answer->translations()->delete();
        });
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}
