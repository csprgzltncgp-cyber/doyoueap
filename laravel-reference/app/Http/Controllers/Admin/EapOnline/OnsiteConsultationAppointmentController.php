<?php

namespace App\Http\Controllers\Admin\EapOnline;

use App\Http\Controllers\Controller;
use App\Models\EapOnline\OnsiteConsultationDateAppointment;
use App\Services\OnsiteConsultationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class OnsiteConsultationAppointmentController extends Controller
{
    public function edit(Request $request, OnsiteConsultationService $onsite_consultation_service): RedirectResponse
    {
        $request->validate([
            'appointment_id' => 'required|exists:mysql_eap_online.onsite_consultation_date_appointments,id',
            'edit_from_time' => 'required',
            'edit_to_time' => 'required',
        ]);

        $onsite_consultation_service->update_appointment($request);

        return redirect()->back();
    }

    public function delete(OnsiteConsultationDateAppointment $appointment, OnsiteConsultationService $onsite_consultation_service): RedirectResponse
    {
        if (! empty($appointment->user_id)) {
            session()->flash('appointment-already-booked');

            return redirect()->back();
        }

        $onsite_consultation_service->delete_appointment($appointment);

        return redirect()->back();
    }
}
