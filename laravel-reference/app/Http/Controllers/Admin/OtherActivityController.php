<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\OtherActivity;
use App\Models\User;
use App\Services\OtherActivityService;
use Illuminate\Http\Request;

class OtherActivityController extends Controller
{
    public function index(OtherActivityService $other_activity_service)
    {
        $other_activities = $other_activity_service->get_other_activities();
        $categories = $other_activity_service->get_index_categories($other_activities);

        return view('admin.other-activity.index', array_merge($categories, ['other_activities' => $other_activities]));
    }

    public function set_paid(): void
    {
        OtherActivity::query()->where('id', request()->input('id'))->update([
            'paid' => true,
        ]);
    }

    public function filter()
    {
        $companies = Company::query()->orderBy('name')->get();
        $experts = User::query()
            ->orderBy('name')
            ->where('type', 'expert')
            ->where('active', 1)
            ->get();

        return view('admin.other-activity.filter', ['experts' => $experts, 'companies' => $companies]);
    }

    public function filterResult(Request $request)
    {
        $filters = array_filter($request->all());

        $other_activities = OtherActivity::query()->get();

        foreach ($filters as $key => $value) {
            if ($key == 'date') {
                if (! empty($value[0]) && ! empty($value[1])) {
                    $other_activities = $other_activities->whereBetween('date', [$value[0], $value[1]]);
                }
            } else {
                $other_activities = $other_activities->where($key, $value);
            }
        }

        return view('admin.other-activity.result', ['other_activities' => $other_activities]);
    }
}
