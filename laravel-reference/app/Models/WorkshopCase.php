<?php

namespace App\Models;

use App\Enums\WorkshopCaseExpertStatus;
use App\Enums\WorkshopCaseStatus;
use App\Traits\OutsourceTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * App\Models\WorkshopCase
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
 * @property string|null $topic
 * @property string|null $activity_id
 * @property string|null $language_id
 * @property int|null $closed
 * @property WorkshopCaseStatus|null $status
 * @property int|null $expert_status
 * @property string|null $price
 * @property string|null $currency
 * @property string|null $expert_price
 * @property string|null $expert_currency
 * @property int|null $number_of_participants
 * @property Carbon|null $closed_at
 * @property Carbon|null $invoiceable_after
 * @property int|null $created_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read City|null $city
 * @property-read Company|null $company
 * @property-read Country|null $country
 * @property-read User|null $creator
 * @property-read Collection<int, WorkshopFeedback> $feedbacks
 * @property-read int|null $feedbacks_count
 * @property-read InvoiceWorkshopData|null $invoice_data
 * @property-read User|null $user
 * @property-read Workshop|null $workshop
 * @property-read Collection<int, WorkshopCaseEvent> $workshop_case_events
 * @property-read int|null $workshop_case_events_count
 *
 * @method static Builder|WorkshopCase newModelQuery()
 * @method static Builder|WorkshopCase newQuery()
 * @method static Builder|WorkshopCase query()
 * @method static Builder|WorkshopCase whereActivityId($value)
 * @method static Builder|WorkshopCase whereCityId($value)
 * @method static Builder|WorkshopCase whereClosed($value)
 * @method static Builder|WorkshopCase whereClosedAt($value)
 * @method static Builder|WorkshopCase whereCompanyContactEmail($value)
 * @method static Builder|WorkshopCase whereCompanyContactName($value)
 * @method static Builder|WorkshopCase whereCompanyContactPhone($value)
 * @method static Builder|WorkshopCase whereCompanyId($value)
 * @method static Builder|WorkshopCase whereCountryId($value)
 * @method static Builder|WorkshopCase whereCreatedAt($value)
 * @method static Builder|WorkshopCase whereCreatedBy($value)
 * @method static Builder|WorkshopCase whereCurrency($value)
 * @method static Builder|WorkshopCase whereDate($value)
 * @method static Builder|WorkshopCase whereEndTime($value)
 * @method static Builder|WorkshopCase whereExpertCurrency($value)
 * @method static Builder|WorkshopCase whereExpertId($value)
 * @method static Builder|WorkshopCase whereExpertPhone($value)
 * @method static Builder|WorkshopCase whereExpertPrice($value)
 * @method static Builder|WorkshopCase whereExpertStatus($value)
 * @method static Builder|WorkshopCase whereFullTime($value)
 * @method static Builder|WorkshopCase whereId($value)
 * @method static Builder|WorkshopCase whereInvoiceableAfter($value)
 * @method static Builder|WorkshopCase whereLanguageId($value)
 * @method static Builder|WorkshopCase whereNumberOfParticipants($value)
 * @method static Builder|WorkshopCase wherePrice($value)
 * @method static Builder|WorkshopCase whereStartTime($value)
 * @method static Builder|WorkshopCase whereStatus($value)
 * @method static Builder|WorkshopCase whereTopic($value)
 * @method static Builder|WorkshopCase whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class WorkshopCase extends Model
{
    use LogsActivity;
    use OutsourceTrait;

    protected $guarded = [];

    protected $casts = [
        'closed_at' => 'datetime',
        'invoiceable_after' => 'datetime',
        'status' => WorkshopCaseStatus::class,
        'expert_status' => WorkshopCaseExpertStatus::class,
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
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

    public function workshop(): BelongsTo
    {
        return $this->belongsTo(Workshop::class, 'activity_id', 'activity_id');
    }

    public function workshop_case_events(): HasMany
    {
        return $this->hasMany(WorkshopCaseEvent::class, 'workshop_case_id', 'id');
    }

    public function invoice_data(): HasOne
    {
        return $this->hasOne(InvoiceWorkshopData::class, 'workshop_case_id', 'id');
    }

    public function creator(): BelongsTo
    {
        return $this->BelongsTo(User::class, 'created_by');
    }

    public function feedbacks(): HasMany
    {
        return $this->hasMany(WorkshopFeedback::class, 'workshop_case_id', 'id');
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
        return OrgData::query()->where('company_id', $this->workshop->company_id)->where('country_id', $this->workshop->country_id)->first();
    }
}
