<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * App\Models\NotificationGroupTargetPermission
 *
 * @property int $id
 * @property int $permission_id
 * @property int $group_target_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 *
 * @method static Builder|NotificationGroupTargetPermission newModelQuery()
 * @method static Builder|NotificationGroupTargetPermission newQuery()
 * @method static Builder|NotificationGroupTargetPermission onlyTrashed()
 * @method static Builder|NotificationGroupTargetPermission query()
 * @method static Builder|NotificationGroupTargetPermission whereCreatedAt($value)
 * @method static Builder|NotificationGroupTargetPermission whereDeletedAt($value)
 * @method static Builder|NotificationGroupTargetPermission whereGroupTargetId($value)
 * @method static Builder|NotificationGroupTargetPermission whereId($value)
 * @method static Builder|NotificationGroupTargetPermission wherePermissionId($value)
 * @method static Builder|NotificationGroupTargetPermission whereUpdatedAt($value)
 * @method static Builder|NotificationGroupTargetPermission withTrashed()
 * @method static Builder|NotificationGroupTargetPermission withoutTrashed()
 *
 * @mixin \Eloquent
 */
class NotificationGroupTargetPermission extends Model
{
    use SoftDeletes;

    protected $table = 'notification_group_targets_permissions';
}
