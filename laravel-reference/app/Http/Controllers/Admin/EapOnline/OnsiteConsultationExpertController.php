<?php

namespace App\Http\Controllers\Admin\EapOnline;

use App\Http\Controllers\Controller;
use App\Models\EapOnline\OnsiteConsultationExpert;
use App\Services\OnsiteConsultationService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OnsiteConsultationExpertController extends Controller
{
    public function index()
    {
        $experts = OnsiteConsultationExpert::query()->get();

        return view('admin.eap-online.onsite-consultation.expert.index', ['experts' => $experts]);
    }

    public function create()
    {
        return view('admin.eap-online.onsite-consultation.expert.create');
    }

    public function edit(OnsiteConsultationExpert $expert)
    {
        return view('admin.eap-online.onsite-consultation.expert.edit', ['expert' => $expert]);
    }

    public function update(Request $request, OnsiteConsultationService $onsite_consultation_service)
    {

        $request->validate([
            'onsite_consultation_expert_id' => 'required|numeric|exists:mysql_eap_online.onsite_consultation_experts,id',
            'name' => 'required|string',
            'description' => 'required|string|max:180',
            'new_image' => 'nullable|max:5120|mimes:png,jpg,jpeg',
        ]);

        // Upload image
        $new_image = request()->file('new_image');

        $path = ($new_image) ? $new_image->store('/eap-online/onsite-consultation-expert-files/'.$request->onsite_consultation_expert_id, 'local') : null;

        $onsite_consultation_service->update_onsite_consultation_expert(
            $request->onsite_consultation_expert_id,
            ['name' => $request->name, 'description' => $request->description],
            $path
        );

        return redirect()->route('admin.eap-online.onsite-consultation.expert.index');
    }

    public function store(OnsiteConsultationService $onsite_consultation_service)
    {
        try {
            request()->validate([
                'name' => 'required|string',
                'description' => 'required|string|max:180',
                'image' => 'required|max:5120|mimes:png,jpg,jpeg',
            ]);

            DB::beginTransaction();

            $expert = $onsite_consultation_service->create_onsite_consultation_expert(
                name: request()->name,
                description: request()->description,
                image: ''
            );

            // Upload image
            $file = request()->file('image');
            $path = $file->store('/eap-online/onsite-consultation-expert-files/'.$expert->id, 'local');

            $expert->update(['image' => $path]);

            DB::commit();

        } catch (Exception $e) {
            DB::rollBack();

            session()->flash('onsite-expert-creation-error', $e->getMessage());

            return redirect()->back();
        }

        return redirect()->route('admin.eap-online.onsite-consultation.index');
    }

    public function delete(OnsiteConsultationExpert $expert)
    {
        $expert->delete();

        return redirect()->route('admin.eap-online.onsite-consultation.expert.index');
    }
}
