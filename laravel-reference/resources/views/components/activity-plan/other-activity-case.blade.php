@props([
    'other_activity',
    'activity_plan'
])

<div class="list-element col-12 workshop-admin-component">
    <span class="data mr-0
    @if($other_activity->status == \App\Enums\OtherActivityStatus::STATUS_IN_PROGRESS) active @endif
    @if($other_activity->status == \App\Enums\OtherActivityStatus::STATUS_CLOSED) closed @endif
    ">
        {{$other_activity->activity_id}} -
        {{$other_activity->date}} -
        {{optional($other_activity->company)->name}} -
        {{optional($other_activity->user)->name}}
    </span>

    <a href="{{ route('admin.other-activities.show', ['id' => $other_activity->id]) }}" class="edit-workshop btn-radius"
        style="--btn-margin-left: 15px;">
        <img class="mr-1" style="width:20px;" src="{{asset('assets/img/select.svg')}}">
        {{__('workshop.select_button')}}
    </a>

    <button
        onclick="toggleActivityPlanMember(this, '{{$activity_plan->id}}', '{{$other_activity->id}}', '{{addslashes(get_class($other_activity))}}')"
        style="--btn-min-width: var(--btn-func-width);"
        class="toggle-activity-plan-member-status btn-radius @if($other_activity->activity_plan_member) active @endif"
    >
        AP
    </button>
</div>
