<?php

namespace App\Models;

use App\Enums\ContractHolderCompany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * App\Models\InvoiceItem
 *
 * @property int $id
 * @property int|null $direct_invoice_data_id
 * @property int $company_id
 * @property int|null $country_id
 * @property string $name
 * @property int $input
 * @property string|null $comment
 * @property string $data_request_salutation
 * @property string $data_request_email
 * @property bool $is_activity_id_shown
 * @property bool $shown_by_item
 * @property int $with_timestamp
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Amount|null $amount
 * @property-read Company $company
 * @property-read Country|null $country
 * @property-read DirectInvoiceData|null $direct_invoice_data
 * @property-read Volume|null $volume
 *
 * @method static Builder|InvoiceItem newModelQuery()
 * @method static Builder|InvoiceItem newQuery()
 * @method static Builder|InvoiceItem query()
 * @method static Builder|InvoiceItem whereComment($value)
 * @method static Builder|InvoiceItem whereCompanyId($value)
 * @method static Builder|InvoiceItem whereCountryId($value)
 * @method static Builder|InvoiceItem whereCreatedAt($value)
 * @method static Builder|InvoiceItem whereDirectInvoiceDataId($value)
 * @method static Builder|InvoiceItem whereId($value)
 * @method static Builder|InvoiceItem whereInput($value)
 * @method static Builder|InvoiceItem whereIsActivityIdShown($value)
 * @method static Builder|InvoiceItem whereName($value)
 * @method static Builder|InvoiceItem whereShownByItem($value)
 * @method static Builder|InvoiceItem whereUpdatedAt($value)
 * @method static Builder|InvoiceItem whereWithTimestamp($value)
 *
 * @mixin \Eloquent
 */
class InvoiceItem extends Model
{
    final public const INPUT_TYPE_MULTIPLICATION = 1;

    final public const INPUT_TYPE_WORKSHOP = 2;

    final public const INPUT_TYPE_CRISIS = 3;

    final public const INPUT_TYPE_OTHER_ACTIVITY = 4;

    final public const INPUT_TYPE_AMOUNT = 5;

    final public const INPUT_TYPE_OPTUM_PSYCHOLOGY_CONSULTATIONS = 6;

    final public const INPUT_TYPE_OPTUM_LAW_CONSULTATIONS = 7;

    final public const INPUT_TYPE_OPTUM_FINANCE_CONSULTATIONS = 8;

    final public const INPUT_TYPE_COMPSYCH_PSYCHOLOGY_CONSULTATIONS = 9;

    final public const INPUT_TYPE_COMPSYCH_LAW_CONSULTATIONS = 10;

    final public const INPUT_TYPE_COMPSYCH_FINANCE_CONSULTATIONS = 11;

    final public const INPUT_TYPE_COMPSYCH_WELL_BEING_COACHING_CONSULTATIONS_30 = 12;

    final public const INPUT_TYPE_COMPSYCH_WELL_BEING_COACHING_CONSULTATIONS_15 = 13;

    protected $guarded = [];

    protected $casts = [
        'is_activity_id_shown' => 'boolean',
        'shown_by_item' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::deleted(function (InvoiceItem $invoice_item): void {
            $invoice_item->amount()->delete();

            if (! $invoice_item->volume->volume_requests->isEmpty()) {
                $invoice_item->volume->volume_requests->each(fn (VolumeRequest $volume_request): bool => $volume_request->delete());
            }

            $invoice_item->volume()->delete();
        });
    }

    public function direct_invoice_data(): BelongsTo
    {
        return $this->belongsTo(DirectInvoiceData::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function amount(): HasOne
    {
        return $this->hasOne(Amount::class);
    }

    public function volume(): HasOne
    {
        return $this->hasOne(Volume::class);
    }

    public static function getInputTypes(Company $company): array
    {
        return array_filter(
            [
                self::INPUT_TYPE_MULTIPLICATION => __('company-edit.invoice-item-input-type.multiplication'),
                self::INPUT_TYPE_WORKSHOP => __('company-edit.invoice-item-input-type.workshop'),
                self::INPUT_TYPE_CRISIS => __('company-edit.invoice-item-input-type.crisis'),
                self::INPUT_TYPE_OTHER_ACTIVITY => __('company-edit.invoice-item-input-type.other-activity'),
                self::INPUT_TYPE_AMOUNT => __('company-edit.invoice-item-input-type.amount'),
                self::INPUT_TYPE_OPTUM_PSYCHOLOGY_CONSULTATIONS => $company->id === ContractHolderCompany::OPTUM->value ? __('company-edit.invoice-item-input-type.optum-psychology-consultations') : null,
                self::INPUT_TYPE_OPTUM_LAW_CONSULTATIONS => $company->id === ContractHolderCompany::OPTUM->value ? __('company-edit.invoice-item-input-type.optum-law-consultations') : null,
                self::INPUT_TYPE_OPTUM_FINANCE_CONSULTATIONS => $company->id === ContractHolderCompany::OPTUM->value ? __('company-edit.invoice-item-input-type.optum-finance-consultations') : null,
                self::INPUT_TYPE_COMPSYCH_PSYCHOLOGY_CONSULTATIONS => $company->id === ContractHolderCompany::COMPSYCH->value ? __('company-edit.invoice-item-input-type.compsych-psychology-consultations') : null,
                self::INPUT_TYPE_COMPSYCH_LAW_CONSULTATIONS => $company->id === ContractHolderCompany::COMPSYCH->value ? __('company-edit.invoice-item-input-type.compsych-law-consultations') : null,
                self::INPUT_TYPE_COMPSYCH_FINANCE_CONSULTATIONS => $company->id === ContractHolderCompany::COMPSYCH->value ? __('company-edit.invoice-item-input-type.compsych-finance-consultations') : null,
                self::INPUT_TYPE_COMPSYCH_WELL_BEING_COACHING_CONSULTATIONS_30 => $company->id === ContractHolderCompany::COMPSYCH->value ? __('company-edit.invoice-item-input-type.compsych-well-being-coaching-consultations-30') : null,
                self::INPUT_TYPE_COMPSYCH_WELL_BEING_COACHING_CONSULTATIONS_15 => $company->id === ContractHolderCompany::COMPSYCH->value ? __('company-edit.invoice-item-input-type.compsych-well-being-coaching-consultations-15') : null,
            ]
        );
    }

    public function needActivityIdCheckbox(): bool
    {
        return $this->input == self::INPUT_TYPE_WORKSHOP || $this->input == self::INPUT_TYPE_CRISIS || $this->input == self::INPUT_TYPE_OTHER_ACTIVITY;
    }

    public function isInputTypeMultiplication(): bool
    {
        return $this->input == self::INPUT_TYPE_MULTIPLICATION;
    }

    public function isInputTypeAmount(): bool
    {
        return $this->input == self::INPUT_TYPE_AMOUNT;
    }

    public function isInputTypeContractHolder(): bool
    {
        return $this->input == self::INPUT_TYPE_OPTUM_PSYCHOLOGY_CONSULTATIONS ||
            $this->input == self::INPUT_TYPE_OPTUM_LAW_CONSULTATIONS ||
            $this->input == self::INPUT_TYPE_OPTUM_FINANCE_CONSULTATIONS ||
            $this->input == self::INPUT_TYPE_COMPSYCH_PSYCHOLOGY_CONSULTATIONS ||
            $this->input == self::INPUT_TYPE_COMPSYCH_LAW_CONSULTATIONS ||
            $this->input == self::INPUT_TYPE_COMPSYCH_FINANCE_CONSULTATIONS ||
            $this->input == self::INPUT_TYPE_COMPSYCH_WELL_BEING_COACHING_CONSULTATIONS_30 ||
            $this->input == self::INPUT_TYPE_COMPSYCH_WELL_BEING_COACHING_CONSULTATIONS_15;
    }
}
