<script>
    
    function toggle_appointment_classes(id) {
        if ($(`#edit-appointment-button-${id}`).text() == "{{__('common.edit')}}") {
            $(`#edit-appointment-button-${id}`).text("{{__('common.cancel')}}");
        } else {
            $(`#edit-appointment-button-${id}`).text("{{__('common.edit')}}");
        }

        $(`#appointment-header-${id}`).toggleClass('active-appointment-header');
        $(`#appointment-panel-${id}`).toggleClass('active-appointment-panel');
    }

    function edit_appointment(id) {
        $(`#appointment-edit-${id}`).toggleClass('d-none');
        toggle_appointment_classes(id);
    }

    function delete_appointment(id) {
        Swal.fire({
            title: '{{__("common.are-you-sure-to-delete")}}',
            text: '{{__("common.operation-cannot-undone")}}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: '{{__("common.yes-delete-it")}}'
        }).then((result) => {
            if (result.value) {
                $(`#${id}`).submit();
            }
        });
    }

</script>
<div class="list-element col-12 d-none" id="appointment-panel-{{$appointment->id}}" data-parent-id="{{$parent_id}}">
    <div class="d-flex flex-column w-100">
        <div class="d-flex justify-content-between align-items-center w-100">
            <div class="list-elem" id="appointment-header-{{$appointment->id}}">
                <span>
                    {{date('H:i', strtotime($appointment->from))}} - {{date('H:i', strtotime($appointment->to))}}
                </span>
            </div>
            <div class="d-flex flex-row align-items-center">
                <div style="color:#007bff" class="mr-3">
                    <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                        style="height:20px; margin-bottom: 3px" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    <span onclick="edit_appointment('{{$appointment->id}}')"
                        class="mr-1"
                        id="edit-appointment-button-{{$appointment->id}}">{{__('common.edit')}}
                    </span>
                </div>
                <form id="delete-appointment-{{$appointment->id}}" class="m-0"
                    action="{{route('admin.eap-online.onsite-consultation.appointment.delete', $appointment)}}"
                    method="post">
                    {{csrf_field()}}
                    <input type="hidden" name="appointment_id"
                            value="{{$appointment->id}}">
                    <div class="d-flex flex-row" style="color:#007bff">
                        <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                            style="height:28px; margin-bottom: 3px" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        <button onclick="delete_appointment('delete-appointment-{{$appointment->id}}')"
                            class="p-0 m-0 bg-transparent" type="button" style="color:#007bff"
                            >{{__('common.delete')}}</button>
                    </div>
                </form>
            </div>
        </div>
        <form method="post"
            action="{{route('admin.eap-online.onsite-consultation.appointment.edit')}}"
            id="appointment-edit-{{$appointment->id}}"
            class="d-none row flex-column col-12">
            {{csrf_field()}}
            <input type="hidden" name="appointment_id" value="{{$appointment->id}}">
            <div class="ml-n1 d-flex align-items-center">
                <img class="mr-1" style="width: 25px;" src="{{asset('assets/img/eap-online/clock.svg')}}"
                    alt="clock">
                <p class="m-0">{{__('eap-online.video_therapy.edit_date')}}</p>
            </div>
            <div class="d-flex align-items-center mt-3">
                <input type="text" name="edit_from_time" class="col-3 timepicker"
                    placeholder="{{__('common.from')}}"
                    value="{{date('H:i', strtotime($appointment->from))}}">
                <span class="mb-3 mx-3">-</span>
                <input type="text" name="edit_to_time" class="col-3 timepicker"
                    placeholder="{{__('common.to')}}"
                    value="{{date('H:i', strtotime($appointment->to))}}">
            </div>
            <div class="col-3 p-0 m-0">
                <button type="submit" class="btn-radius w-100">
                    <img src="{{asset('assets/img/select.svg')}}" class="mr-1" style="height: 20px; width: 20px" alt="">
                    <span>
                        {{__('common.select')}}
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>