<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\InvoiceNote
 *
 * @property int $id
 * @property int|null $direct_invoice_data_id
 * @property int $company_id
 * @property int|null $country_id
 * @property string $value
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Company $company
 * @property-read Country|null $country
 *
 * @method static Builder|InvoiceNote newModelQuery()
 * @method static Builder|InvoiceNote newQuery()
 * @method static Builder|InvoiceNote query()
 * @method static Builder|InvoiceNote whereCompanyId($value)
 * @method static Builder|InvoiceNote whereCountryId($value)
 * @method static Builder|InvoiceNote whereCreatedAt($value)
 * @method static Builder|InvoiceNote whereDirectInvoiceDataId($value)
 * @method static Builder|InvoiceNote whereId($value)
 * @method static Builder|InvoiceNote whereUpdatedAt($value)
 * @method static Builder|InvoiceNote whereValue($value)
 *
 * @mixin \Eloquent
 */
class InvoiceNote extends Model
{
    protected $guarded = [];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}
