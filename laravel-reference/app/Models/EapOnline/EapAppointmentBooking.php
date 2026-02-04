<?php

namespace App\Models\EapOnline;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\EapOnline\EapAppointmentBooking
 *
 * @property int $id
 * @property int $user_id
 * @property int $language_id
 * @property int $permission_id
 * @property int $online_therapy_appointment_id
 * @property string $date
 * @property int $is_client_connected
 * @property int $case_id
 * @property string $room_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read EapLanguage|null $eap_language
 * @property-read EapOnlineTherapyAppointment|null $eap_video_therapy_appointment
 *
 * @method static Builder|EapAppointmentBooking newModelQuery()
 * @method static Builder|EapAppointmentBooking newQuery()
 * @method static Builder|EapAppointmentBooking query()
 * @method static Builder|EapAppointmentBooking whereCaseId($value)
 * @method static Builder|EapAppointmentBooking whereCreatedAt($value)
 * @method static Builder|EapAppointmentBooking whereDate($value)
 * @method static Builder|EapAppointmentBooking whereId($value)
 * @method static Builder|EapAppointmentBooking whereIsClientConnected($value)
 * @method static Builder|EapAppointmentBooking whereLanguageId($value)
 * @method static Builder|EapAppointmentBooking wherePermissionId($value)
 * @method static Builder|EapAppointmentBooking whereRoomId($value)
 * @method static Builder|EapAppointmentBooking whereUpdatedAt($value)
 * @method static Builder|EapAppointmentBooking whereUserId($value)
 * @method static Builder|EapAppointmentBooking whereVideoTherapyAppointmentId($value)
 *
 * @mixin \Eloquent
 */
class EapAppointmentBooking extends Model
{
    protected $connection = 'mysql_eap_online';

    protected $table = 'online_appointment_bookings';

    protected $guarded = [];

    public function eap_language(): BelongsTo
    {
        return $this->belongsTo(EapLanguage::class);
    }

    public function eap_video_therapy_appointment(): BelongsTo
    {
        return $this->belongsTo(EapOnlineTherapyAppointment::class);
    }
}
