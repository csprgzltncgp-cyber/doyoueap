<div>
    @if(has_super_access_to_activity_plan())
        <a href="{{route(auth()->user()->type.'.activity-plan.edit', ['activity_plan' => $activity_plan])}}">{{__('activity-plan.create-edit-category')}}</a>
    @endif

    {{-- Navigation --}}
    <div class="d-flex flex-wrap gap-3 mt-5">
        <div class="menu-item" onclick="document.getElementById('map_container').scrollIntoView({behavior: 'smooth'});">
            {{__('activity-plan.activity-plan')}}
        </div>

        @foreach ($activity_plan_categories as $activity_plan_category)
            <div class="menu-item" onclick="document.getElementById('{{Str::slug($activity_plan_category->name)}}_container').scrollIntoView({behavior: 'smooth'});">
                {{$activity_plan_category->name}}
            </div>
        @endforeach

        <div class="menu-item" onclick="document.getElementById('workshop_container').scrollIntoView({behavior: 'smooth'});">
            {{__('workshop.workshop')}}
        </div>
        <div class="menu-item" onclick="document.getElementById('crisis_intervention_container').scrollIntoView({behavior: 'smooth'});">
            {{__('crisis.crisis')}}
        </div>
        <div class="menu-item" onclick="document.getElementById('other_activity_container').scrollIntoView({behavior: 'smooth'});">
            {{__('other-activity.other-activities')}}
        </div>
        <div class="menu-item" onclick="document.getElementById('workshop_feedback_container').scrollIntoView({behavior: 'smooth'});">
            {{__('common.workshop_feedback')}}
        </div>
        <div class="menu-item" onclick="document.getElementById('prizegame_container').scrollIntoView({behavior: 'smooth'});">
            {{__('prizegame.menu')}}
        </div>
    </div>
    {{-- Navigation --}}

    {{-- Content --}}
    @livewire('admin.activity-plan.map', ['activity_plan' => $activity_plan], key($activity_plan->id . '-map'))

    @foreach ($activity_plan_categories as $activity_plan_category)
        @livewire('admin.activity-plan.activiy-plan-category', [
            'activity_plan_category' => $activity_plan_category,
            'activity_plan' => $activity_plan
        ], key($activity_plan_category->id))
    @endforeach

    @livewire('admin.activity-plan.workshop', ['activity_plan' => $activity_plan], key($activity_plan->id . '-workshop'))

    @livewire('admin.activity-plan.crisis', ['activity_plan' => $activity_plan], key($activity_plan->id . '-crisis'))

    @livewire('admin.activity-plan.other-activity', ['activity_plan' => $activity_plan], key($activity_plan->id . '-other-activity'))

    @livewire('admin.activity-plan.workshop-feedback', ['activity_plan' => $activity_plan], key($activity_plan->id . '-workshop-feedback'))

    @livewire('admin.activity-plan.prize-game', ['activity_plan' => $activity_plan], key($activity_plan->id . '-prize-game'))
    {{-- Content --}}
</div>
