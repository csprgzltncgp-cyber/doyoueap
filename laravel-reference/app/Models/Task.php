<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Task
 *
 * @property int $id
 * @property string $description
 * @property int $from_id
 * @property int $to_id
 * @property string $deadline
 * @property int $status
 * @property int $confirmed
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $title
 * @property-read Collection<int, TaskAttachment> $attachments
 * @property-read int|null $attachments_count
 * @property-read Collection<int, TaskComment> $comments
 * @property-read int|null $comments_count
 * @property-read Collection<int, User> $connected_users
 * @property-read int|null $connected_users_count
 * @property-read User|null $from
 * @property-read User|null $to
 *
 * @method static Builder|Task newModelQuery()
 * @method static Builder|Task newQuery()
 * @method static Builder|Task query()
 * @method static Builder|Task whereConfirmed($value)
 * @method static Builder|Task whereCreatedAt($value)
 * @method static Builder|Task whereDeadline($value)
 * @method static Builder|Task whereDescription($value)
 * @method static Builder|Task whereFromId($value)
 * @method static Builder|Task whereId($value)
 * @method static Builder|Task whereStatus($value)
 * @method static Builder|Task whereTitle($value)
 * @method static Builder|Task whereToId($value)
 * @method static Builder|Task whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Task extends Model
{
    final public const STATUS_CREATED = 1;

    final public const STATUS_OPENED = 2;

    final public const STATUS_COMPLETED = 3;

    protected $guarded = [];

    public static function boot(): void
    {
        parent::boot();
        static::deleting(function ($task): void {
            foreach ($task->comments as $comment) {
                $comment->delete();
            }
        });
    }

    public function from(): BelongsTo
    {
        return $this->belongsTo(User::class, 'from_id', 'id');
    }

    public function to(): BelongsTo
    {
        return $this->belongsTo(User::class, 'to_id', 'id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(TaskComment::class, 'task_id', 'id');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(TaskAttachment::class, 'task_id', 'id');
    }

    public function connected_users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'task_users', 'task_id', 'user_id');
    }

    public function is_over_deadline(): bool
    {
        return Carbon::parse($this->deadline)->isPast() && ! Carbon::parse($this->deadline)->isCurrentDay() && $this->status != self::STATUS_COMPLETED;
    }

    public function is_last_day(): bool
    {
        return Carbon::parse($this->deadline)->isCurrentDay() && $this->status != self::STATUS_COMPLETED;
    }

    public function is_new(): bool
    {
        return $this->status == self::STATUS_CREATED;
    }

    public function has_new_comments(): bool
    {
        return $this->comments()->where('user_id', '!=', auth()->id())->where('seen', false)->count() > 0;
    }
}
