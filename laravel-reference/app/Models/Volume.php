<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\Volume
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
 * @method static Builder|Volume newModelQuery()
 * @method static Builder|Volume newQuery()
 * @method static Builder|Volume query()
 * @method static Builder|Volume whereCreatedAt($value)
 * @method static Builder|Volume whereId($value)
 * @method static Builder|Volume whereInvoiceItemId($value)
 * @method static Builder|Volume whereIsChanging($value)
 * @method static Builder|Volume whereName($value)
 * @method static Builder|Volume whereUpdatedAt($value)
 * @method static Builder|Volume whereValue($value)
 *
 * @mixin \Eloquent
 */
class Volume extends Model
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

    public function volume_requests(): HasMany
    {
        return $this->hasMany(VolumeRequest::class, 'volume_id', 'id');
    }
}
