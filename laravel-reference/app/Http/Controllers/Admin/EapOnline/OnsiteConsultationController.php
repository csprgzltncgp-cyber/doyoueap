<?php

namespace App\Http\Controllers\Admin\EapOnline;

use App\Http\Controllers\Controller;
use App\Models\EapOnline\OnsiteConsultation;

class OnsiteConsultationController extends Controller
{
    public function index()
    {
        $consultations = OnsiteConsultation::query()->with('place')->get();

        return view('admin.eap-online.onsite-consultation.index', ['consultations' => $consultations]);
    }

    public function delete(OnsiteConsultation $consultation): void
    {
        $consultation->languages()->detach();
        $consultation->delete();
    }
}
