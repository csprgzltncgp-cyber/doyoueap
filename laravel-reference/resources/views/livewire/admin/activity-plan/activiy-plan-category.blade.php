<div id="{{Str::slug($activity_plan_category->name)}}_container">
    <div class="mt-5 fix-activity">
        <div class="header">
            <h2>{{$activity_plan_category->name}}</h2>
        </div>

        <div class="mt-3">
            <a href="{{route('admin.activity-plan.category.case.create', [
                'activity_plan_category' => $activity_plan_category,
                'company' => $company,
                'country' => $country,
            ])}}">{{__('activity-plan.create-new-category-case')}}</a>
            <div class="mt-3">
                @foreach ($cases as $index => $case)
                    <x-activity-plan.activity-plan-category-case
                        :activity_plan="$activity_plan"
                        :case="$case"
                        :index="$index"
                    />
                @endforeach

                @if(!$cases->count())
                    <center>{{__('data.no_data')}}</center>
                @endif
            </div>
        </div>
    </div>
</div>
