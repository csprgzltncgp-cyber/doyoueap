<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\TaskCompletionPoint
 *
 * @property int $id
 * @property int $user_id
 * @property int|null $task_id
 * @property int $type
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $user
 *
 * @method static Builder|TaskCompletionPoint newModelQuery()
 * @method static Builder|TaskCompletionPoint newQuery()
 * @method static Builder|TaskCompletionPoint query()
 * @method static Builder|TaskCompletionPoint whereCreatedAt($value)
 * @method static Builder|TaskCompletionPoint whereId($value)
 * @method static Builder|TaskCompletionPoint whereTaskId($value)
 * @method static Builder|TaskCompletionPoint whereType($value)
 * @method static Builder|TaskCompletionPoint whereUpdatedAt($value)
 * @method static Builder|TaskCompletionPoint whereUserId($value)
 *
 * @mixin \Eloquent
 */
class TaskCompletionPoint extends Model
{
    final public const TYPE_OVER_DEADLINE = 1;

    final public const TYPE_LAST_DAY = 2;

    final public const TYPE_WITHIN_DEADLINE = 3;

    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
