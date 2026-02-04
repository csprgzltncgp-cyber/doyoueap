<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\CustomInvoiceItem
 *
 * @property int $id
 * @property string $name
 * @property int $user_id
 * @property int $country_id
 * @property int $amount
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Country $country
 * @property-read User $user
 *
 * @method static Builder|CustomInvoiceItem newModelQuery()
 * @method static Builder|CustomInvoiceItem newQuery()
 * @method static Builder|CustomInvoiceItem query()
 * @method static Builder|CustomInvoiceItem whereAmount($value)
 * @method static Builder|CustomInvoiceItem whereCountryId($value)
 * @method static Builder|CustomInvoiceItem whereCreatedAt($value)
 * @method static Builder|CustomInvoiceItem whereId($value)
 * @method static Builder|CustomInvoiceItem whereName($value)
 * @method static Builder|CustomInvoiceItem whereUpdatedAt($value)
 * @method static Builder|CustomInvoiceItem whereUserId($value)
 *
 * @mixin \Eloquent
 */
class CustomInvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'user_id',
        'country_id',
        'amount',
    ];

    public function getAmountAttribute()
    {
        if (empty($this->attributes['amount'])) {
            return $this->attributes['amount'] ?? null;
        }

        return number_format((float) str_replace(' ', '', (string) $this->attributes['amount']), 0, ',', ' ');
    }

    public function setAmountAttribute($value): void
    {
        $this->attributes['amount'] = str_replace(' ', '', (string) $value);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
