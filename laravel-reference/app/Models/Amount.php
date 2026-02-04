<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\Amount
 *
 * @property int $id
 * @property int $invoice_item_id
 * @property string $name
 * @property string|null $value
 * @property bool $is_changing
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read InvoiceItem|null $invoice_item
 *
 * @method static Builder|Amount newModelQuery()
 * @method static Builder|Amount newQuery()
 * @method static Builder|Amount query()
 * @method static Builder|Amount whereCreatedAt($value)
 * @method static Builder|Amount whereId($value)
 * @method static Builder|Amount whereInvoiceItemId($value)
 * @method static Builder|Amount whereIsChanging($value)
 * @method static Builder|Amount whereName($value)
 * @method static Builder|Amount whereUpdatedAt($value)
 * @method static Builder|Amount whereValue($value)
 *
 * @mixin \Eloquent
 */
class Amount extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_changing' => 'boolean',
    ];

    protected $appends = ['value'];

    public function getValueAttribute(): ?string
    {
        if (empty((float) $this->attributes['value'])) {
            return null;
        }

        return number_format((float) $this->attributes['value'], 3, '.', ' ');
    }

    public function setValueAttribute($value): void
    {
        $this->attributes['value'] = str_replace(' ', '', str_replace(',', '.', (string) $value));
    }

    public function invoice_item(): BelongsTo
    {
        return $this->belongsTo(InvoiceItem::class);
    }
}
