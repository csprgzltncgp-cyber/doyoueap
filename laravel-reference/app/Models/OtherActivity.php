<?php

namespace App\Models;

use App\Enums\OtherActivityStatus;
use App\Enums\OtherActivityType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Carbon;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * App\Models\OtherActivity
 *
 * @property int $id
 * @property int $company_id
 * @property int $contract_holder_id
 * @property int|null $user_id
 * @property int|null $country_id
 * @property int|null $city_id
 * @property string|null $activity_id
 * @property OtherActivityStatus|null $status
 * @property OtherActivityType|null $type
 * @property int $permission_id
 * @property int|null $company_price
 * @property string|null $company_currency
 * @property string|null $company_email
 * @property string|null $company_phone
 * @property int|null $user_price
 * @property string|null $user_currency
 * @property string|null $user_phone
 * @property string|null $language
 * @property int|null $participants
 * @property string|null $date
 * @property string|null $start_time
 * @property string|null $end_time
 * @property bool $paid
 * @property string|null $closed_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read City|null $city
 * @property-read Company $company
 * @property-read ContractHolder $contract_holder
 * @property-read Country|null $country
 * @property-read OtherActivityEvent|null $event
 * @property-read InvoiceOtherActivityData|null $invoice_data
 * @property-read User|null $user
 *
 * @method static Builder|OtherActivity newModelQuery()
 * @method static Builder|OtherActivity newQuery()
 * @method static Builder|OtherActivity query()
 * @method static Builder|OtherActivity whereActivityId($value)
 * @method static Builder|OtherActivity whereCityId($value)
 * @method static Builder|OtherActivity whereClosedAt($value)
 * @method static Builder|OtherActivity whereCompanyCurrency($value)
 * @method static Builder|OtherActivity whereCompanyEmail($value)
 * @method static Builder|OtherActivity whereCompanyId($value)
 * @method static Builder|OtherActivity whereCompanyPhone($value)
 * @method static Builder|OtherActivity whereCompanyPrice($value)
 * @method static Builder|OtherActivity whereContractHolderId($value)
 * @method static Builder|OtherActivity whereCountryId($value)
 * @method static Builder|OtherActivity whereCreatedAt($value)
 * @method static Builder|OtherActivity whereDate($value)
 * @method static Builder|OtherActivity whereEndTime($value)
 * @method static Builder|OtherActivity whereId($value)
 * @method static Builder|OtherActivity whereLanguage($value)
 * @method static Builder|OtherActivity wherePaid($value)
 * @method static Builder|OtherActivity whereParticipants($value)
 * @method static Builder|OtherActivity wherePermissionId($value)
 * @method static Builder|OtherActivity whereStartTime($value)
 * @method static Builder|OtherActivity whereStatus($value)
 * @method static Builder|OtherActivity whereType($value)
 * @method static Builder|OtherActivity whereUpdatedAt($value)
 * @method static Builder|OtherActivity whereUserCurrency($value)
 * @method static Builder|OtherActivity whereUserId($value)
 * @method static Builder|OtherActivity whereUserPhone($value)
 * @method static Builder|OtherActivity whereUserPrice($value)
 *
 * @mixin \Eloquent
 */
class OtherActivity extends Model
{
    use LogsActivity;

    protected $table = 'other_activity';

    protected $guarded = [];

    protected $casts = [
        'status' => OtherActivityStatus::class,
        'company_price' => 'integer',
        'user_price' => 'integer',
        'paid' => 'boolean',
        'type' => OtherActivityType::class,
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnlyDirty()
            ->logAll()
            ->dontSubmitEmptyLogs();
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function contract_holder(): BelongsTo
    {
        return $this->belongsTo(ContractHolder::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function event(): HasOne
    {
        return $this->hasOne(OtherActivityEvent::class, 'other_activity_id', 'id');
    }

    public function org_data()
    {
        return OrgData::query()->where('company_id', optional($this->company)->id)->where('country_id', optional($this->country)->id)->first();
    }

    public function invoice_data(): HasOne
    {
        return $this->hasOne(InvoiceOtherActivityData::class, 'other_activity_id', 'id');
    }

    /**
     * @return MorphMany<ActivityPlanMember>
     */
    public function activity_plan_members(): MorphMany
    {
        return $this->morphMany(ActivityPlanMember::class, 'activity_plan_memberable');
    }

    public function is_outsourceable(): bool
    {
        return
            ((bool) $this->getAttribute('company') && $this->company !== null
                &&
                (bool) $this->getAttribute('company_email')
                &&
                (bool) $this->getAttribute('company_phone')
                &&
                (bool) $this->getAttribute('country')
                &&
                (bool) $this->getAttribute('city')
                &&
                (bool) $this->getAttribute('date')
                &&
                (bool) $this->getAttribute('start_time')
                &&
                (bool) $this->getAttribute('end_time')
                &&
                (bool) $this->getAttribute('activity_id')
                &&
                (bool) $this->getAttribute('language'))
            || (bool) $this->getAttribute('user') && $this->user !== null;
    }

    public function is_outsourced(): bool
    {
        return (bool) $this->getAttribute('user') && $this->user !== null;
    }
}
