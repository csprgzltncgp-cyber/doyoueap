@push('livewire_js')
    <script>
        function filterOtherActivities() {
            Swal.fire({
                title: '{{__('other-activity.all')}}',
                html: '<input type="text" placeholder="{{__('other-activity.activity_id')}}" id="filter_other_activity_activity_id" class="swal2-input"/>' +
                 '<select id="filter_other_activity_expert_id" class="swal2-input">' +
                 '<option value="">{{__('workshop.expert')}}</option>' +
                 '@foreach($experts as $expert)' +
                 '<option value="{{$expert->id}}">{{$expert->name}}</option>' +
                 '@endforeach' +
                 '</select>' +
                 '<select id="filter_other_activity_status" class="swal2-input">' +
                 '<option value="">{{__('workshop.status')}}</option>' +
                 '<option value="1">{{__('workshop.under_agreement')}}</option>' +
                 '<option value="2">{{__('workshop.active')}}</option>' +
                 '<option value="3">{{__('workshop.closed')}}</option>' +
                 '</select>',
                showLoaderOnConfirm: true,
                confirmButtonText: '{{__('common.filter')}}',
                stopKeydownPropagation: false,
                preConfirm: () => {
                    const activity_id = document.querySelector('#filter_other_activity_activity_id').value || null;
                    const expert_id = document.querySelector('#filter_other_activity_expert_id').value || null;
                    const status = document.querySelector('#filter_other_activity_status').value || null;

                    return {
                        activity_id: activity_id,
                        user_id: expert_id,
                        status: status,
                    };
                }
            }).then((result) => {
                if (result.value) {
                    Livewire.emit('other_activity_filter', result.value);
                }
            });
        }
    </script>
@endpush

<div id="other_activity_container">
    <div class="mt-5 fix-activity">
        <div class="header">
            <h2>{{__('other-activity.all')}}</h2>
            <button class="{{ !$is_filtered ? '' : 'd-none'}} filter-button" onclick="filterOtherActivities()">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mr-2" style="width:20px; height:20px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                </svg>

                {{__('common.filter')}}
            </button>

            <button class="{{ $is_filtered ? '' : 'd-none'}} filter-button" wire:click="clear_filter"  style="background-color: #7c2469;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"  class="mr-2" style="width:20px; height:20px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>

                {{__('common.delete-filter')}}
            </button>
        </div>

        <div class="mt-3">
            @if(!$saved_activities->isEmpty())
                <div class="case-list-in col-12 group" onclick="yearOpen('saved_other_activity')">
                    {{__('other-activity.saved_activities')}}
                    <button class="caret-left float-right">
                        <svg id="ysaved_other_activity" xmlns="http://www.w3.org/2000/svg"
                             style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                             stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                </div>
                <div class="lis-element-div" id="saved_other_activity" style="display: none">
                    @foreach($saved_activities as $other_activity)
                        <x-other-activity.other_activity_case_component
                                :activity_plan="$activity_plan"
                                :other_activity="$other_activity"
                        />
                    @endforeach
                </div>
            @endif

            @if(!$is_filtered)
                @foreach($years as $year)
                    @php $year_id = uniqid() @endphp
                    <div class="case-list-in col-12 group" onclick="yearOpen('{{$year_id}}')">
                        {{$year}}
                        <button class="caret-left float-right">
                            <svg id="y{{$year_id}}" xmlns="http://www.w3.org/2000/svg"
                                style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                    </div>
                    <div class="lis-element-div" id="{{$year_id}}">
                        @foreach($months as $month)
                            @php $month_id = uniqid() @endphp
                            @if((string)\Illuminate\Support\Str::of($month)->before('-') == (string)$year)
                                <div class="case-list-in col-12 group" onclick="monthOpen('{{$month_id}}')">
                                    {{$month}}
                                    <button class="caret-left float-right">
                                        <svg id="m{{$month_id}}" xmlns="http://www.w3.org/2000/svg"
                                            style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </button>
                                </div>
                                <div class="lis-element-div-c" id="{{$month_id}}">
                                    @foreach($other_activities->whereNotNull('date')->sortByDesc('date') as $other_activity)
                                        @if((string)\Carbon\Carbon::parse($other_activity->date)->month == \Illuminate\Support\Str::of($month)->after('-')
                                        && (string)\Carbon\Carbon::parse($other_activity->date)->year == \Illuminate\Support\Str::of($year)->after('-'))
                                            <x-activity-plan.other-activity-case
                                                    :activity_plan="$activity_plan"
                                                    :other_activity="$other_activity"
                                            />
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endforeach

                @if($years->isEmpty() || $months->isEmpty())
                    <center>{{__('data.no_data')}}</center>
                @endif
            @else
                @foreach($other_activities->whereNotNull('date')->sortByDesc('date') as $other_activity)
                    <x-activity-plan.other-activity-case
                            :activity_plan="$activity_plan"
                            :other_activity="$other_activity"
                    />
                @endforeach

                @if($other_activities->isEmpty())
                    <center>{{__('data.no_data')}}</center>
                @endif
            @endif
        </div>
    </div>
</div>
