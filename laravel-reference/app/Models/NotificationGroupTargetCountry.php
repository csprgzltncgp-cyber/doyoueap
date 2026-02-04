<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * App\Models\NotificationGroupTargetCountry
 *
 * @property int $id
 * @property int $country_id
 * @property int $group_target_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 *
 * @method static Builder|NotificationGroupTargetCountry newModelQuery()
 * @method static Builder|NotificationGroupTargetCountry newQuery()
 * @method static Builder|NotificationGroupTargetCountry onlyTrashed()
 * @method static Builder|NotificationGroupTargetCountry query()
 * @method static Builder|NotificationGroupTargetCountry whereCountryId($value)
 * @method static Builder|NotificationGroupTargetCountry whereCreatedAt($value)
 * @method static Builder|NotificationGroupTargetCountry whereDeletedAt($value)
 * @method static Builder|NotificationGroupTargetCountry whereGroupTargetId($value)
 * @method static Builder|NotificationGroupTargetCountry whereId($value)
 * @method static Builder|NotificationGroupTargetCountry whereUpdatedAt($value)
 * @method static Builder|NotificationGroupTargetCountry withTrashed()
 * @method static Builder|NotificationGroupTargetCountry withoutTrashed()
 *
 * @mixin \Eloquent
 */
class NotificationGroupTargetCountry extends Model
{
    use SoftDeletes;

    protected $table = 'notification_group_targets_countries';
}
