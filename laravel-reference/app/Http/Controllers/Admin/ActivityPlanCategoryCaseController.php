<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityPlanCategory;
use App\Models\ActivityPlanCategoryCase;
use App\Models\Company;
use App\Models\Country;

class ActivityPlanCategoryCaseController extends Controller
{
    public function create(ActivityPlanCategory $activity_plan_category, Company $company, Country $country)
    {
        return view('admin.activity-plan-category-case.create', ['activity_plan_category' => $activity_plan_category, 'company' => $company, 'country' => $country]);
    }

    public function show(ActivityPlanCategory $activity_plan_category, ActivityPlanCategoryCase $activity_plan_category_case)
    {
        return view('admin.activity-plan-category-case.show', ['activity_plan_category' => $activity_plan_category, 'activity_plan_category_case' => $activity_plan_category_case]);
    }
}
