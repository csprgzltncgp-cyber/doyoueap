<div id="map_container">
    <div class="mt-5 fix-activity">
        <div class="header">
            <h2>{{__('activity-plan.activity-plan')}}</h2>
        </div>

        <div class="company-selector mt-4 mb-1">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="heigth:20px; width:20px;" class="mr-1 mb-1">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Z" />
            </svg>

            <span class="mr-2">{{__('activity-plan.company')}}:</span>

            <select wire:model="current_company_id" id="company-selector">
                @foreach($companies as $company)
                    <option value="{{$company->id}}">{{$company->name}}</option>
                @endforeach
            </select>
        </div>

        <div class="country-selector mb-1">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="heigth:20px; width:20px;" class="mr-1 mb-1">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12.75 3.03v.568c0 .334.148.65.405.864l1.068.89c.442.369.535 1.01.216 1.49l-.51.766a2.25 2.25 0 0 1-1.161.886l-.143.048a1.107 1.107 0 0 0-.57 1.664c.369.555.169 1.307-.427 1.605L9 13.125l.423 1.059a.956.956 0 0 1-1.652.928l-.679-.906a1.125 1.125 0 0 0-1.906.172L4.5 15.75l-.612.153M12.75 3.031a9 9 0 0 0-8.862 12.872M12.75 3.031a9 9 0 0 1 6.69 14.036m0 0-.177-.529A2.25 2.25 0 0 0 17.128 15H16.5l-.324-.324a1.453 1.453 0 0 0-2.328.377l-.036.073a1.586 1.586 0 0 1-.982.816l-.99.282c-.55.157-.894.702-.8 1.267l.073.438c.08.474.49.821.97.821.846 0 1.598.542 1.865 1.345l.215.643m5.276-3.67a9.012 9.012 0 0 1-5.276 3.67m0 0a9 9 0 0 1-10.275-4.835M15.75 9c0 .896-.393 1.7-1.016 2.25" />
              </svg>


            <span class="mr-2">{{__('activity-plan.country')}}:</span>

            <select wire:model="current_country_id" id="country-selector">
                @foreach($countries as $country)
                    <option value="{{$country->id}}">{{$country->name}}</option>
                @endforeach
            </select>
        </div>

        <div class="row col-12">
            <div class="mt-3 activity-plan-member-container">
                @foreach($activity_plan_members as $activity_plan_member)
                    @php
                        $case = $activity_plan_member->activity_plan_memberable;
                    @endphp
                    @switch(get_class($case))
                        @case(App\Models\WorkshopCase::class)
                            <div class="
                                activity-plan-member-box
                                @if($case->status == 2) active @endif
                                @if($case->status == 3) closed @endif
                            ">
                                <h2>Workshop</h2>
                                <span>
                                    #{{$case->activity_id}} -
                                    {{$case->date}} -
                                    {{$case->company ? Str::limit($case->company->name, 25) : ''}}
                                    {{$case->user ? '- ' . $case->user->name : ''}}
                                </span>
                                <a href="{{route(auth()->user()->type . '.workshops.view',['id' => $case->id])}}" class="edit-workshop btn-radius">
                                    {{__('common.select')}}
                                </a>
                            </div>
                            @break
                        @case(App\Models\CrisisCase::class)
                            <div class="
                                activity-plan-member-box
                                @if($case->status == 2) active @endif
                                @if($case->status == 3) closed @endif
                            ">
                                <h2>Crisis Intervention</h2>
                                <span>
                                    #{{$case->activity_id}} -
                                    {{$case->date}} -
                                    {{$case->company ? Str::limit($case->company->name, 25) : ''}}
                                    {{$case->user ? '- ' . $case->user->name : ''}}
                                </span>
                                <a href="{{route('admin.crisis.view', $case->id)}}" class="edit-workshop btn-radius">
                                    {{__('common.select')}}
                                </a>
                            </div>
                            @break
                        @case(App\Models\OtherActivity::class)
                            <div class="
                                activity-plan-member-box
                                @if($case->status == \App\Enums\OtherActivityStatus::STATUS_IN_PROGRESS) active @endif
                                @if($case->status == \App\Enums\OtherActivityStatus::STATUS_CLOSED) closed @endif
                            ">
                                <h2>Other Activity</h2>
                                <span>
                                    {{$case->activity_id}} -
                                    {{$case->date}} -
                                    {{optional($case->company)->name}} -
                                    {{optional($case->user)->name}}
                                </span>
                                <a href="{{ route('admin.other-activities.show', ['id' => $case->id]) }}"
                                    class="edit-workshop btn-radius">
                                    {{__('common.select')}}
                                </a>
                            </div>
                            @break
                        @case(App\Models\ActivityPlanCategoryCase::class)
                            <div class="
                                activity-plan-member-box
                                @if($case->status == \App\Enums\ActivityPlanCategoryCaseStatusEnum::CLOSED) closed @endif
                            ">
                                <h2>{{$case->activity_plan_category->name}}</h2>

                                <x-activity-plan-category-case.highlighted-fields :case="$case"/>

                                <a href="{{route('admin.activity-plan.category.case.show', [
                                    'activity_plan_category' => $case->activity_plan_category,
                                    'activity_plan_category_case' => $case,
                                ])}}" class="edit-workshop btn-radius">
                                    {{__('common.select')}}
                                </a>
                            </div>
                            @break
                        @default
                            @break
                    @endswitch
                @endforeach
            </div>
        </div>
    </div>
</div>
