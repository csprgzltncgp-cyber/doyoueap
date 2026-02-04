@props([
    'case',
])

<span>
    #{{$case->id}} -
    @foreach($case->activity_plan_category_case_values()->whereHas('activity_plan_category_field', fn($query) => $query->where('is_highlighted', true))->get() as $value)
        @switch($value->activity_plan_category_field->type)
            @case(App\Enums\ActivityPlanCategoryFieldTypeEnum::COUNTRY)
                <span>{{App\Models\Country::find($value->value)->name}}</span>
                @break
            @case(App\Enums\ActivityPlanCategoryFieldTypeEnum::CITY)
                <span>{{App\Models\City::find($value->value)->name}}</span>
                @break
            @case(App\Enums\ActivityPlanCategoryFieldTypeEnum::COMPANY)
                <span>{{App\Models\Company::find($value->value)->name}}</span>
                @break
            @case(App\Enums\ActivityPlanCategoryFieldTypeEnum::EXPERT)
                <span>{{App\Models\User::find($value->value)->name}}</span>
                @break
            @case(App\Enums\ActivityPlanCategoryFieldTypeEnum::CGP_EMPLOYEE)
                <span>{{App\Models\User::find($value->value)->name}}</span>
                @break
            @default
            {{Str::limit($value->value, 25)}}
            @break
        @endswitch

        @if(!$loop->last) - @endif
    @endforeach
</span>
