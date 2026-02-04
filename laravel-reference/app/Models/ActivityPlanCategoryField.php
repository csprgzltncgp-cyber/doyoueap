<?php

namespace App\Models;

use App\Enums\ActivityPlanCategoryFieldTypeEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityPlanCategoryField extends Model
{
    protected $fillable = [
        'activity_plan_category_id',
        'name',
        'type',
        'is_highlighted',
    ];

    protected $casts = [
        'type' => ActivityPlanCategoryFieldTypeEnum::class,
        'is_highlighted' => 'boolean',
    ];

    /**
     * @return BelongsTo<ActivityPlanCategory,ActivityPlanCategoryField>
     */
    public function activity_plan_category(): BelongsTo
    {
        return $this->belongsTo(ActivityPlanCategory::class);
    }
}
