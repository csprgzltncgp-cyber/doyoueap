<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * App\Models\InvoiceEvent
 *
 * @property int $id
 * @property int $invoice_id Megadja, hogy melyik számlához tartozik
 * @property int|null $user_id Megadja, hogy ki oké-zta le ezt a jelzést
 * @property string $event
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Invoice $invoice
 *
 * @method static Builder|InvoiceEvent newModelQuery()
 * @method static Builder|InvoiceEvent newQuery()
 * @method static Builder|InvoiceEvent onlyTrashed()
 * @method static Builder|InvoiceEvent query()
 * @method static Builder|InvoiceEvent whereCreatedAt($value)
 * @method static Builder|InvoiceEvent whereDeletedAt($value)
 * @method static Builder|InvoiceEvent whereEvent($value)
 * @method static Builder|InvoiceEvent whereId($value)
 * @method static Builder|InvoiceEvent whereInvoiceId($value)
 * @method static Builder|InvoiceEvent whereUpdatedAt($value)
 * @method static Builder|InvoiceEvent whereUserId($value)
 * @method static Builder|InvoiceEvent withTrashed()
 * @method static Builder|InvoiceEvent withoutTrashed()
 *
 * @mixin \Eloquent
 */
class InvoiceEvent extends Model
{
    use SoftDeletes;

    protected $table = 'invoice_events';

    protected $fillable = ['invoice_id', 'event', 'user_id'];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}
