<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\CustomerSatisfaction
 *
 * @property int $id
 * @property Carbon $from
 * @property Carbon $to
 * @property int $company_id
 * @property bool $is_active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Company $company
 * @property-read Collection<int, CustomerSatisfactionValue> $values
 * @property-read int|null $values_count
 *
 * @method static Builder|CustomerSatisfaction newModelQuery()
 * @method static Builder|CustomerSatisfaction newQuery()
 * @method static Builder|CustomerSatisfaction query()
 * @method static Builder|CustomerSatisfaction whereCompanyId($value)
 * @method static Builder|CustomerSatisfaction whereCreatedAt($value)
 * @method static Builder|CustomerSatisfaction whereFrom($value)
 * @method static Builder|CustomerSatisfaction whereId($value)
 * @method static Builder|CustomerSatisfaction whereIsActive($value)
 * @method static Builder|CustomerSatisfaction whereTo($value)
 * @method static Builder|CustomerSatisfaction whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class CustomerSatisfaction extends Model
{
    protected $guarded = [];

    protected $casts = [
        'from' => 'date',
        'to' => 'date',
        'is_active' => 'boolean',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function values(): HasMany
    {
        return $this->hasMany(CustomerSatisfactionValue::class, 'customer_satisfaction_id');
    }
}
