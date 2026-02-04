<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ActivityPlanCategory extends Model
{
    protected $fillable = [
        'company_id',
        'name',
    ];

    protected static function booted(): void
    {
        static::deleting(function (ActivityPlanCategory $activity_plan_category): void {
            $activity_plan_category->cases()->delete();
            $activity_plan_category->fields()->delete();
        });
    }

    /**
     * @return BelongsTo<Company,ActivityPlanCategory>
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * @return HasMany<ActivityPlanCategoryField>
     */
    public function fields(): HasMany
    {
        return $this->hasMany(ActivityPlanCategoryField::class);
    }

    /**
     * @return HasMany<ActivityPlanCategoryCase>
     */
    public function cases(): HasMany
    {
        return $this->hasMany(ActivityPlanCategoryCase::class);
    }
}
