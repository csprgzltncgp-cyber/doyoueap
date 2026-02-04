<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityPlanCategoryCaseValue extends Model
{
    protected $fillable = [
        'activity_plan_category_case_id',
        'activity_plan_category_field_id',
        'value',
    ];

    /**
     * Get the activity plan category case.
     *
     * @return BelongsTo<ActivityPlanCategoryCase,ActivityPlanCategoryCaseValue>
     */
    public function activity_plan_category_case(): BelongsTo
    {
        return $this->belongsTo(ActivityPlanCategoryCase::class);
    }

    /**
     * Get the activity plan category field.
     *
     * @return BelongsTo<ActivityPlanCategoryField,ActivityPlanCategoryCaseValue>
     */
    public function activity_plan_category_field(): BelongsTo
    {
        return $this->belongsTo(ActivityPlanCategoryField::class);
    }
}
