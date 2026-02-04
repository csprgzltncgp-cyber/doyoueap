<?php

namespace App\Models\EapOnline;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\EapOnline\OnsiteConsultationDate
 *
 * @mixin Builder
 *
 * @property int $id
 * @property int $onsite_consultation_id
 * @property Carbon $date
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property OnsiteConsultation $consultation
 * @property Collection<int, OnsiteConsultationDateAppointment> $appointments
 *
 * @method static Builder|OnsiteConsultationDate newModelQuery()
 * @method static Builder|OnsiteConsultationDate newQuery()
 * @method static Builder|OnsiteConsultationDate query()
 * @method static Builder|OnsiteConsultationDate whereAllLanguages($value)
 * @method static Builder|OnsiteConsultationDate whereCreatedAt($value)
 * @method static Builder|OnsiteConsultationDate whereDescriptionFirstLine($value)
 * @method static Builder|OnsiteConsultationDate whereDescriptionSecondLine($value)
 * @method static Builder|OnsiteConsultationDate whereId($value)
 * @method static Builder|OnsiteConsultationDate whereLanguage($value)
 * @method static Builder|OnsiteConsultationDate whereLink($value)
 * @method static Builder|OnsiteConsultationDate whereLongTitle($value)
 * @method static Builder|OnsiteConsultationDate whereShortTitle($value)
 * @method static Builder|OnsiteConsultationDate whereSlug($value)
 * @method static Builder|OnsiteConsultationDate whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class OnsiteConsultationDate extends Model
{
    protected $connection = 'mysql_eap_online';

    protected $fillable = [
        'onsite_consultation_id',
        'date',
    ];

    protected $casts = [
        'date' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::deleting(function (OnsiteConsultationDate $onsite_consultation_date): void {
            $onsite_consultation_date->appointments->map->delete();
        });
    }

    public function consultation(): BelongsTo
    {
        return $this->belongsTo(OnsiteConsultation::class, 'id');
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(OnsiteConsultationDateAppointment::class, 'onsite_consultation_date_id', 'id');
    }
}
