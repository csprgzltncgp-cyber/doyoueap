<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\InvoiceComment
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
 * @method static Builder|InvoiceComment newModelQuery()
 * @method static Builder|InvoiceComment newQuery()
 * @method static Builder|InvoiceComment query()
 * @method static Builder|InvoiceComment whereCompanyId($value)
 * @method static Builder|InvoiceComment whereCountryId($value)
 * @method static Builder|InvoiceComment whereCreatedAt($value)
 * @method static Builder|InvoiceComment whereDirectInvoiceDataId($value)
 * @method static Builder|InvoiceComment whereId($value)
 * @method static Builder|InvoiceComment whereUpdatedAt($value)
 * @method static Builder|InvoiceComment whereValue($value)
 *
 * @mixin \Eloquent
 */
class InvoiceComment extends Model
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
