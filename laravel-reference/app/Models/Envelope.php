<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\Envelope
 *
 * @property int $id
 * @property int $direct_invoice_id
 * @property Carbon|null $printed_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read DirectInvoice|null $direct_invoice
 *
 * @method static Builder|Envelope newModelQuery()
 * @method static Builder|Envelope newQuery()
 * @method static Builder|Envelope query()
 * @method static Builder|Envelope whereCreatedAt($value)
 * @method static Builder|Envelope whereDirectInvoiceId($value)
 * @method static Builder|Envelope whereId($value)
 * @method static Builder|Envelope wherePrintedAt($value)
 * @method static Builder|Envelope whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Envelope extends Model
{
    protected $guarded = [];

    protected $casts = [
        'printed_at' => 'datetime',
    ];

    public function direct_invoice(): BelongsTo
    {
        return $this->belongsTo(DirectInvoice::class);
    }
}
