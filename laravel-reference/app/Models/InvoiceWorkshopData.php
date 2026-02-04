<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\InvoiceWorkshopData
 *
 * @property int $id
 * @property int $workshop_case_id
 * @property int $expert_id
 * @property int|null $invoice_id
 * @property string $activity_id
 * @property int $price
 * @property string $currency
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User|null $expert
 * @property-read Invoice|null $invoice
 * @property-read WorkshopCase|null $workshop_case
 *
 * @method static Builder|InvoiceWorkshopData newModelQuery()
 * @method static Builder|InvoiceWorkshopData newQuery()
 * @method static Builder|InvoiceWorkshopData query()
 * @method static Builder|InvoiceWorkshopData whereActivityId($value)
 * @method static Builder|InvoiceWorkshopData whereCreatedAt($value)
 * @method static Builder|InvoiceWorkshopData whereCurrency($value)
 * @method static Builder|InvoiceWorkshopData whereExpertId($value)
 * @method static Builder|InvoiceWorkshopData whereId($value)
 * @method static Builder|InvoiceWorkshopData whereInvoiceId($value)
 * @method static Builder|InvoiceWorkshopData wherePrice($value)
 * @method static Builder|InvoiceWorkshopData whereUpdatedAt($value)
 * @method static Builder|InvoiceWorkshopData whereWorkshopCaseId($value)
 *
 * @mixin \Eloquent
 */
class InvoiceWorkshopData extends Model
{
    protected $guarded = [];

    protected $table = 'invoice_workshop_datas';

    public function workshop_case(): BelongsTo
    {
        return $this->belongsTo(WorkshopCase::class, 'workshop_case_id', 'id');
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
