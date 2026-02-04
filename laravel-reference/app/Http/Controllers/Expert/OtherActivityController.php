<?php

namespace App\Http\Controllers\Expert;

use App\Enums\OtherActivityStatus;
use App\Models\OtherActivity;
use Illuminate\Support\Facades\Auth;

class OtherActivityController extends BaseExpertController
{
    public function index()
    {
        $other_activities = OtherActivity::query()
            ->where('user_id', Auth::id())
            ->where('status', '!=', OtherActivityStatus::STATUS_CLOSED)
            ->latest()
            ->get();

        return view('expert.other-activity.index', ['other_activities' => $other_activities]);
    }

    public function index_closed()
    {
        $other_activities = OtherActivity::query()
            ->where('user_id', Auth::id())
            ->where('status', OtherActivityStatus::STATUS_CLOSED)
            ->latest()
            ->get();

        return view('expert.other-activity.index_closed', ['other_activities' => $other_activities]);
    }
}
