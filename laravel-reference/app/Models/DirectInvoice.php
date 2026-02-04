<?php

namespace App\Models;

use App\Models\Scopes\ContractHolderCompanyScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * App\Models\DirectInvoice
 *
 * @property int $id
 * @property int $company_id
 * @property int|null $country_id
 * @property int|null $direct_invoice_data_id
 * @property array $data
 * @property Carbon $from
 * @property Carbon $to
 * @property string|null $invoice_number
 * @property Carbon|null $downloaded_at
 * @property Carbon|null $sent_at
 * @property string|null $invoice_uploaded_at
 * @property Carbon|null $paid_at
 * @property float|null $paid_amount
 * @property bool $active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Company $company
 * @property-read CompletionCertificate|null $completion_certificate
 * @property-read Country|null $country
 * @property-read Collection<int, DirectInvoiceCrisisData> $crisisData
 * @property-read int|null $crisis_data_count
 * @property-read Envelope|null $envelope
 * @property-read Collection<int, DirectInvoiceOtherActivityData> $otherActivityData
 * @property-read int|null $other_activity_data_count
 * @property-read Collection<int, DirectInvoiceWorkshopData> $workshopData
 * @property-read int|null $workshop_data_count
 *
 * @method static Builder|DirectInvoice newModelQuery()
 * @method static Builder|DirectInvoice newQuery()
 * @method static Builder|DirectInvoice query()
 * @method static Builder|DirectInvoice whereCompanyId($value)
 * @method static Builder|DirectInvoice whereCountryId($value)
 * @method static Builder|DirectInvoice whereCreatedAt($value)
 * @method static Builder|DirectInvoice whereData($value)
 * @method static Builder|DirectInvoice whereDirectInvoiceDataId($value)
 * @method static Builder|DirectInvoice whereDownloadedAt($value)
 * @method static Builder|DirectInvoice whereFrom($value)
 * @method static Builder|DirectInvoice whereId($value)
 * @method static Builder|DirectInvoice whereInvoiceNumber($value)
 * @method static Builder|DirectInvoice whereInvoiceUploadedAt($value)
 * @method static Builder|DirectInvoice wherePaidAt($value)
 * @method static Builder|DirectInvoice whereSentAt($value)
 * @method static Builder|DirectInvoice whereTo($value)
 * @method static Builder|DirectInvoice whereUpdatedAt($value)
 * @method static Builder|DirectInvoice wherePaidAmount($value)
 *
 * @mixin \Eloquent
 */
class DirectInvoice extends Model
{
    protected $guarded = [];

    protected $appends = [
        'data',
    ];

    protected $casts = [
        'downloaded_at' => 'datetime',
        'sent_at' => 'datetime',
        'paid_at' => 'datetime',
        'paid_amount' => 'float',
        'active' => 'boolean',
        'company_id' => 'integer',
    ];

    public function getDataAttribute(): mixed
    {
        return json_decode((string) $this->attributes['data'], true, 512, JSON_THROW_ON_ERROR);
    }

    public function setDataAttribute($value): void
    {
        $this->attributes['data'] = json_encode($value, JSON_THROW_ON_ERROR);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class)->withoutGlobalScope(ContractHolderCompanyScope::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function crisisData(): HasMany
    {
        return $this->hasMany(DirectInvoiceCrisisData::class);
    }

    public function workshopData(): HasMany
    {
        return $this->hasMany(DirectInvoiceWorkshopData::class);
    }

    public function otherActivityData(): HasMany
    {
        return $this->hasMany(DirectInvoiceOtherActivityData::class);
    }

    public function completion_certificate(): HasOne
    {
        return $this->hasOne(CompletionCertificate::class);
    }

    public function envelope(): HasOne
    {
        return $this->hasOne(Envelope::class);
    }
}
