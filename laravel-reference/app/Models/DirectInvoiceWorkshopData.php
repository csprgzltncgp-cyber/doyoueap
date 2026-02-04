<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\DirectInvoiceWorkshopData
 *
 * @property int $id
 * @property int $workshop_id
 * @property int $company_id
 * @property int|null $country_id
 * @property int|null $direct_invoice_id
 * @property string|null $invoiceable_after
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Company $company
 * @property-read Country|null $country
 * @property-read DirectInvoice|null $directInvoice
 * @property-read Workshop|null $workshop
 *
 * @method static Builder|DirectInvoiceWorkshopData newModelQuery()
 * @method static Builder|DirectInvoiceWorkshopData newQuery()
 * @method static Builder|DirectInvoiceWorkshopData query()
 * @method static Builder|DirectInvoiceWorkshopData whereCompanyId($value)
 * @method static Builder|DirectInvoiceWorkshopData whereCountryId($value)
 * @method static Builder|DirectInvoiceWorkshopData whereCreatedAt($value)
 * @method static Builder|DirectInvoiceWorkshopData whereDirectInvoiceId($value)
 * @method static Builder|DirectInvoiceWorkshopData whereId($value)
 * @method static Builder|DirectInvoiceWorkshopData whereInvoiceableAfter($value)
 * @method static Builder|DirectInvoiceWorkshopData whereUpdatedAt($value)
 * @method static Builder|DirectInvoiceWorkshopData whereWorkshopId($value)
 *
 * @mixin \Eloquent
 */
class DirectInvoiceWorkshopData extends Model
{
    protected $guarded = [];

    public function workshop(): BelongsTo
    {
        return $this->belongsTo(Workshop::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function directInvoice(): BelongsTo
    {
        return $this->belongsTo(DirectInvoice::class);
    }
}
