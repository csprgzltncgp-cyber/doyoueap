<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * App\Models\DirectInvoiceData
 *
 * @property int $id
 * @property int $company_id
 * @property int|null $country_id
 * @property string|null $admin_identifier
 * @property string $name
 * @property bool $is_name_shown
 * @property Country|null $country
 * @property string $postal_code
 * @property string $city
 * @property string $street
 * @property string $house_number
 * @property bool $is_address_shown
 * @property string $po_number
 * @property bool $is_po_number_shown
 * @property bool $is_po_number_changing
 * @property bool $is_po_number_required
 * @property string $tax_number
 * @property string|null $community_tax_number
 * @property bool $is_tax_number_shown
 * @property string|null $group_id
 * @property int $payment_deadline
 * @property bool $is_payment_deadlie_shown
 * @property bool $invoicing_inactive
 * @property Carbon|null $invoicing_inactive_from
 * @property Carbon|null $invoicing_inactive_to
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Company $company
 * @property-read DirectBillingData|null $direct_billing_data
 * @property-read Collection<int, InvoiceItem> $invoice_items
 * @property-read int|null $invoice_items_count
 *
 * @method static Builder|DirectInvoiceData newModelQuery()
 * @method static Builder|DirectInvoiceData newQuery()
 * @method static Builder|DirectInvoiceData query()
 * @method static Builder|DirectInvoiceData whereAdminIdentifier($value)
 * @method static Builder|DirectInvoiceData whereCity($value)
 * @method static Builder|DirectInvoiceData whereCompanyId($value)
 * @method static Builder|DirectInvoiceData whereCountry($value)
 * @method static Builder|DirectInvoiceData whereCountryId($value)
 * @method static Builder|DirectInvoiceData whereCreatedAt($value)
 * @method static Builder|DirectInvoiceData whereEuTaxNumber($value)
 * @method static Builder|DirectInvoiceData whereGroupId($value)
 * @method static Builder|DirectInvoiceData whereHouseNumber($value)
 * @method static Builder|DirectInvoiceData whereId($value)
 * @method static Builder|DirectInvoiceData whereIsAddressShown($value)
 * @method static Builder|DirectInvoiceData whereIsNameShown($value)
 * @method static Builder|DirectInvoiceData whereIsPaymentDeadlieShown($value)
 * @method static Builder|DirectInvoiceData whereIsPoNumberChanging($value)
 * @method static Builder|DirectInvoiceData whereIsPoNumberRequired($value)
 * @method static Builder|DirectInvoiceData whereIsPoNumberShown($value)
 * @method static Builder|DirectInvoiceData whereIsTaxNumberShown($value)
 * @method static Builder|DirectInvoiceData whereName($value)
 * @method static Builder|DirectInvoiceData wherePaymentDeadline($value)
 * @method static Builder|DirectInvoiceData wherePoNumber($value)
 * @method static Builder|DirectInvoiceData wherePostalCode($value)
 * @method static Builder|DirectInvoiceData whereStreet($value)
 * @method static Builder|DirectInvoiceData whereTaxNumber($value)
 * @method static Builder|DirectInvoiceData whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class DirectInvoiceData extends Model
{
    protected $table = 'direct_invoice_datas';

    protected $guarded = [];

    protected $casts = [
        'is_name_shown' => 'boolean',
        'is_address_shown' => 'boolean',
        'is_po_number_shown' => 'boolean',
        'is_po_number_changing' => 'boolean',
        'is_po_number_required' => 'boolean',
        'is_tax_number_shown' => 'boolean',
        'is_payment_deadlie_shown' => 'boolean',
        'invoicing_inactive' => 'boolean',
        'invoicing_inactive_from' => 'date:Y-m-d',
        'invoicing_inactive_to' => 'date:Y-m-d',
    ];

    public static function boot(): void
    {
        parent::boot();

        static::creating(function ($model): void {
            $model->is_name_shown = true;
            $model->is_address_shown = true;
            $model->is_po_number_shown = true;
            $model->is_tax_number_shown = true;
            $model->is_payment_deadlie_shown = true;
            $model->is_po_number_required = true;
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function direct_billing_data(): HasOne
    {
        return $this->hasOne(DirectBillingData::class);
    }

    public function invoice_items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }
}
