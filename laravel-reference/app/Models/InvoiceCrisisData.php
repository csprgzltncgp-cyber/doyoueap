<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\InvoiceCrisisData
 *
 * @property int $id
 * @property int $crisis_case_id
 * @property int $expert_id
 * @property int|null $invoice_id
 * @property string $activity_id
 * @property int $price
 * @property string $currency
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read CrisisCase|null $crisis_case
 * @property-read User|null $expert
 * @property-read Invoice|null $invoice
 *
 * @method static Builder|InvoiceCrisisData newModelQuery()
 * @method static Builder|InvoiceCrisisData newQuery()
 * @method static Builder|InvoiceCrisisData query()
 * @method static Builder|InvoiceCrisisData whereActivityId($value)
 * @method static Builder|InvoiceCrisisData whereCreatedAt($value)
 * @method static Builder|InvoiceCrisisData whereCrisisCaseId($value)
 * @method static Builder|InvoiceCrisisData whereCurrency($value)
 * @method static Builder|InvoiceCrisisData whereExpertId($value)
 * @method static Builder|InvoiceCrisisData whereId($value)
 * @method static Builder|InvoiceCrisisData whereInvoiceId($value)
 * @method static Builder|InvoiceCrisisData wherePrice($value)
 * @method static Builder|InvoiceCrisisData whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class InvoiceCrisisData extends Model
{
    protected $guarded = [];

    protected $table = 'invoice_crisis_datas';

    public function crisis_case(): BelongsTo
    {
        return $this->belongsTo(CrisisCase::class, 'crisis_case_id', 'id');
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
