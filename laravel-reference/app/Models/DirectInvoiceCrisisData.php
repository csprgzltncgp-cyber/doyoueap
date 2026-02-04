<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\DirectInvoiceCrisisData
 *
 * @property int $id
 * @property int $crisis_id
 * @property int $company_id
 * @property int|null $country_id
 * @property int|null $direct_invoice_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Company $company
 * @property-read Country|null $country
 * @property-read CrisisIntervention|null $crisis
 * @property-read DirectInvoice|null $directInvoice
 *
 * @method static Builder|DirectInvoiceCrisisData newModelQuery()
 * @method static Builder|DirectInvoiceCrisisData newQuery()
 * @method static Builder|DirectInvoiceCrisisData query()
 * @method static Builder|DirectInvoiceCrisisData whereCompanyId($value)
 * @method static Builder|DirectInvoiceCrisisData whereCountryId($value)
 * @method static Builder|DirectInvoiceCrisisData whereCreatedAt($value)
 * @method static Builder|DirectInvoiceCrisisData whereCrisisId($value)
 * @method static Builder|DirectInvoiceCrisisData whereDirectInvoiceId($value)
 * @method static Builder|DirectInvoiceCrisisData whereId($value)
 * @method static Builder|DirectInvoiceCrisisData whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class DirectInvoiceCrisisData extends Model
{
    protected $guarded = [];

    public function crisis(): BelongsTo
    {
        return $this->belongsTo(CrisisIntervention::class);
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
