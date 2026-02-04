<?php

namespace App\Models\EapOnline;

use App\Enums\OnsiteConsultationType;
use App\Models\Company;
use App\Models\Country;
use App\Models\Permission;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\EapOnline\OnsiteConsultation
 *
 * @mixin Builder
 *
 * @property int $id
 * @property OnsiteConsultationType $type
 * @property int $company_id
 * @property int $country_id
 * @property int $permission_id
 * @property string $onsite_consultation_place_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Company $company
 * @property Country $company
 * @property Permission $company
 * @property Collection<int, OnsiteConsultationDate> $dates
 * @property-read Collection<int, EapLanguage> $languages
 *
 * @method static Builder|OnsiteConsultation newModelQuery()
 * @method static Builder|OnsiteConsultation newQuery()
 * @method static Builder|OnsiteConsultation query()
 * @method static Builder|OnsiteConsultation whereAllLanguages($value)
 * @method static Builder|OnsiteConsultation whereCreatedAt($value)
 * @method static Builder|OnsiteConsultation whereDescriptionFirstLine($value)
 * @method static Builder|OnsiteConsultation whereDescriptionSecondLine($value)
 * @method static Builder|OnsiteConsultation whereId($value)
 * @method static Builder|OnsiteConsultation whereLanguage($value)
 * @method static Builder|OnsiteConsultation whereLink($value)
 * @method static Builder|OnsiteConsultation whereLongTitle($value)
 * @method static Builder|OnsiteConsultation whereShortTitle($value)
 * @method static Builder|OnsiteConsultation whereSlug($value)
 * @method static Builder|OnsiteConsultation whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class OnsiteConsultation extends Model
{
    protected $connection = 'mysql_eap_online';

    protected $fillable = [
        'company_id',
        'country_id',
        'permission_id',
        'onsite_consultation_place_id',
        'type',
    ];

    protected $casts = [
        'type' => OnsiteConsultationType::class,
    ];

    protected static function booted(): void
    {
        static::deleting(function (OnsiteConsultation $onsite_consultation): void {
            $onsite_consultation->dates->map->delete();
        });
    }

    public function company(): BelongsTo
    {
        return $this->setConnection('mysql')->belongsTo(Company::class, 'company_id', 'id');
    }

    public function country(): BelongsTo
    {
        return $this->setConnection('mysql')->belongsTo(Country::class, 'country_id', 'id');
    }

    public function permission(): BelongsTo
    {
        return $this->setConnection('mysql')->belongsTo(Permission::class, 'permission_id', 'id');
    }

    public function dates(): HasMany
    {
        return $this->hasMany(OnsiteConsultationDate::class, 'onsite_consultation_id', 'id');
    }

    public function place(): BelongsTo
    {
        return $this->belongsTo(OnsiteConsultationPlace::class, 'onsite_consultation_place_id', 'id');
    }

    public function languages(): BelongsToMany
    {
        return $this->belongsToMany(EapLanguage::class, 'language_onsite_consultation', 'onsite_consultation_id', 'language_id');
    }

    public function booked_appointments(): ?Collection
    {
        $appointments = $this->dates->pluck('appointments')->first();

        if (! $appointments) {
            return null;
        }

        return $appointments->reject(fn ($appointment): bool => is_null($appointment->user_id));
    }
}
