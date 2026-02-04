<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\InvoiceCaseData
 *
 * @property int $id
 * @property string $case_identifier
 * @property int|null $duration
 * @property int $consultations_count
 * @property int $expert_id
 * @property int|null $invoice_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User|null $expert
 * @property-read Invoice|null $invoice
 *
 * @method static Builder|InvoiceCaseData newModelQuery()
 * @method static Builder|InvoiceCaseData newQuery()
 * @method static Builder|InvoiceCaseData query()
 * @method static Builder|InvoiceCaseData whereCaseIdentifier($value)
 * @method static Builder|InvoiceCaseData whereConsultationsCount($value)
 * @method static Builder|InvoiceCaseData whereCreatedAt($value)
 * @method static Builder|InvoiceCaseData whereDuration($value)
 * @method static Builder|InvoiceCaseData whereExpertId($value)
 * @method static Builder|InvoiceCaseData whereId($value)
 * @method static Builder|InvoiceCaseData whereInvoiceId($value)
 * @method static Builder|InvoiceCaseData whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class InvoiceCaseData extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'invoice_case_datas';

    public function expert(): BelongsTo
    {
        return $this->belongsTo(User::class, 'expert_id', 'id');
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'invoice_id', 'id');
    }
}
