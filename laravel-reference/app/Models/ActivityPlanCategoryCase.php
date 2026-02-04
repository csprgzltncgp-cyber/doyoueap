<?php

namespace App\Models;

use App\Enums\ActivityPlanCategoryCaseStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class ActivityPlanCategoryCase extends Model
{
    protected $fillable = [
        'activity_plan_category_id',
        'country_id',
        'company_id',
        'closed_at',
        'status',
    ];

    protected $casts = [
        'closed_at' => 'datetime',
        'status' => ActivityPlanCategoryCaseStatusEnum::class,
    ];

    protected static function booted(): void
    {
        static::deleting(function (ActivityPlanCategoryCase $case): void {
            $case->activity_plan_members()->delete();
            $case->activity_plan_category_case_values()->delete();
        });
    }

    /**
     * Get the activity plan category.
     *
     * @return BelongsTo<ActivityPlanCategory,ActivityPlanCategoryCase>
     */
    public function activity_plan_category(): BelongsTo
    {
        return $this->belongsTo(ActivityPlanCategory::class);
    }

    /**
     * @return HasMany<ActivityPlanCategoryCaseValue>
     */
    public function activity_plan_category_case_values(): HasMany
    {
        return $this->hasMany(ActivityPlanCategoryCaseValue::class);
    }

    /**
     * @return MorphMany<ActivityPlanMember>
     */
    public function activity_plan_members(): MorphMany
    {
        return $this->morphMany(ActivityPlanMember::class, 'activity_plan_memberable');
    }
}
