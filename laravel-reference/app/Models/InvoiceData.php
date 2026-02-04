<?php

namespace App\Models;

use App\Enums\InvoicingType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\InvoiceData
 *
 * @property int $id
 * @property int $user_id Megadja, hogy melyik felhasználóhoz tartozik
 * @property string $name
 * @property string $email
 * @property string $account_number
 * @property string|null $swift
 * @property string|null $tax_number
 * @property string|null $international_tax_number
 * @property string $bank_name
 * @property string $bank_address
 * @property string $destination_country
 * @property int|null $hourly_rate_50
 * @property int|null $hourly_rate_30
 * @property string $currency
 * @property InvoicingType $invoicing_type
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property mixed $hourly_rate30
 * @property mixed $hourly_rate50
 *
 * @method static Builder|InvoiceData newModelQuery()
 * @method static Builder|InvoiceData newQuery()
 * @method static Builder|InvoiceData query()
 * @method static Builder|InvoiceData whereAccountNumber($value)
 * @method static Builder|InvoiceData whereBankAddress($value)
 * @method static Builder|InvoiceData whereBankName($value)
 * @method static Builder|InvoiceData whereCreatedAt($value)
 * @method static Builder|InvoiceData whereCurrency($value)
 * @method static Builder|InvoiceData whereDestinationCountry($value)
 * @method static Builder|InvoiceData whereEmail($value)
 * @method static Builder|InvoiceData whereHourlyRate30($value)
 * @method static Builder|InvoiceData whereHourlyRate50($value)
 * @method static Builder|InvoiceData whereId($value)
 * @method static Builder|InvoiceData whereInternationalTaxNumber($value)
 * @method static Builder|InvoiceData whereName($value)
 * @method static Builder|InvoiceData whereSwift($value)
 * @method static Builder|InvoiceData whereTaxNumber($value)
 * @method static Builder|InvoiceData whereUpdatedAt($value)
 * @method static Builder|InvoiceData whereUserId($value)
 *
 * @property int|null $fixed_wage
 * @property-read Country $country
 *
 * @method static Builder|InvoiceData whereFixedWage($value)
 * @method static Builder|InvoiceData whereInvoicingType($value)
 *
 * @mixin \Eloquent
 */
class InvoiceData extends Model
{
    protected $guarded = [];

    protected $table = 'invoice_datas';

    protected $appends = ['hourly_rate_50', 'hourly_rate_30'];

    protected $casts = [
        'invoicing_type' => InvoicingType::class,
    ];

    public function getHourlyRate50Attribute()
    {
        if (empty($this->attributes['hourly_rate_50'])) {
            return $this->attributes['hourly_rate_50'] ?? null;
        }

        return number_format((float) str_replace(' ', '', (string) $this->attributes['hourly_rate_50']), 0, ',', ' ');
    }

    public function setHourlyRate50Attribute($value): void
    {
        $this->attributes['hourly_rate_50'] = str_replace(' ', '', (string) $value);
    }

    public function getHourlyRate30Attribute()
    {
        if (empty($this->attributes['hourly_rate_30'])) {
            return $this->attributes['hourly_rate_30'] ?? null;
        }

        return number_format((float) str_replace(' ', '', (string) $this->attributes['hourly_rate_30']), 0, ',', ' ');
    }

    public function setHourlyRate30Attribute($value): void
    {
        $this->attributes['hourly_rate_30'] = str_replace(' ', '', (string) $value);
    }

    public function getFixedWageAttribute()
    {
        if (empty($this->attributes['fixed_wage'])) {
            return $this->attributes['fixed_wage'] ?? null;
        }

        return number_format((float) str_replace(' ', '', (string) $this->attributes['fixed_wage']), 0, ',', ' ');
    }

    public function setFixedWageAttribute($value): void
    {
        $this->attributes['fixed_wage'] = str_replace(' ', '', (string) $value);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'destination_country');
    }
}
