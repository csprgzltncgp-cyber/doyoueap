<?php

namespace App\Http\Controllers\Expert;

use App\Enums\CrisisCaseStatus;
use App\Models\CrisisCase;
use App\Models\CrisisCaseEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class CrisisExpertController extends BaseExpertController
{
    public function index()
    {
        $crisis_cases = CrisisCase::query()
            ->where('expert_id', Auth::id())
            ->where('status', '!=', CrisisCaseStatus::CLOSED)
            ->latest()
            ->get();

        return view('expert.crisis.list', ['crisis_cases' => $crisis_cases]);
    }

    public function list_closed()
    {
        $crisis_cases = CrisisCase::query()
            ->where('expert_id', Auth::id())
            ->where('status', CrisisCaseStatus::CLOSED)
            ->latest()
            ->get();

        return view('expert.crisis.closed_list', ['crisis_cases' => $crisis_cases]);
    }

    public function edit($id)
    {
        CrisisCaseEvent::query()->where(['user_id' => Auth::id(), 'event' => 'crisis_case_accepted_by_admin'])->delete();
        $crisis_case = CrisisCase::query()->where('id', $id)->first();

        return view('expert.crisis.view', ['crisis_case' => $crisis_case]);
    }

    public function update(Request $request, $id)
    {
        if ($request->has('number-of-participants')) {
            CrisisCase::query()
                ->where('id', $id)
                ->update([
                    'number_of_participants' => $request->get('number-of-participants'),
                ]);

            return Redirect::back();
        }

        CrisisCase::query()
            ->where('id', $id)
            ->update([
                'expert_price' => $request->expert_price,
                'expert_currency' => $request->expert_currency,
                'expert_status' => 2,
                'updated_at' => now(),
            ]);

        $event = CrisisCaseEvent::query()->where(['crisis_case_id' => $id])->first();
        if (! empty($event)) {
            CrisisCaseEvent::query()->where(['crisis_case_id' => $id])->update([
                'event' => 'crisis_case_price_modified_by_expert',
            ]);
        } else {
            CrisisCaseEvent::query()->create([
                'crisis_case_id' => $id,
                'user_id' => Auth::id(),
                'event' => 'crisis_case_price_modified_by_expert',
            ]);
        }

        return Redirect::back();
    }

    public function accept($id)
    {
        $final_price = CrisisCase::query()->where('id', $id)->first();

        CrisisCase::query()
            ->where('id', $id)
            ->update([
                'expert_price' => $final_price->expert_price,
                'expert_currency' => $final_price->expert_currency,
                'expert_status' => 1,
                'status' => CrisisCaseStatus::PRICE_ACCEPTED,
            ]);

        CrisisCaseEvent::query()->where(['crisis_case_id' => $id])->delete();

        return Redirect::back();
    }

    public function denie($id)
    {
        CrisisCase::query()
            ->where('id', $id)
            ->update([
                'expert_id' => null,
                'expert_phone' => null,
                'expert_status' => 4,
            ]);

        $event = CrisisCaseEvent::query()->where(['crisis_case_id' => $id])->first();

        if (! empty($event)) {
            CrisisCaseEvent::query()->where(['crisis_case_id' => $id])->update([
                'event' => 'crisis_case_denied_by_expert',
            ]);
        } else {
            CrisisCaseEvent::query()->create([
                'crisis_case_id' => $id,
                'user_id' => Auth::id(),
                'event' => 'crisis_case_denied_by_expert',
            ]);
        }

        return redirect()->route('expert.crisis.list');
    }
}
