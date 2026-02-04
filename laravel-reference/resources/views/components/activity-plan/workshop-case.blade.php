@props([
    'workshop',
    'activity_plan'
])
<div class="list-element col-12 workshop-admin-component">
    <span
        class="mr-0 data
        @if($workshop->status == 2) active @endif
        @if($workshop->status == 3) closed @endif"
    >
        #{{$workshop->activity_id}} -
        {{$workshop->date}} -
        {{$workshop->company ? Str::limit($workshop->company->name, 25) : ''}}
        {{$workshop->user ? '- ' . $workshop->user->name : ''}}
    </span>

    <a class="edit-workshop btn-radius" style="--btn-margin-left:var(--btn-margin-x)" href="{{route(auth()->user()->type . '.workshops.view',['id' => $workshop->id])}}">
        <img src="{{asset('assets/img/select.svg')}}" class="mr-1" style="height: 20px; width: 20px" alt="">
        {{__('workshop.select_button')}}
    </a>

    <button
        onclick="toggleActivityPlanMember(this, '{{$activity_plan->id}}', '{{$workshop->id}}', '{{addslashes(get_class($workshop))}}')"
        style="--btn-min-width: var(--btn-func-width);"
        class="toggle-activity-plan-member-status btn-radius @if($workshop->activity_plan_member) active @endif"
    >
        AP
    </button>
</div>
