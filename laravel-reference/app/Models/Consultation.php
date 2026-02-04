<?php

namespace App\Models;

use App\Enums\CaseExpertStatus;
use App\Mail\Consultation2Weeks;
use App\Models\EapOnline\EapLanguageLines;
use App\Models\EapOnline\EapOnlineTherapyAppointment;
use App\Models\EapOnline\EapUser;
use App\Notifications\EapOnline\EapMessageCreated;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * App\Models\Consultation
 *
 * @property int $id
 * @property int $case_id Megadja, hogy melyik esethez tartozik a konzultáció
 * @property int $user_id Megadja, hogy melyik szakértőt rendelték a konzultációhoz
 * @property int $permission_id Megadja, hogy melyik jogosultság lett felhasználva
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Cases $case
 * @property-read User $expert
 *
 * @method static Builder|Consultation newModelQuery()
 * @method static Builder|Consultation newQuery()
 * @method static Builder|Consultation onlyTrashed()
 * @method static Builder|Consultation query()
 * @method static Builder|Consultation whereCaseId($value)
 * @method static Builder|Consultation whereCreatedAt($value)
 * @method static Builder|Consultation whereDeletedAt($value)
 * @method static Builder|Consultation whereId($value)
 * @method static Builder|Consultation wherePermissionId($value)
 * @method static Builder|Consultation whereUpdatedAt($value)
 * @method static Builder|Consultation whereUserId($value)
 * @method static Builder|Consultation withTrashed()
 * @method static Builder|Consultation withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Consultation extends Model
{
    use SoftDeletes;

    protected $fillable = ['case_id', 'user_id', 'permission_id', 'created_at'];

    public function expert(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function case(): BelongsTo
    {
        return $this->belongsTo(Cases::class, 'case_id');
    }

    public static function addConsultation($request): array
    {
        $case = Cases::query()->findOrFail($request->case_id);
        $case->experts->last();

        $online_appointment_booking = DB::connection('mysql_eap_online')->table('online_appointment_bookings')->where('case_id', $case->id)->get()->sortByDesc('id')->first();

        // IF online expert/date select appoitnment and custom appoitnment al   reday exists return error
        if ($online_appointment_booking && ! $request->appointment_id && self::custom_date_exists($request->date)) {
            return ['custom_consultation_date_exists' => true];
        }

        // ha van már a mai napra hozzáadva konzultáció, akkor hibát kell dobnunk
        if ($case->consultations/* ->where('user_id',$current_expert->id) */ ->whereBetween('created_at', [Carbon::parse($request->date)->startOfDay(), Carbon::parse($request->date)->endOfDay()])->count()) {
            return ['consultation_today_exists' => true];
        }

        if (! $case->has_more_consultations()) {
            return ['more_consultation_can_be_added' => false];
        }

        if ($case->getRawOriginal('status') == 'assigned_to_expert') {
            $case->status = 'employee_contacted';
            $case->employee_contacted_at = Carbon::now();
            $case->save();
        }

        // ha az uj ules es az azt megelozo kozott tobb mint 2 het telt el
        $previous_consultation = $case->consultations()->where('created_at', '<', Carbon::parse($request->date))->orderBy('created_at', 'desc')->first();

        if ($previous_consultation && Carbon::parse($previous_consultation->created_at)->diffInDays(Carbon::parse($request->date)) > 14) {
            $new_consultation_date = Carbon::parse($request->date)->format('Y-m-d H:i');
            $previous_consultation_date = Carbon::parse($previous_consultation->created_at)->format('Y-m-d H:i');
            // Mail::to('barbara.kiss@cgpeu.com')->send(new Consultation2Weeks($case->case_identifier, $new_consultation_date, $previous_consultation_date));
        }

        $case->experts()->syncWithoutDetaching([Auth::user()->id => ['accepted' => CaseExpertStatus::ACCEPTED->value]]);

        $consultation_date = Carbon::parse($request->date);

        $userId = Auth::user()->type === 'admin'
            ? $case->case_accepted_expert()->id
            : Auth::id();

        $c = self::query()->create([
            'case_id' => $case->id,
            'user_id' => $userId,
            'permission_id' => $case->case_type->value,
            'created_at' => $consultation_date,
        ]);

        // Send notification to client when intake has room_id (chat, video)
        if ($request->eap_user_id && $case->consultations->count() > 1) {
            $c->send_notification('new', $request->eap_user_id, $case);
        }

        // CREATE NEW ONLINE THERAPY APPOINTMENT BOOKING IN EAP DATABASE

        $room_id = null;
        if ($online_appointment_booking) {
            $appointment_id = $request->appointment_id ?? self::get_custom_appointment_id(
                date: $request->date,
                language_id: $online_appointment_booking->language_id,
                permission_id: $online_appointment_booking->permission_id,
            );

            try {
                $room_id = Http::timeout(15)->withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer '.config('app.cgp_internal_authentication_token'),
                ])->post(config('app.eap_online_url').'/api/create_online_therapy_booking', [
                    'case_id' => (int) $c->case_id,
                    'expert_id' => (int) $c->user_id,
                    'permission_id' => (int) $c->permission_id,
                    'date' => (string) Carbon::parse($c->getRawOriginal('created_at'))->format('Y-m-d'),
                    'appointment_id' => (int) $appointment_id,
                    'consultation_type' => (int) $case->values->where('case_input_id', 24)->first()->value,
                ]);

                if ($room_id->successful()) {
                    $data = json_decode($room_id->body(), true);
                    $room_id = $data['room_id'];

                    if ($request->eap_user_id) {
                        $eap_user = EapUser::query()->find($request->eap_user_id);
                        $language_line = EapLanguageLines::query()->where('key', 'new_operator_message_notification')->first();
                        $message = data_get($language_line->text, ($eap_user->language) ? $eap_user->language->code : 'en');

                        EapUser::query()->find($request->eap_user_id)->notify(new EapMessageCreated($message));
                    }
                } else {
                    Log::info("Failed to create a room for case_id: {$c->case_id}: {$room_id->body()}");

                    $room_id = null;
                }
            } catch (Exception $e) {
                Log::info("Failed to create a new appointment booking for case_id: {$c->case_id}: {$e->getMessage()}");

                $room_id = null;
            }
        }
        // CREATE NEW ONLINE THERAPY APPOINTMENT BOOKING IN EAP DATABASE
        $available_consultations = optional($case->values->where('case_input_id', 21)->first())->input_value->value;
        $more_consultation_can_be_added = ($available_consultations - $case->usedPermissions() > 0) && $case->can_add_more_consultation((bool) $online_appointment_booking);

        return [
            'id' => $c->id,
            'number' => $case->usedPermissions(),
            'time' => $consultation_date->format('Y-m-d H:i'),
            'more_consultation_can_be_added' => $more_consultation_can_be_added,
            'customerSatisfactionModal' => $case->shouldShowCustomerSatisfactionModal(),
            'room_id' => $room_id,
        ];
    }

    public function delete_consultation(?int $booking_id, ?string $room_id, ?int $eap_user_id = null): void
    {
        // If online booking, force delete the consultation.
        $case_id = $this->case_id;

        if ($booking_id) {

            // IF the deleted consultation is the first, delete the entire case
            if ($this->case->consultations()->withTrashed()->count() <= 1) {
                Cases::delete_case($this->case_id);
            } else {
                $this->forceDelete();
            }
            try {
                $delete_booking = Http::timeout(15)->withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer '.config('app.cgp_internal_authentication_token'),
                ])->post(config('app.eap_online_url').'/api/delete_online_therapy_booking', [
                    'online_boooking_id' => (int) $booking_id,
                ]);

                if ($delete_booking->successful()) {
                    $eap_user = EapUser::query()->find($eap_user_id);
                    $language_line = EapLanguageLines::query()->where('key', 'new_operator_message_notification')->first();
                    $message = data_get($language_line->text, ($eap_user->language) ? $eap_user->language->code : 'en');

                    EapUser::query()->find($eap_user_id)->notify(new EapMessageCreated($message));
                } else {
                    Log::info("Failed to delete online appointment booking (id:{$booking_id}) in EAP database: {$delete_booking->body()}");
                    $delete_booking = null;
                }
            } catch (Exception $e) {
                Log::info("Failed to delete online appointment booking (id:{$booking_id}) in EAP database: {$e->getMessage()}");
            }
        } else {
            $this->delete();
        }
        // Send notification to client when intake has room_id (chat, video)
        if (! $room_id && ! $eap_user_id) {
            return;
        }

        if ($booking_id) {
            return;
        }

        $case = Cases::query()
            ->where('id', $case_id)
            ->first();

        $this->send_notification('delete', $eap_user_id, $case);
    }

    public function send_notification(string $type, int $eap_user_id, Cases $case): void
    {
        // Send email notification
        try {
            $notification = Http::timeout(15)->withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer '.config('app.cgp_internal_authentication_token'),
            ])->post(config('app.eap_online_url').'/api/client_notification_email', [
                'case_id' => (int) $case->id,
                'type' => $type,
                'date' => $this->getRawOriginal('created_at'),
                'expert' => optional($case->case_accepted_expert())->name,

            ]);

            $eap_user = EapUser::query()->find($eap_user_id);
            $language_line = EapLanguageLines::query()->where('key', 'new_operator_message_notification')->first();
            $message = data_get($language_line->text, ($eap_user->language) ? $eap_user->language->code : 'en');

            EapUser::query()->find($eap_user_id)->notify(new EapMessageCreated($message));

            if (! $notification->successful()) {
                Log::info("Failed to send appointment {$type} mail for EAP user in case_id: {$this->case_id}: {$notification->body()}");
            }
        } catch (Exception $e) {
            Log::info("Failed to send  appointment {$type} mail for EAP user in case_id: {$this->case_id}: {$e->getMessage()}");
        }
    }

    private static function get_custom_appointment_id(string $date, int $language_id, int $permission_id): int
    {
        $custom_appointment = EapOnlineTherapyAppointment::query()
            ->create([
                'is_custom' => true,
                'from' => Carbon::parse($date)->format('H:i'),
                'to' => Carbon::parse($date)->addHour()->format('H:i'),
                'day' => Carbon::parse($date)->dayOfWeek,
                'expert_id' => Auth::user()->id,
                'language_id' => $language_id,
                'permission_id' => $permission_id,
            ]);

        return $custom_appointment->id;
    }

    private static function custom_date_exists(string $date): bool
    {
        return EapOnlineTherapyAppointment::query()->where([
            'expert_id' => Auth::user()->id,
            'from' => Carbon::parse($date)->format('H:i'),
            'to' => Carbon::parse($date)->addHour()->format('H:i'),
            'day' => Carbon::parse($date)->dayOfWeek,
            'is_custom' => 0,
        ])->exists();
    }
}
