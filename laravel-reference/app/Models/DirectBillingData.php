<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\DirectBillingData
 *
 * @property int $id
 * @property int|null $direct_invoice_data_id
 * @property int $company_id
 * @property int|null $country_id
 * @property int $billing_frequency
 * @property string|null $invoice_language
 * @property string $currency
 * @property string|null $vat_rate
 * @property bool $tehk
 * @property bool $inside_eu
 * @property bool $outside_eu
 * @property bool $send_invoice_by_post
 * @property bool $send_completion_certificate_by_post
 * @property string $post_code
 * @property Country|null $country
 * @property string $city
 * @property string $street
 * @property string $house_number
 * @property bool $send_invoice_by_email
 * @property bool $send_completion_certificate_by_email
 * @property string|null $custom_email_subject
 * @property bool $upload_invoice_online
 * @property string $invoice_online_url
 * @property bool $upload_completion_certificate_online
 * @property string $completion_certificate_online_url
 * @property string $contact_holder_name
 * @property bool $show_contact_holder_name_on_post
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Company $company
 * @property-read Collection<int, DirectBillingDataEmail> $emails
 * @property-read int|null $emails_count
 *
 * @method static Builder|DirectBillingData newModelQuery()
 * @method static Builder|DirectBillingData newQuery()
 * @method static Builder|DirectBillingData query()
 * @method static Builder|DirectBillingData whereBillingFrequency($value)
 * @method static Builder|DirectBillingData whereCity($value)
 * @method static Builder|DirectBillingData whereCompanyId($value)
 * @method static Builder|DirectBillingData whereCompletionCertificateOnlineUrl($value)
 * @method static Builder|DirectBillingData whereContractHolderName($value)
 * @method static Builder|DirectBillingData whereCountry($value)
 * @method static Builder|DirectBillingData whereCountryId($value)
 * @method static Builder|DirectBillingData whereCreatedAt($value)
 * @method static Builder|DirectBillingData whereCurrency($value)
 * @method static Builder|DirectBillingData whereCustomEmailSubject($value)
 * @method static Builder|DirectBillingData whereDirectInvoiceDataId($value)
 * @method static Builder|DirectBillingData whereHouseNumber($value)
 * @method static Builder|DirectBillingData whereId($value)
 * @method static Builder|DirectBillingData whereInvoiceLanguage($value)
 * @method static Builder|DirectBillingData whereInvoiceOnlineUrl($value)
 * @method static Builder|DirectBillingData wherePostCode($value)
 * @method static Builder|DirectBillingData whereSendCompletionCertificateByEmail($value)
 * @method static Builder|DirectBillingData whereSendCompletionCertificateByPost($value)
 * @method static Builder|DirectBillingData whereSendInvoiceByEmail($value)
 * @method static Builder|DirectBillingData whereSendInvoiceByPost($value)
 * @method static Builder|DirectBillingData whereShowContractHolderNameOnPost($value)
 * @method static Builder|DirectBillingData whereStreet($value)
 * @method static Builder|DirectBillingData whereTahk($value)
 * @method static Builder|DirectBillingData whereUpdatedAt($value)
 * @method static Builder|DirectBillingData whereUploadCompletionCertificateOnline($value)
 * @method static Builder|DirectBillingData whereUploadInvoiceOnline($value)
 * @method static Builder|DirectBillingData whereVatRate($value)
 * @method static Builder|DirectBillingData whereContactHolderName($value)
 * @method static Builder|DirectBillingData whereShowContactHolderNameOnPost($value)
 *
 * @mixin \Eloquent
 */
class DirectBillingData extends Model
{
    final public const FREQUENCY_MONTHLY = 1;

    final public const FREQUENCY_QUARTELY = 3;

    final public const FREQUENCY_YEARLY = 12;

    protected $table = 'direct_billing_datas';

    protected $guarded = [];

    protected $casts = [
        'send_invoice_by_post' => 'boolean',
        'send_completion_certificate_by_post' => 'boolean',
        'send_invoice_by_email' => 'boolean',
        'send_completion_certificate_by_email' => 'boolean',
        'upload_invoice_online' => 'boolean',
        'upload_completion_certificate_online' => 'boolean',
        'show_contact_holder_name_on_post' => 'boolean',
        'tehk' => 'boolean',
        'billing_frequency' => 'integer',
        'inside_eu' => 'boolean',
        'outside_eu' => 'boolean',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function emails(): HasMany
    {
        return $this->hasMany(DirectBillingDataEmail::class);
    }
}
