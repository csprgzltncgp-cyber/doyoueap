<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\ExpertCurrencyChange
 *
 * @property int $id
 * @property int $user_id
 * @property string $company_name
 * @property string $registered_seat
 * @property string $registration_number
 * @property string $tax_number
 * @property string $represented_by
 * @property string|null $hourly_rate_30_currency
 * @property string|null $hourly_rate_50_currency
 * @property string|null $hourly_rate_30
 * @property string|null $hourly_rate_50
 * @property string|null $document
 * @property string|null $downloaded_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $expert
 *
 * @method static Builder|ExpertCurrencyChange newModelQuery()
 * @method static Builder|ExpertCurrencyChange newQuery()
 * @method static Builder|ExpertCurrencyChange query()
 * @method static Builder|ExpertCurrencyChange whereCompanyName($value)
 * @method static Builder|ExpertCurrencyChange whereCreatedAt($value)
 * @method static Builder|ExpertCurrencyChange whereDocument($value)
 * @method static Builder|ExpertCurrencyChange whereDownloadedAt($value)
 * @method static Builder|ExpertCurrencyChange whereHourlyRate30($value)
 * @method static Builder|ExpertCurrencyChange whereHourlyRate30Currency($value)
 * @method static Builder|ExpertCurrencyChange whereHourlyRate50($value)
 * @method static Builder|ExpertCurrencyChange whereHourlyRate50Currency($value)
 * @method static Builder|ExpertCurrencyChange whereId($value)
 * @method static Builder|ExpertCurrencyChange whereRegisteredSeat($value)
 * @method static Builder|ExpertCurrencyChange whereRegistrationNumber($value)
 * @method static Builder|ExpertCurrencyChange whereRepresentedBy($value)
 * @method static Builder|ExpertCurrencyChange whereTaxNumber($value)
 * @method static Builder|ExpertCurrencyChange whereUpdatedAt($value)
 * @method static Builder|ExpertCurrencyChange whereUserId($value)
 *
 * @mixin \Eloquent
 */
class ExpertCurrencyChange extends Model
{
    protected $guarded = [];

    public function expert(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
