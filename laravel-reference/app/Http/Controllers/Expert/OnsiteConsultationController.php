<?php

namespace App\Http\Controllers\Expert;

use App\Http\Controllers\Controller;
use App\Models\EapOnline\OnsiteConsultationDateAppointment;
use Carbon\Carbon;

class OnsiteConsultationController extends Controller
{
    public function __invoke()
    {
        $appointments = OnsiteConsultationDateAppointment::query()
            ->with(['date', 'user'])
            ->whereNotNull('user_id') // Only show booked appointments
            ->where('onsite_consultation_expert_id', 7) // Only show appointments for `Naveet Dowson`
            ->whereHas('date', function ($query): void {
                $query->where('date', '>=', Carbon::now()->format('Y-m-d'));
            })
            ->get();

        return view('expert.onsite-consultation.index', ['appointments' => $appointments]);
    }
}
