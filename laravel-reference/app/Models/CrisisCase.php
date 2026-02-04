<?php

namespace App\Models;

use App\Enums\CrisisCaseStatus;
use App\Traits\OutsourceTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Carbon;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * App\Models\CrisisCase
 *
 * @property int $id
 * @property int|null $company_id
 * @property string|null $company_contact_name
 * @property string|null $company_contact_email
 * @property string|null $company_contact_phone
 * @property int|null $country_id
 * @property int|null $city_id
 * @property int|null $expert_id
 * @property string|null $expert_phone
 * @property string|null $date
 * @property string|null $start_time
 * @property string|null $end_time
 * @property string|array|null $full_time
 * @property string|null $activity_id
 * @property string|null $language_id
 * @property int|null $closed
 * @property int|null $status
 * @property int|null $expert_status
 * @property string|null $price
 * @property string|null $currency
 * @property string|null $expert_price
 * @property string|null $expert_currency
 * @property int|null $number_of_participants
 * @property string|null $closed_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read City|null $city
 * @property-read Company|null $company
 * @property-read Country|null $country
 * @property-read Collection<int, CrisisCaseEvent> $crisis_case_events
 * @property-read int|null $crisis_case_events_count
 * @property-read CrisisIntervention|null $crisis_intervention
 * @property-read InvoiceCrisisData|null $invoice_data
 * @property-read User|null $user
 *
 * @method static Builder|CrisisCase newModelQuery()
 * @method static Builder|CrisisCase newQuery()
 * @method static Builder|CrisisCase query()
 * @method static Builder|CrisisCase whereActivityId($value)
 * @method static Builder|CrisisCase whereCityId($value)
 * @method static Builder|CrisisCase whereClosed($value)
 * @method static Builder|CrisisCase whereClosedAt($value)
 * @method static Builder|CrisisCase whereCompanyContactEmail($value)
 * @method static Builder|CrisisCase whereCompanyContactName($value)
 * @method static Builder|CrisisCase whereCompanyContactPhone($value)
 * @method static Builder|CrisisCase whereCompanyId($value)
 * @method static Builder|CrisisCase whereCountryId($value)
 * @method static Builder|CrisisCase whereCreatedAt($value)
 * @method static Builder|CrisisCase whereCurrency($value)
 * @method static Builder|CrisisCase whereDate($value)
 * @method static Builder|CrisisCase whereEndTime($value)
 * @method static Builder|CrisisCase whereExpertCurrency($value)
 * @method static Builder|CrisisCase whereExpertId($value)
 * @method static Builder|CrisisCase whereExpertPhone($value)
 * @method static Builder|CrisisCase whereExpertPrice($value)
 * @method static Builder|CrisisCase whereExpertStatus($value)
 * @method static Builder|CrisisCase whereFullTime($value)
 * @method static Builder|CrisisCase whereId($value)
 * @method static Builder|CrisisCase whereLanguageId($value)
 * @method static Builder|CrisisCase whereNumberOfParticipants($value)
 * @method static Builder|CrisisCase wherePrice($value)
 * @method static Builder|CrisisCase whereStartTime($value)
 * @method static Builder|CrisisCase whereStatus($value)
 * @method static Builder|CrisisCase whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class CrisisCase extends Model
{
    use LogsActivity;
    use OutsourceTrait;

    protected $guarded = [];

    protected $casts = [
        'status' => CrisisCaseStatus::class,
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
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'expert_id');
    }

    public function crisis_intervention(): BelongsTo
    {
        return $this->belongsTo(CrisisIntervention::class, 'activity_id', 'activity_id');
    }

    public function crisis_case_events(): HasMany
    {
        return $this->hasMany(CrisisCaseEvent::class, 'crisis_case_id', 'id');
    }

    public function invoice_data(): HasOne
    {
        return $this->hasOne(InvoiceCrisisData::class, 'crisis_case_id', 'id');
    }

    /**
     * @return MorphMany<ActivityPlanMember>
     */
    public function activity_plan_members(): MorphMany
    {
        return $this->morphMany(ActivityPlanMember::class, 'activity_plan_memberable');
    }

    public function org_data()
    {
        return OrgData::query()->where('company_id', $this->crisis_intervention->company_id)->where('country_id', $this->crisis_intervention->country_id)->first();
    }
}
