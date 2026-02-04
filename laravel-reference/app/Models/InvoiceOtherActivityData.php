<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\InvoiceOtherActivityData
 *
 * @property int $id
 * @property int $other_activity_id
 * @property int $expert_id
 * @property int|null $invoice_id
 * @property string $activity_id
 * @property int|null $price
 * @property string|null $currency
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User|null $expert
 * @property-read Invoice|null $invoice
 * @property-read OtherActivity|null $other_activity
 *
 * @method static Builder|InvoiceOtherActivityData newModelQuery()
 * @method static Builder|InvoiceOtherActivityData newQuery()
 * @method static Builder|InvoiceOtherActivityData query()
 * @method static Builder|InvoiceOtherActivityData whereActivityId($value)
 * @method static Builder|InvoiceOtherActivityData whereCreatedAt($value)
 * @method static Builder|InvoiceOtherActivityData whereCurrency($value)
 * @method static Builder|InvoiceOtherActivityData whereExpertId($value)
 * @method static Builder|InvoiceOtherActivityData whereId($value)
 * @method static Builder|InvoiceOtherActivityData whereInvoiceId($value)
 * @method static Builder|InvoiceOtherActivityData whereOtherActivityId($value)
 * @method static Builder|InvoiceOtherActivityData wherePrice($value)
 * @method static Builder|InvoiceOtherActivityData whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class InvoiceOtherActivityData extends Model
{
    protected $guarded = [];

    protected $table = 'invoice_other_activity_datas';

    public function other_activity(): BelongsTo
    {
        return $this->belongsTo(OtherActivity::class, 'other_activity_id', 'id');
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'invoice_id', 'id');
    }

    public function expert(): BelongsTo
    {
        return $this->belongsTo(User::class, 'expert_id', 'id');
    }
}
