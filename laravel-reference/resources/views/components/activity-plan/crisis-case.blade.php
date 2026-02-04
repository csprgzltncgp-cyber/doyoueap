@props([
    'crisis_intervention',
    'activity_plan'
])

<div class="list-element col-12 workshop-admin-component">
    <span
        class="mr-0 data
        @if($crisis_intervention->status == 2) active @endif
        @if($crisis_intervention->status == 3) closed @endif"
    >
        #{{$crisis_intervention->activity_id}} -
        {{$crisis_intervention->date}} -
        {{$crisis_intervention->company ? Str::limit($crisis_intervention->company->name, 25) : ''}}
        {{$crisis_intervention->user ? '- ' . $crisis_intervention->user->name : ''}}
    </span>

    <a class="edit-crisis btn-radius" style="--btn-margin-left: var(--btn-margin-x)" href="{{route('admin.crisis.view', $crisis_intervention->id)}}">
        <img src="{{asset('assets/img/select.svg')}}" class="mr-1" style="height: 20px; width: 20px" alt="">
        {{__('crisis.select_button')}}
    </a>

    <button
        onclick="toggleActivityPlanMember(this, '{{$activity_plan->id}}', '{{$crisis_intervention->id}}', '{{addslashes(get_class($crisis_intervention))}}')"
        style="--btn-min-width: var(--btn-func-width);"
        class="toggle-activity-plan-member-status btn-radius @if($crisis_intervention->activity_plan_member) active @endif"
    >
        AP
    </button>
</div>
