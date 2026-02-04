<?php

namespace App\Http\Controllers\Admin\EapOnline;

use App\Http\Controllers\Controller;
use App\Models\Cases;
use App\Models\Country;
use App\Models\EapOnline\EapExpertDayOff;
use App\Models\EapOnline\EapLanguage;
use App\Models\EapOnline\EapOnlineTherapyAppointment;
use App\Models\Permission;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EapVideoTherapyController extends Controller
{
    public function actions()
    {
        return view('admin.eap-online.video_therapy_actions');
    }

    public function timetable_part_1()
    {
        $languages = EapLanguage::query()->orderBy('name')->get();
        $permissions = Permission::query()->get();

        return view('admin.eap-online.video_therapy.part_1', ['languages' => $languages, 'permissions' => $permissions]);
    }

    public function timetable_part_2(Request $request)
    {
        $language_id = (int) $request->input('language');
        $permission_id = (int) $request->input('permission');

        $appointments = EapOnlineTherapyAppointment::query()
            ->notCustom()
            ->where('language_id', $language_id)
            ->where('permission_id', $permission_id)
            ->with('expert')
            ->orderBy('from')
            ->get();

        $formatted_appointments_for_calendar = json_encode($this->format_appointment($appointments), JSON_THROW_ON_ERROR);
        $countries = EapLanguage::query()->find($language_id)->countries()->get()->pluck('id')->toArray();

        $experts = User::query()
            ->with('expertCountries')
            ->orderBy('name')
            ->where('type', 'expert')
            ->where('active', 1)
            ->where('locked', 0)
            ->where(function ($q) use ($countries): void {
                $q->whereIn('country_id', $countries)->orderBy('name')
                    ->orWhereHas('expertCountries', function ($query) use ($countries): void {
                        $query->whereIn('country_id', $countries);
                    });
            })->get()->filter(fn ($expert) => $expert->hasPermission($permission_id));

        return view('admin.eap-online.video_therapy.part_2', ['experts' => $experts, 'appointments' => $appointments, 'formatted_appointments_for_calendar' => $formatted_appointments_for_calendar, 'permission_id' => $permission_id, 'language_id' => $language_id]);
    }

    public function save_appointment(Request $request)
    {
        $request->validate([
            'from_time' => 'required',
            'to_time' => 'required',
            'expert' => 'required',
            'language_id' => 'required',
            'permission_id' => 'required',
        ]);

        $language_id = (int) $request->input('language_id');
        $permission_id = (int) $request->input('permission_id');

        EapOnlineTherapyAppointment::query()->create([
            'to' => $request->input('to_time'),
            'from' => $request->input('from_time'),
            'expert_id' => $request->input('expert'),
            'day' => $request->input('day'),
            'language_id' => $language_id,
            'permission_id' => $permission_id,
        ]);

        return redirect()->back();
    }

    public function edit_appointment(Request $request, $appointment_id)
    {
        try {
            $request->validate([
                'edit_day' => 'required',
                'edit_from_time' => 'required',
                'edit_to_time' => 'required',
                'edit_expert' => 'required',
            ]);

            EapOnlineTherapyAppointment::query()->notCustom()->where('id', $appointment_id)->update([
                'day' => $request->input('edit_day'),
                'from' => $request->input('edit_from_time'),
                'to' => $request->input('edit_to_time'),
                'expert_id' => $request->input('edit_expert'),
            ]);

        } catch (ModelNotFoundException) {
            abort(404);
        }

        return redirect()->back();
    }

    public function delete_appointment(Request $request)
    {
        try {
            EapOnlineTherapyAppointment::query()->notCustom()->findOrFail($request->input('appointment_id'))->delete();
        } catch (ModelNotFoundException) {
            abort(404);
        }

        return redirect()->back();
    }

    public function end_therapy(Request $request): void
    {
        $room_id = $request->input('room_id');

        $online_booking = DB::connection('mysql_eap_online')->table('online_appointment_bookings')->where('room_id', $room_id)->first();
        if ($online_booking) {

            DB::connection('mysql_eap_online')->table('online_appointment_bookings')->where('room_id', $room_id)->update([
                'consultation_end' => Carbon::now(),
            ]);

            // If chat consultation(82), delete chat messages
            if ($online_booking->consultation_type === 82) {
                DB::connection('mysql_eap_online')->table('chat_messages')->where('room_id', $room_id)->delete();
            }
        }

        $intake_booking = DB::connection('mysql_eap_online')->table('intake_bookings')->where('room_id', $room_id)->first();
        if ($intake_booking) {
            DB::connection('mysql_eap_online')->table('intake_bookings')->where('room_id', $room_id)->update([
                'consultation_end' => Carbon::now(),
            ]);
            $case = Cases::query()->where('id', $intake_booking->case_id)->first();
            $consultation_type = $case->values->where('case_input_id', 24)->first()->value;
            // If chat consultation(82), delete chat messages
            if ($consultation_type == 82) {
                DB::connection('mysql_eap_online')->table('chat_messages')->where('room_id', $room_id)->delete();
            }
        }
    }

    public function permissions_view()
    {
        $countries = Country::query()->orderBy('name')->get();

        return view('admin.eap-online.video_therapy.permissions.view', ['countries' => $countries]);
    }

    public function permissions_store(Request $request)
    {
        session()->flash('permission-saved', true);

        return redirect()->back();
    }

    /**
     * @return array<mixed, array<'daysOfWeek'|'endTime'|'id'|'startTime'|'title', mixed>>
     */
    private function format_appointment($appointments): array
    {
        $formatted = [];
        foreach ($appointments as $appointment) {
            $formatted[] = [
                'title' => User::query()->find($appointment->expert_id)->name,
                'daysOfWeek' => [$appointment->day],
                'startTime' => $appointment->from,
                'endTime' => $appointment->to,
                'id' => $appointment->id,
            ];
        }

        return $formatted;
    }

    /**
     * @return array<mixed, array<'end'|'id'|'start'|'title', mixed>>
     */
    private function format_day_off($appointments): array
    {
        $formatted = [];
        foreach ($appointments as $appointment) {
            $formatted[] = [
                'title' => User::query()->find($appointment->expert_id)->name,
                'start' => $appointment->from,
                'end' => $appointment->to,
                'id' => $appointment->id,
            ];
        }

        return $formatted;
    }

    public function expert_day_off_1()
    {
        $languages = EapLanguage::query()->orderBy('name')->get();
        $permissions = Permission::query()->get();

        return view('admin.eap-online.video_therapy.day_off.part_1', ['languages' => $languages, 'permissions' => $permissions]);
    }

    public function expert_day_off_2(Request $request)
    {
        $language_id = (int) $request->input('language');
        $permission_id = (int) $request->input('permission');

        $days_off = EapExpertDayOff::query()
            ->where('language_id', $language_id)
            ->where('permission_id', $permission_id)
            ->with('expert')
            ->orderBy('from')
            ->get()->filter(fn ($day_off): bool => ! empty($day_off->expert));

        $experts_off = $days_off->unique('expert')->pluck('expert');
        $formatted_days_off_for_calendar = json_encode($this->format_day_off($days_off), JSON_THROW_ON_ERROR);
        $countries = EapLanguage::query()->find($language_id)->countries()->get()->pluck('id')->toArray();

        $experts = User::query()
            ->with('expertCountries')
            ->where('type', 'expert')
            ->orderBy('name')
            ->where(function ($q) use ($countries): void {
                $q->whereIn('country_id', $countries)->orderBy('name')
                    ->orWhereHas('expertCountries', function ($query) use ($countries): void {
                        $query->whereIn('country_id', $countries);
                    });
            })->get()->filter(fn ($expert) => $expert->hasPermission($permission_id));

        return view('admin.eap-online.video_therapy.day_off.part_2', ['experts' => $experts, 'experts_off' => $experts_off, 'days_off' => $days_off, 'formatted_days_off_for_calendar' => $formatted_days_off_for_calendar, 'permission_id' => $permission_id, 'language_id' => $language_id]);
    }

    public function save_day_off(Request $request)
    {
        $request->validate([
            'from_time' => 'required',
            'to_time' => 'required',
            'expert' => 'required',
            'language_id' => 'required',
            'permission_id' => 'required',
        ]);

        $language_id = (int) $request->input('language_id');
        $permission_id = (int) $request->input('permission_id');

        EapExpertDayOff::query()->create([
            'to' => $request->input('to_time'),
            'from' => $request->input('from_time'),
            'expert_id' => $request->input('expert'),
            'language_id' => $language_id,
            'permission_id' => $permission_id,
        ]);

        return redirect()->back();
    }

    public function edit_day_off(Request $request, $appointment_id)
    {
        try {
            $request->validate([
                'edit_from_time' => 'required',
                'edit_to_time' => 'required',
                'edit_expert' => 'required',
            ]);

            EapExpertDayOff::query()->where('id', $appointment_id)->update([
                'from' => $request->input('edit_from_time'),
                'to' => $request->input('edit_to_time'),
                'expert_id' => $request->input('edit_expert'),
            ]);

        } catch (ModelNotFoundException) {
            abort(404);
        }

        return redirect()->back();
    }

    public function delete_day_off(Request $request)
    {
        try {
            EapExpertDayOff::query()->findOrFail($request->input('expert_day_off_id'))->delete();
        } catch (ModelNotFoundException) {
            abort(404);
        }

        return redirect()->back();
    }
}
