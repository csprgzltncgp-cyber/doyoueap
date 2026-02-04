<?php

namespace App\Models\PrizeGame;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\PrizeGame\Digit
 *
 * @property int $id
 * @property int $value
 * @property int $order
 * @property int $content_id
 * @property int|null $question_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Content|null $content
 * @property-read Question|null $question
 *
 * @method static Builder|Digit newModelQuery()
 * @method static Builder|Digit newQuery()
 * @method static Builder|Digit query()
 * @method static Builder|Digit whereContentId($value)
 * @method static Builder|Digit whereCreatedAt($value)
 * @method static Builder|Digit whereId($value)
 * @method static Builder|Digit whereOrder($value)
 * @method static Builder|Digit whereQuestionId($value)
 * @method static Builder|Digit whereUpdatedAt($value)
 * @method static Builder|Digit whereValue($value)
 *
 * @mixin \Eloquent
 */
class Digit extends Model
{
    protected $guarded = [];

    protected $connection = 'mysql_eap_online';

    protected $table = 'prizegame_digits';

    public function content(): BelongsTo
    {
        return $this->belongsTo(Content::class);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}
