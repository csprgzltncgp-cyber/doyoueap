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
    <script src="{{asset('assets/js/datetime.js')}}"></script>
    <script>
        $('.timepicker').datetimepicker({
            format: 'HH:mm',
        })

        let dates = [];

        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd',
            orientation: 'bottom',
            autoclose: true
        }).on('changeDate', function (e) {
            dates.push(e.format(0,"yyyy-mm-dd"));
            list_selected_dates(dates);

            $('#date_select').val('');
        });

        function list_selected_dates(date)
        {
            const selected_dates = document.getElementById('selected_dates');
            selected_dates.innerHTML = '';

            $.each(dates, function (index, date) {
                selected_dates.innerHTML += '\
                    <div class="w-100">'
                        +date+
                        '<svg onclick="remove_date('+index+')" xmlns="http://www.w3.org/2000/svg" style="width:20px; cursor:pointer;" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 ml-1">\
                        <path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />\
                        </svg>\
                    </div>\
                    <input type="hidden" name="dates[]" value="'+date+'">'
            });
        }

        function remove_date(index)
        {
            dates.splice(index, 1);
            list_selected_dates(dates);
        }

        function open_modal(id) {
            $(`#${id}`).modal("show");
        }

        let times = [];

        function save_time() {

            times.push({
                'from':$('input[name="modal_from_time"]').val(),
                'to':$('input[name="modal_to_time"]').val()
            });

            $('#modal-time-picker').modal("hide");
            $('input[name="from_time"]').val($('input[name="modal_from_time"]').val());
            $('input[name="to_time"]').val($('input[name="modal_to_time"]').val());
            $("#time-picker-div").text($("input[name='from_time']").val() + ' - ' + $("input[name='to_time']").val());

            list_selected_times();
        }

        function list_selected_times()
        {
            const selected_dates = document.getElementById('selected_times');
            selected_dates.innerHTML = '';

            $.each(times, function (index, time) {
                selected_dates.innerHTML += '\
                    <div class="w-100">'
                        +time.from+'-'+time.to+
                        '<svg onclick="remove_time('+index+')" xmlns="http://www.w3.org/2000/svg" style="width:20px; cursor:pointer;" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 ml-1">\
                        <path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />\
                        </svg>\
                    </div>\
                    <input type="hidden" name="times[]" value=`'+JSON.stringify(time)+'`>'
            });
        }

        function remove_time(index)
        {
            times.splice(index, 1);
            list_selected_times(dates);
        }

        function toggle_rows(parent_id, element) {
            if ($(element).hasClass('active')) {
                $(element).removeClass('active');

                $('.list-element .caret-left').html(
                    `<svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                    </svg>`);

                $('.list-element').each(function () {
                    if ($(this).data('parent-id') && $(this).data('parent-id') == parent_id) {
                        $(this).addClass('d-none');

                        // Close child elements if active
                        if ($(this).hasClass('active')) {
                            $(this).click();
                        }
                    }
                });
            } else {
                $(element).addClass('active');

                $(element).find('button.caret-left').html(
                    `<svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7" />
                    </svg>`);

                $('.list-element').each(function () {
                    if ($(this).data('parent-id') && $(this).data('parent-id') == parent_id) {
                        $(this).removeClass('d-none');
                    }
                });
            }
        }

        @if(session()->has('appointment-already-booked'))
            Swal.fire({
                title: "{{__('eap-online.onsite_consultation.appointment_already_booked')}}",
                icon: 'error'
            });
        @endif

        function delete_consultation_date(id, element) {
            Swal.fire({
                title: '{{__('common.are-you-sure-to-delete')}}',
                text: "{{__('common.operation-cannot-undone')}}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '{{__('common.yes-delete-it')}}',
                cancelButtonText: '{{__('common.cancel')}}',
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: 'POST',
                        url: '/ajax/delete-onsite-consultation-date/' + id,
                        success: function (data) {
                            if (data.status != 3) {
                                $('#consultation_date_row_'+id).remove();
                            } else {
                                Swal.fire({
                                    title: "{{__('eap-online.onsite_consultation.appointment_already_booked')}}",
                                    icon: 'error'
                                });
                            }
                        }
                    });
                }
            });
        }
    </script>
@endsection

@section('content')
    <div class="col-12 ml-0 pl-0">
        <div class="row col-12">
            {{ Breadcrumbs::render('eap-online.onsite-consultation.date.index', $consultation) }}
            <h1 class="col-12">{{__('eap-online.onsite_consultation.appointments')}}: {{$consultation->company->name}} -
                {{$consultation->country->name}} -
                {{$consultation->permission->translation->value}} -
                {{$consultation->place->name}} -
                {{$consultation->place->address}} -
                ({{implode(', ',$consultation->languages->pluck('name')->toArray())}})
            </h1>

            <form id="new-consultation"
                action="{{route('admin.eap-online.onsite-consultation.date.store')}}"
                class="col-12" method="post">
                {{csrf_field()}}

                <input type="hidden" name="onsite_consultation_id" value="{{$consultation->id}}">
                <input type="hidden" name="permission_id" value="{{$consultation->permission->id}}">
                <input type="hidden" name="country_id" value="{{$consultation->country->id}}">

                <div class="row d-flex flex-column col-12">
                    <h1 class="mb-3">{{__('eap-online.video_therapy.new_time')}}</h1>
                    @if (in_array($consultation->type, [\App\Enums\OnsiteConsultationType::WITH_EXPERT, \App\Enums\OnsiteConsultationType::ONLINE_WITH_EXPERT]))
                    <div class="col-4 mb-1 mt-3" style="border: 2px solid rgb(89,198,198) !important; padding-top:5px; padding-bottom:5px">
                        <div class="d-flex flex-row align-items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="color: rgb(89, 198, 198); height: 25px; width: 25px;" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                              </svg>
                            <span class="mr-1" style="color: rgb(89, 198, 198);">{{ __('eap-online.onsite_consultation.expert') }}:</span>
                            <select name="expert" style="margin:0px!important; padding: 10px 0px 10px 0px !important; border:0px!important; color:black!important">
                                <option value="" selected hidden>{{__('common.please-choose-one')}}</option>
                                @foreach ($experts as $expert)
                                    <option value="{{$expert->id}}">{{$expert->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @endif
                    <div class="col-4 mb-1 mt-3" style="border: 2px solid rgb(89,198,198) !important;">
                        <div class="d-flex flex-column">
                            <div onclick="date_select.focus();" class="d-flex flex-row align-items-center" style="cursor:pointer;">
                                <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="color: rgb(89, 198, 198); height: 25px; width: 25px;" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5m-9-6h.008v.008H12v-.008zM12 15h.008v.008H12V15zm0 2.25h.008v.008H12v-.008zM9.75 15h.008v.008H9.75V15zm0 2.25h.008v.008H9.75v-.008zM7.5 15h.008v.008H7.5V15zm0 2.25h.008v.008H7.5v-.008zm6.75-4.5h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V15zm0 2.25h.008v.008h-.008v-.008zm2.25-4.5h.008v.008H16.5v-.008zm0 2.25h.008v.008H16.5V15z" />
                                </svg>
                                <div class="d-flex justify-content-between align-items-center w-100">
                                    <input type="text" name="date_select" id="date_select" readonly class="datepicker w-100" style="margin:0px!important; padding: 10px 10px 10px 0px !important; border:0px!important; background-color: transparent; cursor:pointer; margin-top:4px!important"
                                    placeholder="{{__('eap-online.onsite_consultation.add_dates')}}">
                                    <svg xmlns="http://www.w3.org/2000/svg" style="width:30px; color: rgb(89, 198, 198);" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="d-flex flex-column pb-1" id="selected_dates" style="color: rgb(89, 198, 198);"></div>
                        </div>
                    </div>
                    <div class="col-4 mb-1 mt-3" style="border: 2px solid rgb(89,198,198) !important;">
                        <div class="d-flex flex-column">
                            <div class="d-flex flex-row align-items-center w-100" style="padding-top:10px; padding-bottom:10px; color: rgb(89,198,198); cursor:pointer" onclick="open_modal('modal-time-picker')">
                                <svg style="height: 20px; width: 20px;" xmlns="http://www.w3.org/2000/svg" class="mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div class="d-flex justify-content-between align-items-center w-100">
                                    <div id="time-picker-div w-100">
                                        {{__('eap-online.onsite_consultation.add_appointments')}}
                                    </div>
                                    <svg xmlns="http://www.w3.org/2000/svg" style="width:25px; color: rgb(89, 198, 198);" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="d-flex flex-column pb-1" id="selected_times" style="color: rgb(89, 198, 198);"></div>
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
            </form>

            @if($dates->isEmpty())
                <div class="col-12">
                    <h1 class="mt-5">{{__('eap-online.onsite_consultation.no_appointments')}}</h1>
                </div>
            @else
                <div class="col-12">
                    <h1 class="mt-5">{{__('eap-online.video_therapy.edit_fixed_dates')}}</h1>
                </div>
            @endif

            @foreach($dates as $date)
                @include('components.eap-online.onsite-consultation.date_line_component', ['date' => $date, 'consultation_type' => $consultation->type])
            @endforeach
        </div>
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
                    <button class="button btn-radius mr-3 float-right" style="--btn-margin-right: 0px; --btn-height:auto;" onclick="save_time()">
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
