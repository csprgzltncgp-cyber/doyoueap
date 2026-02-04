@extends('layout.master')

@section('title')
    Admin Dashboard
@endsection

@section('extra_css')
    <link rel="stylesheet" href="/assets/css/list.css?v={{time()}}">
    <link rel="stylesheet" href="/assets/css/form.css?v={{time()}}">
    <link rel="stylesheet" href="/assets/css/cases/view.css?t=<?php echo e(time()); ?>">
    <link rel="stylesheet" href="/assets/css/eap-online/articles.css?v={{time()}}">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.min.css"
          integrity="sha512-f0tzWhCwVFS3WeYaofoLWkTP62ObhewQ1EZn65oSYDZUg1+CyywGKkWzm8BxaJj5HGKI72PnMH9jYyIFz+GH7g=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <link rel="stylesheet" href="{{asset('assets/css/datetimepicker.css')}}">
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

        .active-day-off-header {
            color: #00b8e6;
            background-color: rgba(222, 240, 241, 0);
        }

        .active-day-off-panel {
            background-color: rgba(222, 240, 241, 0.6);
        }

        .fc-button-primary {
            background-color: rgb(89,198,198)!important;
            border: 0px!important;
        }

        .fc-today-button {
            border-radius: 12px !important;
        }

        .fc-prev-button {
            border-top-left-radius: 12px !important;
            border-bottom-left-radius: 12px !important;
        }

        .fc-next-button {
            border-top-right-radius: 12px !important;
            border-bottom-right-radius: 12px !important;
        }

    </style>
@endsection

@section('extra_js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.js" type="text/javascript"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.full.min.js"
            integrity="sha512-AIOTidJAcHBH2G/oZv9viEGXRqDNmfdPVPYOYKGy3fti0xIplnlgMHUGfuNRzC6FkzIo0iIxgFnr9RikFxK+sw=="
            crossorigin="anonymous" referrerpolicy="no-referrer">
    </script>
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
            format: 'Y-m-d H:i'
        });

        function toggleAppointmentClasses(id) {
            if ($(`#edit-day-off-button-${id}`).text() == "{{__('common.edit')}}") {
                $(`#edit-day-off-button-${id}`).text("{{__('common.cancel')}}");
            } else {
                $(`#edit-day-off-button-${id}`).text("{{__('common.edit')}}");
            }

            $(`#day-off-header-${id}`).toggleClass('active-day-off-header');
            $(`#day-off-panel-${id}`).toggleClass('active-day-off-panel');
        }

        const events = JSON.parse(@json($formatted_days_off_for_calendar));

        const minTime = events.map(item => item.startTime).sort().shift()
        const maxTime = events.map(item => item.endTime).sort().pop()

        document.addEventListener('DOMContentLoaded', function () {
            let calendarEl = document.getElementById('calendar');
            let calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                nowIndicator: true,
                hiddenDays: [6, 0],
                firstDay: 1,
                slotMinTime: minTime,
                slotMaxTime: maxTime,
                allDaySlot: false,
                locale: '{{app()->getLocale()}}',
                events: events,
                eventColor: '#59c6c6',
                contentHeight: 600,
                expandRows: true,
                //editable: true,
                displayEventTime: true,
                displayEventEnd: true,
                eventTimeFormat: {
                    hour: 'numeric',
                    minute: '2-digit',
                    meridiem: false
                },
                dayHeaderFormat: {weekday: 'long'},
                buttonText: {
                    today: '{{__('eap-online.video_therapy.today')}}'
                },

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
            $(`#day-off-edit-${id}`).toggleClass('d-none');
            toggleAppointmentClasses(id);
        }


    </script>
@endsection

@section('content')
    <div class="row">
        <div class="row col-12">
            {{ Breadcrumbs::render('eap-online.video-therapy.actions.expert_day_off.edit', $language_id, $permission_id) }}
            <h1 class="col-12">{{ __('eap-online.video_therapy.expert_day_off') }}: {{\App\Models\EapOnline\EapLanguage::find($language_id)->name}}</h1>
            <form id="new-day-off"
                  action="{{route('admin.eap-online.video_therapy.actions.expert_day_off.save_day_off')}}"
                  class="col-12" method="post">
                {{csrf_field()}}

                <input type="hidden" name="permission_id" value="{{$permission_id}}">
                <input type="hidden" name="language_id" value="{{$language_id}}">

                <div class="row d-flex flex-column col-12">
                    <h1 class="mb-3">{{__('eap-online.video_therapy.new_time')}}</h1>
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
                    @foreach($experts_off as $expert_off)
                        <div class="list-element col-12 group" onClick="toggleDay({{$loop->index + 1}}, this)">
                            <div class="d-flex align-items-center">
                                <p class="mr-3">{{$expert_off->name}}</p>
                            </div>

                            <button class="caret-left float-right p-0">
                                <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                        </div>
                        @foreach($days_off as $day_off)
                            @if ($day_off->expert_id == $expert_off->id)
                                <div class="list-element col-12 d-none" id="day-off-panel-{{$day_off->id}}"
                                    data-day="{{$loop->parent->index + 1}}">
                                    <div class="d-flex flex-column w-100">
                                        <div class="d-flex justify-content-between align-items-center w-100">
                                            <div class="list-elem" id="day-off-header-{{$day_off->id}}">
                                                <span>{{date('Y.m.d H:i', strtotime($day_off->from))}} - {{date('Y.m.d H:i', strtotime($day_off->to))}}</span>
                                            </div>
                                            <div class="d-flex flex-row align-items-center">
                                                <div style="color:#007bff" class="mr-3">
                                                    <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                                                        style="height:20px; margin-bottom: 3px" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                    </svg>
                                                    <span onclick="editAppointment('{{$day_off->id}}')"
                                                        class="mr-1"
                                                        id="edit-day-off-button-{{$day_off->id}}">{{__('common.edit')}}
                                                    </span>
                                                </div>
                                                <form id="delete-day-off-{{$day_off->id}}" class="m-0"
                                                    action="{{route('admin.eap-online.video_therapy.actions.expert_day_off.delete_day_off')}}"
                                                    method="post">
                                                    {{csrf_field()}}
                                                    <input type="hidden" name="expert_day_off_id"
                                                            value="{{$day_off->id}}">
                                                    <div class="d-flex flex-row" style="color:#007bff">
                                                        <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                                                            style="height:28px; margin-bottom: 3px" fill="none" viewBox="0 0 24 24"
                                                            stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                        </svg>
                                                        <button onclick="deleteAppointment('delete-day-off-{{$day_off->id}}')"
                                                            class="p-0 m-0 bg-transparent" type="button" style="color:#007bff"
                                                            >{{__('common.delete')}}</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                        <form method="post"
                                                action="{{route('admin.eap-online.video_therapy.actions.expert_day_off.edit_day_off', ['expert_day_off_id' => $day_off->id])}}"
                                                id="day-off-edit-{{$day_off->id}}"
                                                class="d-none row flex-column col-12">
                                            {{csrf_field()}}
                                            <div class="ml-n1 d-flex align-items-center">
                                                <img class="mr-1" style="width: 25px;" src="{{asset('assets/img/eap-online/clock.svg')}}"
                                                        alt="clock">
                                                <p class="m-0">{{__('eap-online.video_therapy.edit_date')}}</p>
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <input type="text" name="edit_from_time" class="col-3 timepicker"
                                                        placeholder="{{__('common.from')}}"
                                                        value="{{date('Y-m-d H:i:s', strtotime($day_off->from))}}">
                                                <span class="mb-3 mx-3">-</span>
                                                <input type="text" name="edit_to_time" class="col-3 timepicker"
                                                        placeholder="{{__('common.to')}}"
                                                        value="{{date('Y-m-d H:i:s', strtotime($day_off->to))}}">
                                            </div>
                                            <div id="not-selected-expert-{{$day_off->id}}" style="cursor: pointer;"
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
                                                    <option @if($expert->id == $day_off->expert_id) selected
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
                    <div class="d-flex flex-row align-items-center">
                        <input type="text" name="modal_from_time" class="timepicker w-50"
                           placeholder="{{__('common.from')}}">
                        <span class="ml-1 mr-1">-</span>
                        <input type="text" name="modal_to_time" class="timepicker w-50" placeholder="{{__('common.to')}}">
                    </div>
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
