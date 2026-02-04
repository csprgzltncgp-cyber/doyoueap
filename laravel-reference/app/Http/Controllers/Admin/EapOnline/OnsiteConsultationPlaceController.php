<?php

namespace App\Http\Controllers\Admin\EapOnline;

use App\Http\Controllers\Controller;
use App\Models\EapOnline\OnsiteConsultationPlace;
use App\Services\OnsiteConsultationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class OnsiteConsultationPlaceController extends Controller
{
    public function index()
    {
        $places = OnsiteConsultationPlace::query()->get();

        return view('admin.eap-online.onsite-consultation.place.index', ['places' => $places]);
    }

    public function create()
    {
        return view('admin.eap-online.onsite-consultation.place.create');
    }

    public function edit(OnsiteConsultationPlace $place)
    {
        return view('admin.eap-online.onsite-consultation.place.edit', ['place' => $place]);
    }

    public function store(Request $request, OnsiteConsultationService $onsite_consultation_service): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string',
            'address' => 'required|string',
        ]);

        $onsite_consultation_service->store_place($request->name, $request->address);

        return redirect()->route('admin.eap-online.onsite-consultation.place.index');
    }

    public function update(Request $request, OnsiteConsultationService $onsite_consultation_service)
    {
        $request->validate([
            'onsite_consultation_place_id' => 'required|numeric|exists:mysql_eap_online.onsite_consultation_places,id',
            'name' => 'required|string',
            'address' => 'required|string',
        ]);

        $onsite_consultation_service->update_place(
            $request->onsite_consultation_place_id,
            ['name' => $request->name, 'address' => $request->address]
        );

        return redirect()->route('admin.eap-online.onsite-consultation.place.index');
    }

    public function delete(OnsiteConsultationPlace $place)
    {
        $place->delete();

        return redirect()->route('admin.eap-online.onsite-consultation.place.index');
    }
}
