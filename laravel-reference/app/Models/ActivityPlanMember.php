<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int $id
 * @property int $activity_plan_id
 * @property int $activity_plan_memberable_id
 * @property string $activity_plan_memberable_type
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class ActivityPlanMember extends Model
{
    protected $fillable = [
        'activity_plan_id',
        'activity_plan_memberable_id',
        'activity_plan_memberable_type',
    ];

    public function activity_plan_memberable(): MorphTo
    {
        return $this->morphTo();
    }
}
