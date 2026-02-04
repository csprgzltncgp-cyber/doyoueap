<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\CustomerSatisfactionValue
 *
 * @property int $id
 * @property int $customer_satisfaction_id
 * @property int $country_id
 * @property int|null $value
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read CustomerSatisfaction|null $customer_satisfaction
 *
 * @method static Builder|CustomerSatisfactionValue newModelQuery()
 * @method static Builder|CustomerSatisfactionValue newQuery()
 * @method static Builder|CustomerSatisfactionValue query()
 * @method static Builder|CustomerSatisfactionValue whereCountryId($value)
 * @method static Builder|CustomerSatisfactionValue whereCreatedAt($value)
 * @method static Builder|CustomerSatisfactionValue whereCustomerSatisfactionId($value)
 * @method static Builder|CustomerSatisfactionValue whereId($value)
 * @method static Builder|CustomerSatisfactionValue whereUpdatedAt($value)
 * @method static Builder|CustomerSatisfactionValue whereValue($value)
 *
 * @mixin \Eloquent
 */
class CustomerSatisfactionValue extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function customer_satisfaction(): BelongsTo
    {
        return $this->belongsTo(CustomerSatisfaction::class);
    }
}
