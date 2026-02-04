<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\NotificationInvidualTarget
 *
 * @property int $id
 * @property int $user_id
 * @property int $notification_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read Notification $notification
 *
 * @method static Builder|NotificationInvidualTarget newModelQuery()
 * @method static Builder|NotificationInvidualTarget newQuery()
 * @method static Builder|NotificationInvidualTarget query()
 * @method static Builder|NotificationInvidualTarget whereCreatedAt($value)
 * @method static Builder|NotificationInvidualTarget whereDeletedAt($value)
 * @method static Builder|NotificationInvidualTarget whereId($value)
 * @method static Builder|NotificationInvidualTarget whereNotificationId($value)
 * @method static Builder|NotificationInvidualTarget whereUpdatedAt($value)
 * @method static Builder|NotificationInvidualTarget whereUserId($value)
 *
 * @mixin \Eloquent
 */
class NotificationInvidualTarget extends Model
{
    protected $table = 'notification_invidual_targets';

    public function notification(): BelongsTo
    {
        return $this->belongsTo(Notification::class, 'notification_id', 'id');
    }
}
