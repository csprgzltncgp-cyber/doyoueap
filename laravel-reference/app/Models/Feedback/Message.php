<?php

namespace App\Models\Feedback;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\Feedback\Message
 *
 * @property int $id
 * @property int $feedback_id
 * @property int $type
 * @property string $value
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Feedback|null $feedback
 *
 * @method static Builder|Message newModelQuery()
 * @method static Builder|Message newQuery()
 * @method static Builder|Message query()
 * @method static Builder|Message whereCreatedAt($value)
 * @method static Builder|Message whereFeedbackId($value)
 * @method static Builder|Message whereId($value)
 * @method static Builder|Message whereType($value)
 * @method static Builder|Message whereUpdatedAt($value)
 * @method static Builder|Message whereValue($value)
 *
 * @mixin \Eloquent
 */
class Message extends Model
{
    final public const TYPE_CLIENT = 1;

    final public const TYPE_ADMIN = 2;

    protected $connection = 'mysql_feedback';

    protected $table = 'messages';

    protected $fillable = [
        'type',
        'value',
    ];

    public function feedback(): BelongsTo
    {
        return $this->belongsTo(Feedback::class);
    }
}
