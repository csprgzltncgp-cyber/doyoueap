@push('livewire_js')
    <script>
         function filterWorkshops() {
            Swal.fire({
                title: '{{__('common.workshops')}}',
                html: '<input type="text" placeholder="{{__('workshop.workshop_serial_number')}}" id="filter_workshop_activity_id" class="swal2-input"/>' +
                 '<select id="filter_workshop_expert_id" class="swal2-input">' +
                 '<option value="">{{__('workshop.expert')}}</option>' +
                 '@foreach($experts as $expert)' +
                 '<option value="{{$expert->id}}">{{$expert->name}}</option>' +
                 '@endforeach' +
                 '</select>' +
                 '<select id="filter_workhsop_status" class="swal2-input">' +
                 '<option value="">{{__('workshop.status')}}</option>' +
                 '<option value="1">{{__('workshop.under_agreement')}}</option>' +
                 '<option value="2">{{__('workshop.active')}}</option>' +
                 '<option value="3">{{__('workshop.closed')}}</option>' +
                 '</select>',
                showLoaderOnConfirm: true,
                confirmButtonText: '{{__('common.filter')}}',
                stopKeydownPropagation: false,
                preConfirm: () => {
                    const activity_id = document.querySelector('#filter_workshop_activity_id').value || null;
                    const expert_id = document.querySelector('#filter_workshop_expert_id').value || null;
                    const status = document.querySelector('#filter_workhsop_status').value || null;

                    return {
                        activity_id: activity_id,
                        expert_id: expert_id,
                        status: status,
                    };
                }
            }).then((result) => {
                if (result.value) {
                    Livewire.emit('workshop_filter', result.value);
                }
            });
        }
    </script>
@endpush

<div id="workshop_container">
    <div class="mt-5 fix-activity">
        <div class="header">
            <h2>{{__('common.workshops')}}</h2>
            <button class="{{ !$is_filtered ? '' : 'd-none'}} filter-button" onclick="filterWorkshops()">
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
            @if($categories['saved_workshop_cases']->count() > 0)
                <div class="case-list-in col-12 group" onclick="yearOpen('saved_workshop_cases')">
                    {{__('workshop.saved_workshops')}}
                    <button class="caret-left float-right">
                        <svg id="ysaved_workshop_cases" xmlns="http://www.w3.org/2000/svg"
                            style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                </div>
                <div class="lis-element-div" id="saved_workshop_cases" style="display: none">
                    @foreach($categories['saved_workshop_cases'] as $workshop)
                    <x-activity-plan.workshop-case
                        :activity_plan="$activity_plan"
                        :workshop="$workshop"
                    />
                    @endforeach
                </div>
            @endif

            @if(!$is_filtered)
                @foreach($categories['filtered_years'] as $year)
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
                        @foreach($categories['filtered_months'] as $month)
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
                                    @foreach($workshops->whereNotNull('date')->sortByDesc('date') as $workshop)
                                        @if((string)\Carbon\Carbon::parse($workshop->date)->month == \Illuminate\Support\Str::of($month)->after('-')
                                        && (string)\Carbon\Carbon::parse($workshop->date)->year == \Illuminate\Support\Str::of($year)->after('-'))
                                            <x-activity-plan.workshop-case
                                                    :activity_plan="$activity_plan"
                                                    :workshop="$workshop"
                                            />
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endforeach

                @if(empty($categories['filtered_years']) || empty($categories['filtered_months']))
                    <center>{{__('data.no_data')}}</center>
                @endif
            @else
                @foreach($workshops->whereNotNull('date')->sortByDesc('date') as $workshop)
                        <x-activity-plan.workshop-case
                            :workshop="$workshop"
                            :activity_plan="$activity_plan"
                        />
                @endforeach

                @if(!$workshops->count())
                    <center>{{__('data.no_data')}}</center>
                @endif
            @endif
        </div>
    </div>
</div>
