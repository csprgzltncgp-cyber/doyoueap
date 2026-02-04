<div class="mb-2">
    <div class="case-list-in mb-0 col-12 group">
        {{$activity_plan_category->name}}

        <button onclick="deleteActivityPlanCategory({{$activity_plan_category->id}})" class="caret-left float-right ml-3" style="color:rgb(219, 11, 32)">
            <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                    style="height: 20px; margin-bottom: 3px" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round"
                        stroke-width="2"
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
            {{__('common.delete')}}
        </button>

        <button wire:click="toggle_opened" class="float-right caret-left pt-1" style="color:#007bff">
            <svg xmlns="http://www.w3.org/2000/svg" style="{{$opened ? '' : 'transform: rotate(180deg);'}} width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
            </svg>

            {{__('common.edit')}}
        </button>
    </div>

    <form class="col-12 row {{$opened ? '' : 'd-none'}} mt-2 mb-4">
        @foreach ($activity_plan_category_fields as $activity_plan_category_field)
            <div class="row col-12">
                <div class="
                    col-3
                    @if($activity_plan_category_field->is_highlighted) category-field-element-row @else category-field-element @endif
                    @if(optional($activity_plan_category_fields->where('type', App\Enums\ActivityPlanCategoryFieldTypeEnum::EVENT_DATE)->first())->id === $activity_plan_category_field->id)
                    event-date-field
                    @endif
                ">
                    {{$activity_plan_category_field->name}} ({{$activity_plan_category_field->type->getTranslation()}}):
                </div>

                <svg
                    onclick="editActivityPlanCategoryField({{$activity_plan_category->id}}, {{$activity_plan_category_field->id}}, '{{$activity_plan_category_field->name}}', {{$activity_plan_category_field->is_highlighted}})"
                    xmlns="http://www.w3.org/2000/svg"  style="height: 23px; margin-top: 20px; cursor: pointer;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>

                <svg onclick="deleteActivityPlanCategoryField({{$activity_plan_category->id}}, {{$activity_plan_category_field->id}})" xmlns="http://www.w3.org/2000/svg"
                        style="height: 23px; margin-top: 20px; cursor: pointer;" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" class="ml-1">
                    <path stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </div>
        @endforeach

        <button onclick="addActivityPlanCategoryField({{$activity_plan_category->id}})" type="button" class="mt-4 button btn-radius">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="mr-1" style="width: 20px; height: 20px;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>

            {{__('activity-plan.create-new-field')}}
        </button>
    </form>
</div>
