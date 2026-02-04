<?php

namespace App\Models\EapOnline;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\EapOnline\OnsiteConsultationDateAppointment
 *
 * @mixin Builder
 *
 * @property int $id
 * @property int $onsite_consultation_date_id
 * @property int $user_id
 * @property Carbon $from
 * @property Carbon $to
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property OnsiteConsultationDate $date
 *
 * @method static Builder|OnsiteConsultationDateAppointment newModelQuery()
 * @method static Builder|OnsiteConsultationDateAppointment newQuery()
 * @method static Builder|OnsiteConsultationDateAppointment query()
 * @method static Builder|OnsiteConsultationDateAppointment whereAllLanguages($value)
 * @method static Builder|OnsiteConsultationDateAppointment whereCreatedAt($value)
 * @method static Builder|OnsiteConsultationDateAppointment whereDescriptionFirstLine($value)
 * @method static Builder|OnsiteConsultationDateAppointment whereDescriptionSecondLine($value)
 * @method static Builder|OnsiteConsultationDateAppointment whereId($value)
 * @method static Builder|OnsiteConsultationDateAppointment whereLanguage($value)
 * @method static Builder|OnsiteConsultationDateAppointment whereLink($value)
 * @method static Builder|OnsiteConsultationDateAppointment whereLongTitle($value)
 * @method static Builder|OnsiteConsultationDateAppointment whereShortTitle($value)
 * @method static Builder|OnsiteConsultationDateAppointment whereSlug($value)
 * @method static Builder|OnsiteConsultationDateAppointment whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class OnsiteConsultationDateAppointment extends Model
{
    protected $connection = 'mysql_eap_online';

    protected $fillable = [
        'onsite_consultation_date_id',
        'onsite_consultation_expert_id',
        'user_id',
        'from',
        'to',
    ];

    protected $casts = [
        'from' => 'datetime',
        'to' => 'datetime',
    ];

    public function date(): BelongsTo
    {
        return $this->belongsTo(OnsiteConsultationDate::class, 'onsite_consultation_date_id', 'id');
    }

    public function expert(): BelongsTo
    {
        return $this->belongsTo(OnsiteConsultationExpert::class, 'onsite_consultation_expert_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(EapUser::class, 'user_id', 'id');
    }
}
