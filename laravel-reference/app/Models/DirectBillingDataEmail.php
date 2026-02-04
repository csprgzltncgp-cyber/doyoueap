<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\DirectBillingDataEmail
 *
 * @property int $id
 * @property int $direct_billing_data_id
 * @property string $email
 * @property bool $is_cc
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read DirectBillingData|null $directBillingData
 *
 * @method static Builder|DirectBillingDataEmail newModelQuery()
 * @method static Builder|DirectBillingDataEmail newQuery()
 * @method static Builder|DirectBillingDataEmail query()
 * @method static Builder|DirectBillingDataEmail whereCreatedAt($value)
 * @method static Builder|DirectBillingDataEmail whereDirectBillingDataId($value)
 * @method static Builder|DirectBillingDataEmail whereEmail($value)
 * @method static Builder|DirectBillingDataEmail whereId($value)
 * @method static Builder|DirectBillingDataEmail whereIsCc($value)
 * @method static Builder|DirectBillingDataEmail whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class DirectBillingDataEmail extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_cc' => 'boolean',
    ];

    public function directBillingData(): BelongsTo
    {
        return $this->belongsTo(DirectBillingData::class);
    }
}
