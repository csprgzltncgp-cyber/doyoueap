<?php

namespace App\Http\Controllers\Admin\EapOnline;

use App\Enums\OnsiteConsultationType;
use App\Http\Controllers\Controller;
use App\Models\EapOnline\OnsiteConsultation;
use App\Models\EapOnline\OnsiteConsultationDate;
use App\Models\EapOnline\OnsiteConsultationExpert;
use App\Services\OnsiteConsultationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OnsiteConsultationDateController extends Controller
{
    public function index(OnsiteConsultation $onsite_consultation): View
    {
        $experts = OnsiteConsultationExpert::query()->get();

        return view('admin.eap-online.onsite-consultation.edit', [
            'consultation' => $onsite_consultation,
            'dates' => $onsite_consultation->dates->sortBy('date'),
            'experts' => $experts,
        ]);
    }

    public function store(Request $request, OnsiteConsultationService $onsite_consultation_service): RedirectResponse
    {
        $rules = [
            'onsite_consultation_id' => 'required|exists:mysql_eap_online.onsite_consultations,id',
            'permission_id' => 'required|exists:permissions,id',
            'country_id' => 'required|exists:countries,id',
            'dates' => 'min:1',
            'times' => 'min:1',
            'from_time' => 'required',
            'to_time' => 'required',
        ];

        $onsite_consultation = $onsite_consultation_service->get_onsite_consultation_by_id($request->onsite_consultation_id);

        if (in_array($onsite_consultation->type, [OnsiteConsultationType::WITH_EXPERT, OnsiteConsultationType::ONLINE_WITH_EXPERT])) {
            $rules['expert'] = 'required|exists:mysql_eap_online.onsite_consultation_experts,id';
        }

        $request->validate($rules);

        collect($request->dates)->each(function ($date) use ($request, $onsite_consultation_service): void {

            // Check if date exists
            $onsite_consultation_date = $onsite_consultation_service->get_onsite_consultation_date_by_date($request->onsite_consultation_id, $date);

            if ($onsite_consultation_date !== null) {
                $onsite_consultation_service->store_appointment(
                    $onsite_consultation_date,
                    $request->times,
                    $request->expert
                );
            } else {
                $new_consultation_date = $onsite_consultation_service->store_date($request->onsite_consultation_id, $date);
                $onsite_consultation_service->store_appointment(
                    $new_consultation_date,
                    $request->times,
                    $request->expert
                );
            }
        });

        return redirect()->back();
    }

    public function delete(OnsiteConsultationDate $consultation_date): array
    {
        if (! $consultation_date->appointments->whereNotNull('user_id')->isEmpty()) {
            return ['status' => 3];
        }

        $consultation_date->appointments->map->delete();
        $consultation_date->delete();

        return ['status' => 1];
    }
}
