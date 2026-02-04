@props([
    'case',
    'activity_plan'
])

<div class="list-element col-12 workshop-admin-component">
    <span class="mr-0 data
        @if($case->status == \App\Enums\ActivityPlanCategoryCaseStatusEnum::CLOSED) closed @endif
    ">
        <x-activity-plan-category-case.highlighted-fields :case="$case"/>
    </span>

    <a class="edit-workshop btn-radius" style="--btn-margin-left:var(--btn-margin-x)" href="{{route('admin.activity-plan.category.case.show', [
        'activity_plan_category' => $case->activity_plan_category,
        'activity_plan_category_case' => $case,
    ])}}">
        <img src="{{asset('assets/img/select.svg')}}" class="mr-1" style="height: 20px; width: 20px" alt="">
        {{__('workshop.select_button')}}
    </a>

    <button
        onclick="toggleActivityPlanMember(this, '{{$activity_plan->id}}', '{{$case->id}}', '{{addslashes(get_class($case))}}')"
        style="--btn-min-width: var(--btn-func-width);"
        class="toggle-activity-plan-member-status btn-radius @if($case->activity_plan_members()->where('activity_plan_id', $activity_plan->id)->exists()) active @endif"
    >
        AP
    </button>
</div>
