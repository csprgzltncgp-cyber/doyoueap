<div class="row">
    <div class="col-12">
        <h1>{{__('common.case-view')}}</h1>
    </div>

    <div class="col-12 case-title">
        <p>
            {{$activity_plan_category_case->created_at->format('Y-m-d')}} -
            {{array_key_exists(0, $activity_plan_category_case->activity_plan_category_case_values->toArray()) ? Str::limit($activity_plan_category_case->activity_plan_category_case_values[0]->value, 25) : ''}} -
            {{array_key_exists(1, $activity_plan_category_case->activity_plan_category_case_values->toArray()) ? Str::limit($activity_plan_category_case->activity_plan_category_case_values[1]->value, 25) : ''}} -
            {{array_key_exists(2, $activity_plan_category_case->activity_plan_category_case_values->toArray()) ? Str::limit($activity_plan_category_case->activity_plan_category_case_values[2]->value, 25) : ''}} -
        </p>
    </div>

    <div class="col-12 case-details">
        <ul>
            <li>
                {{__('common.status')}}: <span id="case-status">{{$activity_plan_category_case->status}}</span></button>
            </li>
            <li>{{__('common.identifier')}}: #{{$activity_plan_category_case->id}}</li>

            @foreach($activity_plan_category_case->activity_plan_category_case_values as $value)

            <li>
                @switch($value->activity_plan_category_field->type)
                    @case(App\Enums\ActivityPlanCategoryFieldTypeEnum::COUNTRY)
                        <span>{{optional($value->activity_plan_category_field)->name}}: {{App\Models\Country::find($value->value)->name}}</span>
                        @break
                    @case(App\Enums\ActivityPlanCategoryFieldTypeEnum::CITY)
                        <span>{{optional($value->activity_plan_category_field)->name}}: {{App\Models\City::find($value->value)->name}}</span>
                        @break
                    @case(App\Enums\ActivityPlanCategoryFieldTypeEnum::COMPANY)
                        <span>{{optional($value->activity_plan_category_field)->name}}: {{App\Models\Company::find($value->value)->name}}</span>
                        @break
                    @case(App\Enums\ActivityPlanCategoryFieldTypeEnum::EXPERT)
                        <span>{{optional($value->activity_plan_category_field)->name}}: {{App\Models\User::find($value->value)->name}}</span>
                        @break
                    @case(App\Enums\ActivityPlanCategoryFieldTypeEnum::CGP_EMPLOYEE)
                        <span>{{optional($value->activity_plan_category_field)->name}}: {{App\Models\User::find($value->value)->name}}</span>
                        @break
                    @default
                        <span>{{optional($value->activity_plan_category_field)->name}}: <span>{{$value->value}}</span>
                @endswitch
                </li>
            @endforeach

            @if(!empty($activity_plan_category_case->closed_at))
            <li>
                {{__('common.closed_at')}}: <span id="case-status">{{$activity_plan_category_case->closed_at->format('Y-m-d')}}</span>
            </li>
        @endif
        </ul>
    </div>

    <div class="col-12 button-holder mt-3 row d-flex flex-row-reverse">
        @if($activity_plan_category_case->status == App\Enums\ActivityPlanCategoryCaseStatusEnum::OPENED)
            <a wire:click="close" class="button position-relative btn-radius d-flex">
                <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                    style="height:20px; margin-bottom: 3px" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
                {{__('workshop.close_workshop')}}
            </a>
        @endif

        <button onclick="deleteActivityPlanCategoryCase()" style="background-color: #7c2469;" class="button btn-radius position-relative">
            <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                style="height:20px; margin-bottom: 3px" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
            {{__('common.delete')}}
        </button>
    </div>

    <div class="col-12 back-button mb-5">
        <a href="{{route(auth()->user()->type.'.activity-plan.index')}}">{{__('common.back-to-list')}}</a>
    </div>
</div>
