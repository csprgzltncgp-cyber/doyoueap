<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\DirectInvoiceOtherActivityData
 *
 * @property int $id
 * @property int $other_activity_id
 * @property int $company_id
 * @property int|null $country_id
 * @property int|null $direct_invoice_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Company $company
 * @property-read Country|null $country
 * @property-read DirectInvoice|null $directInvoice
 * @property-read OtherActivity|null $other_activity
 *
 * @method static Builder|DirectInvoiceOtherActivityData newModelQuery()
 * @method static Builder|DirectInvoiceOtherActivityData newQuery()
 * @method static Builder|DirectInvoiceOtherActivityData query()
 * @method static Builder|DirectInvoiceOtherActivityData whereCompanyId($value)
 * @method static Builder|DirectInvoiceOtherActivityData whereCountryId($value)
 * @method static Builder|DirectInvoiceOtherActivityData whereCreatedAt($value)
 * @method static Builder|DirectInvoiceOtherActivityData whereDirectInvoiceId($value)
 * @method static Builder|DirectInvoiceOtherActivityData whereId($value)
 * @method static Builder|DirectInvoiceOtherActivityData whereOtherActivityId($value)
 * @method static Builder|DirectInvoiceOtherActivityData whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class DirectInvoiceOtherActivityData extends Model
{
    protected $guarded = [];

    public function other_activity(): BelongsTo
    {
        return $this->belongsTo(OtherActivity::class);
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
