<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * App\Models\NotificationGroupTargetUserType
 *
 * @property int $id
 * @property string $type
 * @property int $group_target_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 *
 * @method static Builder|NotificationGroupTargetUserType newModelQuery()
 * @method static Builder|NotificationGroupTargetUserType newQuery()
 * @method static Builder|NotificationGroupTargetUserType onlyTrashed()
 * @method static Builder|NotificationGroupTargetUserType query()
 * @method static Builder|NotificationGroupTargetUserType whereCreatedAt($value)
 * @method static Builder|NotificationGroupTargetUserType whereDeletedAt($value)
 * @method static Builder|NotificationGroupTargetUserType whereGroupTargetId($value)
 * @method static Builder|NotificationGroupTargetUserType whereId($value)
 * @method static Builder|NotificationGroupTargetUserType whereType($value)
 * @method static Builder|NotificationGroupTargetUserType whereUpdatedAt($value)
 * @method static Builder|NotificationGroupTargetUserType withTrashed()
 * @method static Builder|NotificationGroupTargetUserType withoutTrashed()
 *
 * @mixin \Eloquent
 */
class NotificationGroupTargetUserType extends Model
{
    use SoftDeletes;

    protected $fillable = ['type', 'group_target_id'];

    protected $table = 'notification_group_targets_user_type';
}
