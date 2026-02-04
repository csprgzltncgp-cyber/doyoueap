<?php

namespace App\Models\EapOnline;

use App\Models\Cases;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\EapOnline\EapOnlineTherapyAppointment
 *
 * @property int $id
 * @property string $from
 * @property string $to
 * @property int $day
 * @property int $expert_id
 * @property int $language_id
 * @property int $permission_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, EapAppointmentBooking> $eap_bookings
 * @property-read int|null $eap_bookings_count
 * @property-read EapLanguage|null $eap_language
 * @property-read User|null $expert
 * @property Collection|null $reserved_dates
 *
 * @method static Builder|EapOnlineTherapyAppointment newModelQuery()
 * @method static Builder|EapOnlineTherapyAppointment newQuery()
 * @method static Builder|EapOnlineTherapyAppointment query()
 * @method static Builder|EapOnlineTherapyAppointment whereCreatedAt($value)
 * @method static Builder|EapOnlineTherapyAppointment whereDay($value)
 * @method static Builder|EapOnlineTherapyAppointment whereExpertId($value)
 * @method static Builder|EapOnlineTherapyAppointment whereFrom($value)
 * @method static Builder|EapOnlineTherapyAppointment whereId($value)
 * @method static Builder|EapOnlineTherapyAppointment whereLanguageId($value)
 * @method static Builder|EapOnlineTherapyAppointment wherePermissionId($value)
 * @method static Builder|EapOnlineTherapyAppointment whereTo($value)
 * @method static Builder|EapOnlineTherapyAppointment whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class EapOnlineTherapyAppointment extends Model
{
    protected $connection = 'mysql_eap_online';

    protected $table = 'online_therapy_appointments';

    protected $guarded = [];

    protected $casts = [
        'expert_id' => 'integer',
        'id' => 'integer',
    ];

    public static function boot(): void
    {
        parent::boot();

        self::deleting(function (self $appointment): void {
            $appointment_booking = DB::connection('mysql_eap_online')->table('online_appointment_bookings')
                ->where('online_therapy_appointment_id', $appointment->id)->first();

            if ($appointment_booking) {
                DB::connection('mysql_eap_online')->table('online_appointment_bookings')
                    ->where('online_therapy_appointment_id', $appointment->id)->delete();

                Cases::query()->findOrFail($appointment_booking->case_id)->delete();
            }
        });
    }

    protected function scopeNotCustom(Builder $query): void
    {
        $query->where('is_custom', false);
    }

    public function eap_language(): BelongsTo
    {
        return $this->belongsTo(EapLanguage::class);
    }

    public function expert(): BelongsTo
    {
        return $this->setConnection('mysql')->belongsTo(User::class, 'expert_id', 'id');
    }

    public function eap_bookings(): HasMany
    {
        return $this->hasMany(EapAppointmentBooking::class);
    }
}
