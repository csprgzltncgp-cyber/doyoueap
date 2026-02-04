<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * App\Models\NotificationSeen
 *
 * @property int $id
 * @property int $user_id
 * @property int $notification_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read User $user
 *
 * @method static Builder|NotificationSeen newModelQuery()
 * @method static Builder|NotificationSeen newQuery()
 * @method static Builder|NotificationSeen onlyTrashed()
 * @method static Builder|NotificationSeen query()
 * @method static Builder|NotificationSeen whereCreatedAt($value)
 * @method static Builder|NotificationSeen whereDeletedAt($value)
 * @method static Builder|NotificationSeen whereId($value)
 * @method static Builder|NotificationSeen whereNotificationId($value)
 * @method static Builder|NotificationSeen whereUpdatedAt($value)
 * @method static Builder|NotificationSeen whereUserId($value)
 * @method static Builder|NotificationSeen withTrashed()
 * @method static Builder|NotificationSeen withoutTrashed()
 *
 * @mixin \Eloquent
 */
class NotificationSeen extends Model
{
    use SoftDeletes;

    protected $table = 'notification_seen';

    protected $fillable = ['user_id', 'notification_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
