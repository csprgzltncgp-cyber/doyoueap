<div class="list-element date-{{$date->id}} col-12 d-none group" onClick="toggle_rows('{{$date->id.'-'.$expert->id}}', this)" data-parent-id="{{$date->id}}">
    <div class="d-flex flex-column w-100">
        <div class="d-flex justify-content-between align-items-center w-100">
            <div class="list-elem" id="expert-header-{{$expert->id}}">
                <span>
                    {{$expert->name}}
                </span>
            </div>
            <button class="caret-left float-right p-0">
                <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
        </div>
    </div>
</div>

@foreach ($date->appointments->where('onsite_consultation_expert_id', $expert->id) as $appointment)
    @include('components.eap-online.onsite-consultation.appointment_line_component', ['appointment' => $appointment, 'parent_id' => $date->id.'-'.$expert->id])
@endforeach