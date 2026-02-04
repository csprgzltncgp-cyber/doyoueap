@extends('layout.master')

@section('title')
    Admin Dashboard
@endsection

@section('extra_css')
    <link rel="stylesheet" href="/assets/css/list.css?v={{time()}}">
    <link rel="stylesheet" href="/assets/css/form.css?v={{time()}}">
    <link rel="stylesheet" href="/assets/css/cases/view.css?t=<?php echo e(time()); ?>">
    <link rel="stylesheet" href="/assets/css/eap-online/articles.css?v={{time()}}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.css"
          rel="stylesheet"/>
    <link rel="stylesheet" href="/assets/css/cases/datetime.css?t={{time()}}">
    <link
            href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.css"
            rel="stylesheet"/>
    <link rel="stylesheet" href="/assets/css/cases/datetime.css?t={{ time() }}">
    <link rel="stylesheet" href="{{asset('assets/js/fullcalendar-5.9.0/lib/main.css')}}">


    <style>
        .list-elem {
            background: rgb(222, 240, 241);
            color: black;
            text-transform: uppercase;
            margin-right: 10px;
            min-width: 200px;
        }

        .list-elem:hover {
            color: black;
        }

        .list-element button, .list-element a {
            margin-right: 10px;
            display: inline-block;
        }

        .list-element button.delete-button {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background: transparent;
            border: 0px solid black;
            color: #007bff;
            outline: none;
        }

        .list-element {
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .active-appointment-header {
            color: #00b8e6;
            background-color: rgba(222, 240, 241, 0);
        }

        .active-appointment-panel {
            background-color: rgba(222, 240, 241, 0.6);
        }

    </style>
@endsection

@section('extra_js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.js" type="text/javascript"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    <script src="{{asset('assets/js/fullcalendar-5.9.0/lib/main.js')}}"></script>
    <script type="text/javascript">
        const date_trans = '{{__('eap-online.video_therapy.date')}}';
        const expert_trans = '{{__('crisis.expert')}}';
        const required_trans = "{{__('eap-online.required')}}";
    </script>
    <script src="/assets/js/eap-online/validator.js?v={{time()}}" charset="utf-8"></script>
    <script src="/assets/js/eap-online/video_therpay_validator.js?v={{time()}}" charset="utf-8"></script>
    <script>
        $('.timepicker').datetimepicker({
            format: 'HH:mm',
        });

        function toggleAppointmentClasses(id) {
            if ($(`#edit-appointment-button-${id}`).text() == "{{__('common.edit')}}") {
                $(`#edit-appointment-button-${id}`).text("{{__('common.cancel')}}");
            } else {
                $(`#edit-appointment-button-${id}`).text("{{__('common.edit')}}");
            }

            $(`#appointment-header-${id}`).toggleClass('active-appointment-header');
            $(`#appointment-panel-${id}`).toggleClass('active-appointment-panel');
        }

        const events = JSON.parse(@json($formatted_appointments_for_calendar));

        const minTime = events.map(item => item.startTime).sort().shift()
        const maxTime = events.map(item => item.endTime).sort().pop()

        document.addEventListener('DOMContentLoaded', function () {
            let calendarEl = document.getElementById('calendar');
            let calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'timeGridWeek',
                nowIndicator: true,
                hiddenDays: [6, 0],
                firstDay: 1,
                slotMinTime: minTime,
                slotMaxTime: maxTime,
                headerToolbar: false,
                allDaySlot: false,
                locale: '{{app()->getLocale()}}',
                events: events,
                eventColor: '#59c6c6',
                contentHeight: 600,
                minHeight: 550,
                expandRows: true,
                //editable: true,
                slotLabelFormat: {
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: false
                },
                dayHeaderFormat: {weekday: 'long'}

            });
            calendar.render();
        });

        function toggleDay(dayId, element) {
            if ($(element).hasClass('active')) {
                $(element).removeClass('active');
                $('.list-element .caret-left').html(`<svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                                    </svg>`);
                $('.list-element').each(function () {
                    if ($(this).data('day') && $(this).data('day') == dayId) {
                        $(this).addClass('d-none');
                    }
                });
            } else {
                $(element).addClass('active')
                $(element).find('button.caret-left').html(`<svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                      <path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7" />
                                                    </svg>`);
                $('.list-element').each(function () {
                    if ($(this).data('day') && $(this).data('day') == dayId) {
                        $(this).removeClass('d-none');
                    } else if (!$(this).hasClass('group')) {
                        $(this).addClass('d-none');
                    }
                });
            }
        }

        function openModal(id) {
            $(`#${id}`).modal("show");
        }

        function saveTime() {
            $('#modal-time-picker').modal("hide");
            $('input[name="from_time"]').val($('input[name="modal_from_time"]').val());
            $('input[name="to_time"]').val($('input[name="modal_to_time"]').val());
            $("#time-picker-div").text($("input[name='from_time']").val() + ' - ' + $("input[name='to_time']").val());
        }

        function saveExpert() {
            $('#modal-expert-select').modal("hide");
            $('input[name="expert"]').val($('select[name="expert"]').val());
            $('#not-selected-expert').removeClass('d-flex').addClass('d-none');
            $('#selected-expert').addClass('d-flex').removeClass('d-none');
            $("#selected-expert p").text($("select[name='expert'] option:selected").text())
        }

        function deleteExpert() {
            $('input[name="expert"]').val('');
            $('#not-selected-expert').addClass('d-flex').removeClass('d-none');
            $('#selected-expert').removeClass('d-flex').addClass('d-none');
        }

        function deleteAppointment(id) {
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

        function editAppointment(id) {
            $(`#appointment-edit-${id}`).toggleClass('d-none');
            toggleAppointmentClasses(id);
        }


    </script>
@endsection

@section('content')
    <div class="row">
        <div class="row col-12">
            {{ Breadcrumbs::render('eap-online.video-therapy.schedule.edit', $language_id, $permission_id) }}
            <h1 class="col-12">{{ __('eap-online.video_therapy.video_chat_appointments') }}: {{\App\Models\EapOnline\EapLanguage::find($language_id)->name}}</h1>
            <form id="new-appointment"
                  action="{{route('admin.eap-online.video_therapy.actions.psychology.save_appointment')}}"
                  class="col-12" method="post">
                {{csrf_field()}}

                <input type="hidden" name="permission_id" value="{{$permission_id}}">
                <input type="hidden" name="language_id" value="{{$language_id}}">

                <div class="row d-flex flex-column col-12">
                    <h1 class="mb-3">{{__('eap-online.video_therapy.new_time')}}</h1>
                    <div class="col-4 mb-1 mt-3" style="border: 2px solid rgb(89,198,198) !important;">
                        <div class="d-flex flex-row align-items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="color: rgb(89, 198, 198); height: 25px; width: 25px;" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5m-9-6h.008v.008H12v-.008zM12 15h.008v.008H12V15zm0 2.25h.008v.008H12v-.008zM9.75 15h.008v.008H9.75V15zm0 2.25h.008v.008H9.75v-.008zM7.5 15h.008v.008H7.5V15zm0 2.25h.008v.008H7.5v-.008zm6.75-4.5h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V15zm0 2.25h.008v.008h-.008v-.008zm2.25-4.5h.008v.008H16.5v-.008zm0 2.25h.008v.008H16.5V15z" />
                            </svg>
                            <select style="margin:0px!important; padding: 10px 0px 10px 0px !important; border:0px!important;" name="day">
                                <option value="1">{{__('eap-online.video_therapy.monday')}}</option>
                                <option value="2">{{__('eap-online.video_therapy.tuesday')}}</option>
                                <option value="3">{{__('eap-online.video_therapy.wednesday')}}</option>
                                <option value="4">{{__('eap-online.video_therapy.thursday')}}</option>
                                <option value="5">{{__('eap-online.video_therapy.friday')}}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-4 mb-1 mt-3" style="border: 2px solid rgb(89,198,198) !important;"  onclick="openModal('modal-time-picker')">
                        <div class="d-flex flex-row align-items-center" style="padding-top:10px; padding-bottom:10px; color: rgb(89,198,198);">
                            <svg style="height: 20px; width: 20px;" xmlns="http://www.w3.org/2000/svg" class="mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div id="time-picker-div">
                                {{__('eap-online.video_therapy.set_date')}}
                            </div>
                        </div>
                    </div>
                    <div class="col-4 mb-1 mt-3" style="border: 2px solid rgb(89,198,198) !important;">
                        <div class="d-flex flex-row align-items-center">
                            <svg onclick="openModal('modal-expert-select')" style="color: rgb(89, 198, 198); height: 25px; width: 25px;"
                                    xmlns="http://www.w3.org/2000/svg" class="mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                            </svg>
                            <select name="expert" style="margin:0px!important; padding: 10px 0px 10px 0px !important; border:0px!important;" onchange="saveExpert()">
                                <option value="" hidden>
                                    {{__('eap-online.video_therapy.select_expert')}}
                                </option>
                                @foreach($experts as $expert)
                                    <option value="{{$expert->id}}">{{$expert->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-4 mt-3 p-0">
                        <button type="submit" class="col-4 btn-radius w-100">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="width:20px" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            <span>
                                {{__('common.add')}}
                            </span>
                        </button>
                    </div>
                </div>
                <input type="hidden" name="from_time">
                <input type="hidden" name="to_time">
                <input type="hidden" name="expert">
            </form>
            <div class=" col-12">
                <h1 class="mt-5">{{__('eap-online.video_therapy.edit_fixed_dates')}}</h1>
                <div class="col-12 row p-0 m-0">
                    @php $days = [
                        __('eap-online.video_therapy.monday'),
                        __('eap-online.video_therapy.tuesday'),
                        __('eap-online.video_therapy.wednesday'),
                        __('eap-online.video_therapy.thursday'),
                        __('eap-online.video_therapy.friday')
                    ]; @endphp
                    @foreach($days as $day)
                        <div class="list-element col-12 group" onClick="toggleDay({{$loop->index + 1}}, this)">
                            <div class="d-flex align-items-center">
                                <p class="mr-3">{{$day}}</p>
                            </div>

                            <button class="caret-left float-right p-0">
                                <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                        </div>
                        @foreach($appointments as $appointment)
                            @if($appointment->day == ($loop->parent->index + 1))
                                <div class="list-element col-12 d-none" id="appointment-panel-{{$appointment->id}}"
                                     data-day="{{$loop->parent->index + 1}}">
                                    <div class="d-flex flex-column w-100">
                                        <div class="d-flex justify-content-between align-items-center w-100">
                                            <div class="list-elem" id="appointment-header-{{$appointment->id}}">
                                                <span>{{date('H:i', strtotime($appointment->from))}} - {{date('H:i', strtotime($appointment->to))}}</span>
                                                -
                                                <span>{{$appointment->expert->name}}</span>
                                            </div>
                                            <div class="d-flex flex-row align-items-center">
                                                <div style="color:#007bff" class="mr-3">
                                                    <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                                                        style="height:20px; margin-bottom: 3px" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                    </svg>
                                                    <span onclick="editAppointment('{{$appointment->id}}')"
                                                        class="mr-1"
                                                        id="edit-appointment-button-{{$appointment->id}}">{{__('common.edit')}}
                                                    </span>
                                                </div>
                                                <form id="delete-appointment-{{$appointment->id}}" class="m-0"
                                                    action="{{route('admin.eap-online.video_therapy.actions.psychology.delete_appointment')}}"
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
                                                        <button onclick="deleteAppointment('delete-appointment-{{$appointment->id}}')"
                                                            class="p-0 m-0 bg-transparent" type="button" style="color:#007bff"
                                                            >{{__('common.delete')}}</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                        <form method="post"
                                              action="{{route('admin.eap-online.video_therapy.actions.psychology.edit_appointment', ['appointment_id' => $appointment->id])}}"
                                              id="appointment-edit-{{$appointment->id}}"
                                              class="d-none row flex-column col-12">
                                            {{csrf_field()}}
                                            <div class="ml-n1 d-flex align-items-center">
                                                <img class="mr-1" style="width: 25px;" src="{{asset('assets/img/eap-online/clock.svg')}}"
                                                     alt="clock">
                                                <p class="m-0">{{__('eap-online.video_therapy.edit_date')}}</p>
                                            </div>
                                            <select class="col-3 mt-3 bg-transparent" name="edit_day">
                                                <option @if($appointment->day == 1) selected
                                                        @endif value="1">{{__('eap-online.video_therapy.monday')}}</option>
                                                <option @if($appointment->day == 2) selected
                                                        @endif value="2">{{__('eap-online.video_therapy.tuesday')}}</option>
                                                <option @if($appointment->day == 3) selected
                                                        @endif value="3">{{__('eap-online.video_therapy.wednesday')}}</option>
                                                <option @if($appointment->day == 4) selected
                                                        @endif value="4">{{__('eap-online.video_therapy.thursday')}}</option>
                                                <option @if($appointment->day == 5) selected
                                                        @endif value="5">{{__('eap-online.video_therapy.friday')}}</option>
                                            </select>
                                            <div class="d-flex align-items-center">
                                                <input type="text" name="edit_from_time" class="col-3 timepicker"
                                                       placeholder="{{__('common.from')}}"
                                                       value="{{date('H:i', strtotime($appointment->from))}}">
                                                <span class="mb-3 mx-3">-</span>
                                                <input type="text" name="edit_to_time" class="col-3 timepicker"
                                                       placeholder="{{__('common.to')}}"
                                                       value="{{date('H:i', strtotime($appointment->to))}}">
                                            </div>
                                            <div id="not-selected-expert-{{$appointment->id}}" style="cursor: pointer;"
                                                 class="d-flex align-items-center mt-3">
                                                <img onclick="openModal('modal-expert-select-edit')"
                                                     class="mr-1"
                                                     style="width: 25px;"
                                                     src="{{asset('assets/img/eap-online/user.svg')}}"
                                                     alt="user">
                                                <p onclick="openModal('modal-expert-select-edit')"
                                                   class="m-0">{{__('eap-online.video_therapy.edit_expert')}}</p>
                                            </div>
                                            <select name="edit_expert" class="col-3 bg-transparent mb-5 mt-3">
                                                @foreach($experts as $expert)
                                                    <option @if($expert->id == $appointment->expert_id) selected
                                                            @endif value="{{$expert->id}}">{{$expert->name}}</option>
                                                @endforeach
                                            </select>
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
                            @endif
                        @endforeach
                    @endforeach
                </div>
            </div>
            <div class="col-12 mb-5">
                <h1 class="mt-5 mb-3">{{__('eap-online.video_therapy.calendar_view')}}</h1>
                <div id='calendar'></div>
            </div>
        </div>
    </div>
    <div class="row col-12 col-lg-2 back-button my-5">
        <a href="{{ route('admin.eap-online.video_therapy.actions') }}">{{__('common.back-to-list')}}</a>
    </div>
@endsection


@section('modal')
    <div class="modal" tabindex="-1" id="modal-time-picker" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{__('eap-online.video_therapy.set_date')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="text" name="modal_from_time" class="timepicker w-25"
                           placeholder="{{__('common.from')}}">
                    -
                    <input type="text" name="modal_to_time" class="timepicker w-25" placeholder="{{__('common.to')}}">
                    <button class="button btn-radius mr-3 float-right" style="--btn-margin-right: 0px; --btn-height:auto;" onclick="saveTime()">
                        <img src="{{asset('assets/img/select.svg')}}" class="mr-1" style="height: 20px; width: 20px" alt="">
                        <span>
                            {{__('common.select')}}
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
