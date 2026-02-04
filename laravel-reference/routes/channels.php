<?php

use App\Models\EapOnline\EapOnlineTherapyAppointment;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
 */

Broadcast::channel('chat-therapy.{roomId}', function ($user, $roomId): array|false {
    // Online booking logic
    $online_booking = DB::connection('mysql_eap_online')
        ->table('online_appointment_bookings')
        ->where('room_id', $roomId)
        ->first();

    if ($online_booking) {
        $appointment = EapOnlineTherapyAppointment::query()
            ->where('id', $online_booking->online_therapy_appointment_id)
            ->first();

        if ($appointment && (int) $appointment->expert_id === (int) $user->id) {
            return ['name' => $user->name];
        }
    }

    // Intake booking logic
    $intake_booking = DB::connection('mysql_eap_online')
        ->table('intake_bookings')
        ->where('room_id', $roomId)
        ->first();

    if (! $intake_booking) {
        return false;
    }

    if (! in_array($intake_booking->case_id, $user->cases->pluck('id')->toArray())) {
        return false;
    }

    return ['name' => $user->name];
});
