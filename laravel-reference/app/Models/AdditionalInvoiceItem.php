<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\AdditionalInvoiceItem
 *
 * @property int $id
 * @property string $name
 * @property int $price
 * @property int $invoice_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Invoice|null $invoice
 *
 * @method static Builder|AdditionalInvoiceItem newModelQuery()
 * @method static Builder|AdditionalInvoiceItem newQuery()
 * @method static Builder|AdditionalInvoiceItem query()
 * @method static Builder|AdditionalInvoiceItem whereCreatedAt($value)
 * @method static Builder|AdditionalInvoiceItem whereId($value)
 * @method static Builder|AdditionalInvoiceItem whereInvoiceId($value)
 * @method static Builder|AdditionalInvoiceItem whereName($value)
 * @method static Builder|AdditionalInvoiceItem wherePrice($value)
 * @method static Builder|AdditionalInvoiceItem whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class AdditionalInvoiceItem extends Model
{
    protected $fillable = [
        'name',
        'price',
        'invoice_id',
    ];

    protected $appends = ['price'];

    public function getPriceAttribute()
    {
        if (empty($this->attributes['price'])) {
            return $this->attributes['price'] ?? null;
        }

        return number_format((float) str_replace(' ', '', (string) $this->attributes['price']), 0, ',', ' ');
    }

    public function setPriceAttribute($value): void
    {
        $this->attributes['price'] = str_replace(' ', '', (string) $value);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}
