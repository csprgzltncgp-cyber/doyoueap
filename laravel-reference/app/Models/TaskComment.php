<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\TaskComment
 *
 * @property int $id
 * @property int $task_id
 * @property int|null $user_id
 * @property string $value
 * @property bool $seen
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Task|null $task
 * @property-read User|null $user
 *
 * @method static Builder|TaskComment newModelQuery()
 * @method static Builder|TaskComment newQuery()
 * @method static Builder|TaskComment query()
 * @method static Builder|TaskComment whereCreatedAt($value)
 * @method static Builder|TaskComment whereId($value)
 * @method static Builder|TaskComment whereSeen($value)
 * @method static Builder|TaskComment whereTaskId($value)
 * @method static Builder|TaskComment whereUpdatedAt($value)
 * @method static Builder|TaskComment whereUserId($value)
 * @method static Builder|TaskComment whereValue($value)
 *
 * @mixin \Eloquent
 */
class TaskComment extends Model
{
    protected $guarded = [];

    protected $casts = [
        'seen' => 'boolean',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function is_from_creator(): bool
    {
        return $this->user->id == $this->task->from_id;
    }
}
