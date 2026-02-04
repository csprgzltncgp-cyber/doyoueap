<?php

namespace App\Http\Controllers\Expert;

use App\Enums\WorkshopCaseExpertStatus;
use App\Enums\WorkshopCaseStatus;
use App\Models\WorkshopCase;
use App\Models\WorkshopCaseEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class WorkshopExpertController extends BaseExpertController
{
    public function index()
    {
        $workshop_cases = WorkshopCase::query()
            ->where('expert_id', Auth::id())
            ->where('status', '!=', WorkshopCaseStatus::CLOSED)
            ->latest()
            ->get();

        return view('expert.workshops.list', ['workshop_cases' => $workshop_cases]);
    }

    public function list_closed()
    {
        $workshop_cases = WorkshopCase::query()
            ->where('expert_id', Auth::id())
            ->where('status', WorkshopCaseStatus::CLOSED)
            ->latest()
            ->get();

        return view('expert.workshops.closed_list', ['workshop_cases' => $workshop_cases]);
    }

    public function edit($id)
    {
        WorkshopCaseEvent::query()->where(['user_id' => Auth::id(), 'event' => 'workshop_case_accepted_by_admin'])->delete();
        $workshop_case = WorkshopCase::query()->where('id', $id)->first();
        $prefix = 'expert';

        return view('expert.workshops.view', ['workshop_case' => $workshop_case, 'prefix' => $prefix]);
    }

    public function update(Request $request, $id)
    {
        if ($request->has('number-of-participants')) {
            WorkshopCase::query()
                ->where('id', $id)
                ->update([
                    'number_of_participants' => $request->get('number-of-participants'),
                ]);

            return Redirect::back();
        }

        WorkshopCase::query()
            ->where('id', $id)
            ->update([
                'expert_price' => $request->expert_price,
                'expert_currency' => $request->expert_currency,
                'expert_status' => WorkshopCaseExpertStatus::EXPERT_PRICE_CHANGE,
            ]);

        $event = WorkshopCaseEvent::query()->where(['workshop_case_id' => $id])->first();
        if (! empty($event)) {
            WorkshopCaseEvent::query()->where(['workshop_case_id' => $id])->update([
                'event' => 'workshop_case_price_modified_by_expert',
            ]);
        } else {
            WorkshopCaseEvent::query()->create([
                'workshop_case_id' => $id,
                'user_id' => Auth::id(),
                'event' => 'workshop_case_price_modified_by_expert',
            ]);
        }

        return Redirect::back();
    }

    public function accept($id)
    {
        $final_price = WorkshopCase::query()->where('id', $id)->first();
        WorkshopCase::query()
            ->where('id', $id)
            ->update([
                'expert_price' => $final_price->expert_price,
                'expert_currency' => $final_price->expert_currency,
                'expert_status' => WorkshopCaseExpertStatus::ACCEPTED,
                'status' => WorkshopCaseStatus::PRICE_ACCEPTED,
            ]);

        WorkshopCaseEvent::query()->where(['workshop_case_id' => $id])->delete();

        return Redirect::back();
    }

    public function denie($id)
    {
        WorkshopCase::query()
            ->where('id', $id)
            ->update([
                'expert_id' => null,
                'expert_phone' => null,
                'expert_status' => WorkshopCaseExpertStatus::DENIED,
            ]);

        $event = WorkshopCaseEvent::query()->where(['workshop_case_id' => $id])->first();

        if (! empty($event)) {
            WorkshopCaseEvent::query()->where(['workshop_case_id' => $id])->update([
                'event' => 'workshop_case_denied_by_expert',
            ]);
        } else {
            WorkshopCaseEvent::query()->create([
                'workshop_case_id' => $id,
                'user_id' => Auth::id(),
                'event' => 'workshop_case_denied_by_expert',
            ]);
        }

        return redirect()->route('expert.workshops.list');
    }
}
