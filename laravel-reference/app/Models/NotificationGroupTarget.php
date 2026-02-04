<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * App\Models\NotificationGroupTarget
 *
 * @property int $id
 * @property int $notification_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, NotificationGroupTargetCountry> $countries
 * @property-read int|null $countries_count
 * @property-read Notification $notification
 * @property-read Collection<int, NotificationGroupTargetPermission> $permissions
 * @property-read int|null $permissions_count
 * @property-read Collection<int, NotificationGroupTargetUserType> $userTypes
 * @property-read int|null $user_types_count
 *
 * @method static Builder|NotificationGroupTarget newModelQuery()
 * @method static Builder|NotificationGroupTarget newQuery()
 * @method static Builder|NotificationGroupTarget onlyTrashed()
 * @method static Builder|NotificationGroupTarget query()
 * @method static Builder|NotificationGroupTarget whereCreatedAt($value)
 * @method static Builder|NotificationGroupTarget whereDeletedAt($value)
 * @method static Builder|NotificationGroupTarget whereId($value)
 * @method static Builder|NotificationGroupTarget whereNotificationId($value)
 * @method static Builder|NotificationGroupTarget whereUpdatedAt($value)
 * @method static Builder|NotificationGroupTarget withTrashed()
 * @method static Builder|NotificationGroupTarget withoutTrashed()
 *
 * @mixin \Eloquent
 */
class NotificationGroupTarget extends Model
{
    use SoftDeletes;

    protected $table = 'notification_group_targets';

    public function countries(): HasMany
    {
        return $this->hasMany(NotificationGroupTargetCountry::class, 'group_target_id');
    }

    public function userTypes(): HasMany
    {
        return $this->hasMany(NotificationGroupTargetUserType::class, 'group_target_id');
    }

    public function permissions(): HasMany
    {
        return $this->hasMany(NotificationGroupTargetPermission::class, 'group_target_id');
    }

    public function notification(): BelongsTo
    {
        return $this->belongsTo(Notification::class, 'notification_id', 'id');
    }
}
