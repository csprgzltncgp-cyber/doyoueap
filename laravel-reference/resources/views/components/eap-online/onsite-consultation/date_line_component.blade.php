<div class="col-12" id="consultation_date_row_{{$date->id}}">
    <div class="col-12 row p-0 m-0">
        <div class="list-element col-12 group" onClick="toggle_rows({{$date->id}}, this)">
            <div class="d-flex align-items-center justify-content-between w-100">
                <p class="mr-3">
                    {{$date->date->format('Y-m-d')}}
                </p>
                <div class="mr-3">
                    <button type="button" class="float-right delete-button" onclick="delete_consultation_date({{$date->id}})">
                        <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                            style="height:20px; margin-bottom: 3px" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        {{__('common.delete')}}
                    </button>
                </div>
            </div>

            <button class="caret-left float-right p-0">
                <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
        </div>

        @if (in_array($consultation->type, [\App\Enums\OnsiteConsultationType::WITH_EXPERT, \App\Enums\OnsiteConsultationType::ONLINE_WITH_EXPERT]))
            @foreach($date->appointments->pluck('expert')->unique() as $expert)
                @include('components.eap-online.onsite-consultation.expert_line_component', ['expert' => $expert])
            @endforeach
        @else
            @foreach ($date->appointments as $appointment)
                @include('components.eap-online.onsite-consultation.appointment_line_component', ['appointment' => $appointment, 'parent_id' => $date->id])
            @endforeach
        @endif
    </div>
</div>