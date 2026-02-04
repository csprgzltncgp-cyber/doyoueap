@extends('layout.master')

@section('title')
    Admin Dashboard
@endsection

@section('extra_js')
    <script src="/assets/js/datetime.js" charset="utf-8"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9.7.1/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.full.min.js"
            integrity="sha512-AIOTidJAcHBH2G/oZv9viEGXRqDNmfdPVPYOYKGy3fti0xIplnlgMHUGfuNRzC6FkzIo0iIxgFnr9RikFxK+sw=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        function showEditConsultation(element, consultation_id, consultation_date) {
            $('#consultation_edit form input[name="consultation_id"]').val(consultation_id);
            $('#consultation_edit form input[name="consultation_date"]').val(consultation_date);
            $('#consultation_edit').modal('show');
            consultation_li = element;
        }

        let type;

        function deleteUpload(uploadId, button) {
            Swal.fire({
                title: '{{__('common.are-you-sure-to-delete')}}',
                text: "{{__('common.operation-cannot-undone')}}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '{{__('common.yes-delete-it')}}'
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: 'DELETE',
                        url: '/ajax/delete-upload/' + uploadId,
                        success: function (data) {
                            console.log(data);
                            $(button).closest('li').remove();
                        }
                    });
                }
            });
        }


        function fileUpload(id) {
            $('#' + id + ' .dz-default').trigger('click');
        }

        function deleteConsultation(element, id) {
            Swal.fire({
                title: '{{__('common.are-you-sure-to-delete')}}',
                text: "{{__('common.operation-cannot-undone')}}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '{{__('common.yes-delete-it')}}!'
            }).then((result) => {
                if (result.value) {
                    var li = $(element).closest('li');
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: 'DELETE',
                        url: '/ajax/delete-consultation/' + id,
                        success: function (data) {
                            li.remove();
                            $('#number_of_consultations').html(parseInt($('#number_of_consultations').html()) - 1);
                        }
                    });
                }
            });
        }

        function revertExpertCannotAssign(userId, caseId, button) {
            Swal.fire({
                title: '{{__('common.are-you-sure-withdraw')}}',
                text: "{{__('common.operation-cannot-undone')}}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '{{__('common.yes-restore')}}'
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: 'DELETE',
                        url: '/ajax/revert-expert-cannot-assign/' + caseId + '/' + userId,
                        success: function (data) {
                            $(button).remove();
                        }
                    });
                }
            });
        }

        function deleteCustomerSatisfaction(caseId, element) {
            Swal.fire({
                title: '{{__('common.are-you-sure-to-delete')}}',
                text: "{{__('common.operation-cannot-undone')}}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '{{__('common.yes-delete-it')}}'
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: 'DELETE',
                        url: '/ajax/delete-customer-satisfaction/' + caseId,
                        success: function (data) {
                            $('span#score').html('');
                            $(element).remove();
                        }
                    });
                }
            });
        }

        $(function () {
            assignExpert();
            $('.datepicker').datetimepicker({
                'format': 'Y-m-d H:i',
                minDate: 0,
                step: 1
            });
            case_input_change_form();
            addConsultation();
            editConsultation();
            setStatus();
            setActivityCode();
        });
        const case_id = {{$case->id}};

        function setStatus() {
            var button_text = $('#set-status-button').html();
            $('#set-status-button').attr('disabled', 'disabled');
            const status = $(this).find('select[name="status"]').val();
            $('form[name="set-status"]').on('submit', function (e) {
                e.preventDefault();
                const form = $(this);
                const status = form.find('select[name="status"]').val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: 'POST',
                    url: '/ajax/set-status',
                    data: {
                        case_id: case_id,
                        status: status
                    },
                    success: function (data) {
                        const text = form.find('option:selected').html();
                        $('span#case-status').html(text);
                        $('#status').modal('hide');
                    },
                    error: function (error) {
                        ;
                    }
                });
            });
        }

        function case_input_change_form() {
            $('form.case_input_change').on('submit', function (e) {
                const form = $(this);
                e.preventDefault();
                const input_id = $(this).find('input[name="input_id"]').val();

                var value = $(this).find('select[name="value"]').length != 0 ? $(this).find('select[name="value"]').val() : $(this).find('input[name="value"]').val();

                if (value == null) {
                    var value = $(this).find('textarea[name="value"]').val();
                }

                const text = $(this).find('select[name="value"]').length != 0 ? $(this).find('select[name="value"] option:selected').html() : value;

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: 'POST',
                    url: '/ajax/admin/assing-new-value-to-case-input',
                    data: {
                        input_id: input_id,
                        value: value,
                        case_id: case_id
                    },
                    success: function (data) {
                        if (data.status == 0) {
                            form.closest('.modal').modal('hide');
                            $('#case_input_' + input_id + '_value_holder').html(text);
                        }
                    }
                });
            });
        }

        function editConsultation() {
            $('form[name="edit-consultation"]').on('submit', function (e) {
                e.preventDefault();
                const form = $(this);
                const date = form.find('input[name="consultation_date"]').val();
                const consultation_id = form.find('input[name="consultation_id"]').val();
                if (date == '') {
                    return false;
                }
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: 'POST',
                    url: '/ajax/edit-consultation',
                    data: {
                        consultation_id: consultation_id,
                        consultation_date: date,
                        case_id: case_id
                    },
                    success: function (data) {
                        if (data.consultation_today_exists) {
                            const error = "<p class=\"error\">{{__('common.consultation_already_exists_for_given_day')}}</p>";
                            $('form[name="edit-consultation"] input[name="consultation_date"]').after(error);
                            return false;
                        } else {
                            $('form[name="edit-consultation"] p.error').remove();
                        }
                        $('#consultation_edit').modal('hide');
                        $(consultation_li).find('span').html(date);
                    }
                });
            });
        }

        function addConsultation() {
            $('form[name="add-consultation"]').on('submit', function (e) {
                e.preventDefault();
                const form = $(this);
                const input = form.find('input[name="date"]');
                const date = input.val();
                if (date == '') {
                    return false;
                }
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: 'POST',
                    url: '/ajax/add-consultation-to-case',
                    data: {
                        case_id: case_id,
                        date: date
                    },
                    success: function (data) {
                        if (data.consultation_today_exists) {
                            const error = "<p class=\"error\">{{__('common.consultation_already_exists_for_given_day')}}</p>";
                            $('form[name="add-consultation"] input[name="date"]').after(error);
                            return false;
                        } else {
                            $('form[name="add-consultation"] p.error').remove();
                        }
                        const html = '<li class="consultation">\
            <button onClick="showEditConsultation(this,\' ' + data.id + '\',\' ' + data.time + '\')">' + ` <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                                             style="height:20px; margin-bottom: 3px"
                                             fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>` + ' Ülés ' + data.number + ' időpontja: <span> ' + data.time + ' </span></button>\
            <button onClick="deleteConsultation(this,\' ' + data.id + '\')">' + `<svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                                             style="height:20px; margin-bottom: 3px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
</svg>` + '/button>\
          </li>';
                        $('#add-consultation').closest('li').prev('li').after(html);
                        input.val('');
                        form.closest('.modal').modal('hide');
                        $('#number_of_consultations').html(parseInt($('#number_of_consultations').html()) + 1);
                        $('#cant-assing-case').remove();
                        $('#set-status-button').addClass('purple-button');
                        if (!$('#set-status-button').find('i').length) {
                            $('#set-status-button').prepend('<i class="fas fa-check-circle" style="margin-right:5px;"></i>');
                        }
                    }
                });
            })
        }

        function assignExpert() {
            $('form[name="assign-expert"]').on('submit', function (e) {
                e.preventDefault();
                const expert_id = $(this).find('select[name="experts"]').val();
                $('#experts').modal('hide');
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: 'POST',
                    url: '/ajax/assing-expert-to-case',
                    data: {
                        expert_id: expert_id,
                        case_id: case_id
                    },
                    success: function (data) {
                        const name = data.name;
                        $('#expert-assing-button').html('Kiközvetített szakértő: ' + name);
                    }
                });
            });
        }

        function setActivityCode() {
            $('form[name="set_activity_code"]').on('submit', function (e) {
                e.preventDefault();
                const activity_code = $(this).find('input[name="activity_code"]').val();
                $('#activity_code').modal('hide');
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: 'POST',
                    url: '/ajax/set-activity-code',
                    data: {
                        activity_code: activity_code,
                        case_id: case_id
                    },
                    success: function (data) {
                        $('#activity_code_button').html('Activity code: ' + activity_code);
                    }
                });
            });
        }

    </script>
@endsection

@section('extra_css')
    <link rel="stylesheet" href="/assets/css/cases/view.css?t={{time()}}">
    <link rel="stylesheet" href="/assets/css/cases/datetime.css?t={{time()}}">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.min.css"
          integrity="sha512-f0tzWhCwVFS3WeYaofoLWkTP62ObhewQ1EZn65oSYDZUg1+CyywGKkWzm8BxaJj5HGKI72PnMH9jYyIFz+GH7g=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <link rel="stylesheet" href="{{asset('assets/css/datetimepicker.css')}}">
@endsection

@section('content')
    @csrf
    {{--        start hidden post helper content--}}
    <div class="modal" tabindex="-1" id="ccase-re-generate" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">#{{$case->case_identifier}} - eset új szakértő megadása</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{route('admin.cases.generate-new-cases')}}">
                        @csrf
                        <Label>Szakértő kiválasztása</Label>
                        <select name="experts" style="width: 100%;">
                            @foreach($case->getAvailableExperts() as $expert)
                                <option value="{{$expert->id}}">{{$expert->name}}</option>
                                <option value="{{$expert->id}}">{{$expert->name}}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="old_case_id" value="{{$case->id}}">
                        <button class="button btn-radius mr-0 float-right">
                            <img class="mr-1" style="width:20px;" src="{{asset('assets/img/save.svg')}}">
                            <span class="mt-1">{{__('common.save')}}</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    {{--        end hidden post helper conten--}}
    <div class="row">
        <div class="col-12">
            {{ Breadcrumbs::render('cases.view', $case->id) }}

            <h1>{{__('common.case-view')}}</h1>
        </div>
        <div class="col-12 case-title">
            <p>{{$case->values->where('case_input_id', 1)->first()->value}}
                {{-- @if($case->created_at > now()->subMonths(3)) --}}
                    - {{$case->company ? $case->company->name : ''}}
                {{-- @endif --}}
                - {{ ($case->case_accepted_expert()) ? $case->case_accepted_expert()->name : '' }}
                - {{$case->case_type != null ? $case->case_type->getValue() : null}}
                {{-- @if($case->created_at > now()->subMonths(3)) --}}
                    - {{$case->case_location != null ? $case->case_location->getValue() : null}}
                {{-- @endif --}}
                - {{$case->case_client_name != null ? $case->case_client_name->getValue() : null}}</p>
        </div>
        <div class="col-12 case-details">
            <ul>
                <!-- Chat/video online booking -->
                @if ($online_appointment_booking && $online_appointment_booking->room_id)
                    <li class="d-flex">
                        <img class="mr-1" style="width:20px" src="{{asset('assets/img/eap_online_timetable.svg')}}">
                        {{__('common.eap_case_type_timetable')}}
                    </li>
                @endif

                <!-- Chat/Video intake booking -->
                @if ($intake_online_booking && $intake_online_booking->room_id)
                    <li>
                        <img class="mr-1" style="width:30px" src="{{asset('assets/img/eap_online.svg')}}">
                        {{__('common.eap_case_type_assigned')}}
                    </li>
                @endif
                <li>
                    <button data-toggle="modal" data-target="#status">
                        <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                             style="height:20px; margin-bottom: 3px"
                             fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        {{__('common.status')}}: <span id="case-status">{{$case->status}}</span></button>
                </li>
                <li>
                    {{__('asset.country')}}: {{$case->country->name}}
                </li>
                <li>{{__('common.identifier')}}: {{$case->case_identifier}}</li>
                @foreach($case->values as $value)
                    @if($value->input && $value->showAbleAfter3Months())
                        @if ( ($case->case_type->value != 1 && $value->input->default_type == 'case_specialization')
                        || ( ($value->input->default_type == 'ucms_case_identifier' || $value->input->default_type == 'additional_information') && $case->company->org_datas->where('country_id', $case->country_id)->first()?->contract_holder_id != 6) )
                            <!-- Skip specialization -->
                            <!-- Skip UCMS and Additional information form client inputs -->
                        @else
                            <li>
                                @if($value->input->default_type != 'company_chooser' && $value->input->default_type != 'company_chooser' /*&& $value->input->default_type != 'location'*/
                                && $value->input->default_type != 'case_type' && $value->input->default_type != 'case_specialization' && $value->input->default_type != 'case_language_skill'
                                && $value->input->default_type != 'clients_language')
                                    @if($value->input->default_type == 'case_creation_time')
                                        <button data-toggle="modal" data-target="#case_input_{{$value->input->id}}">
                                            <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                                                style="height:20px; margin-bottom: 3px"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                            {{$value->input->translation->value}}: <span
                                                    id="case_input_{{$value->input->id}}_value_holder"> {{$value->getValue()}}</span>
                                        </button>
                                    @else
                                        <button data-toggle="modal" data-target="#case_input_{{$value->input->id}}">
                                            <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                                                style="height:20px; margin-bottom: 3px"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                            {{$value->input->translation->value}}: <span
                                            id="case_input_{{$value->input->id}}_value_holder">{{$value->getValue()}}</span>
                                        </button>
                                    @endif
                                @elseif ($value->input->default_type == 'clients_language')
                                    <button data-toggle="modal" data-target="#case_input_{{$value->input->id}}">
                                        <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                                            style="height:20px; margin-bottom: 3px"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        {{$value->input->translation->value}}: <span
                                        id="case_input_{{$value->input->id}}_value_holder">
                                            {{$language_skills->where('id', $value->value)->first()->translation->value}}
                                        </span>
                                    </button>
                                @else
                                    <span class="not-editable">{{$value->input->translation->value}}:
                                        <span id="case_input_{{$value->input->id}}_value_holder">{{$value->getValue()}}</span>
                                    </span>
                                @endif
                            </li>
                        @endif
                    @endif
                @endforeach

                @if($case->consultations->count() <= 0)
                    @if($case->case_accepted_expert())
                        <li>
                            <button data-toggle="modal" data-target="#experts" id="expert-assing-button">
                                <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                                    style="height:20px; margin-bottom: 3px"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                {{__('common.expert-outsourced')}}: {{$case->case_accepted_expert()->name}}</button>
                        </li>
                    @else
                        <li>
                            <button data-toggle="modal" data-target="#experts" id="expert-assing-button"><i
                                        class="fas fa-plus-circle"></i> {{__('common.expert-outsourced')}}
                            </button>
                        </li>
                    @endif
                @endif
                @if($case->case_company_contract_holder() == 1)
                    <li>
                        <button data-toggle="modal" id="activity_code_button" data-target="#activity_code">
                            <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                                 style="height:20px; margin-bottom: 3px"
                                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Activity code: {{$case->activity_code}}</button>
                    </li>
                @endif

                <li>{{__('common.number-of-sessions')}}: <span
                            id="number_of_consultations">{{sizeof($case->consultations)}}</span></li>
                @if(sizeof($case->consultations))
                    @foreach (($online_appointment_booking) ? $case->consultations()->withTrashed()->get()->sortBy('id') : $case->consultations()->get()->sortBy('created_at') as $key => $consultation)
                        @php $date = \Carbon\Carbon::parse($consultation->created_at)->format('Y-m-d H:i') @endphp
                        <li class="consultation @if ($consultation->deleted_at) consultation--deleted @endif">
                            <button onClick="showEditConsultation(this,{{$consultation->id}},'{{$date}}')" @if ($consultation->deleted_at) disabled @endif>
                                <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                                     style="height:20px; margin-bottom: 3px"
                                     fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                {{__('common.date-of-session')}} {{$key+1}}:
                                <span>{{$date}}</span>
                                - {{$consultation->expert ? $consultation->expert->name : null}}</button>
                            @if (!$consultation->deleted_at)
                                <button onClick="deleteConsultation(this,{{$consultation->id}})">
                                    <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                                        style="height:20px; margin-bottom: 3px" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            @endif
                        </li>
                    @endforeach
                @endif
                @if($case->customer_satisfaction_not_possible)
                    <li>
                        <label class="container" id="customer-satisfaction-not-possible">Elégedettségi kérdőív kitöltése
                            nem volt lehetséges
                            <input type="checkbox" name="customer-satisfaction-not-possible" disabled="disabled"
                                   @if($case->customer_satisfaction_not_possible) checked="checked" @endif >
                            <span class="checkmark"></span>
                        </label>
                    </li>
                @endif
                @if($case->case_accepted_expert() && !(in_array(5,$case->case_accepted_expert()->permission()->pluck('permissions.id')->toArray())))
                    <li>
                        <button data-toggle="modal" data-target="#customer_satisfaction_modal"
                                id="customer_satisfaction_button">{{__('common.satisfaction-level')}}<span
                                    id="score">: {{$case->customer_satisfaction}}</span>
                        </button> @if($case->customer_satisfaction !== null)
                            <button onClick="deleteCustomerSatisfaction({{$case->id}},this)">
                                <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                                     style="height:20px; margin-bottom: 3px" fill="none" viewBox="0 0 24 24"
                                     stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        @endif</li>
                @endif

                @if(!empty($case->confirmed_at))
                    <li>
                        {{__('common.closed_at')}}: <span id="case-status">{{Carbon\Carbon::parse($case->confirmed_at)->format('Y-m-d')}}</span>
                    </li>
                @endif
            </ul>
        </div>
        <div class="col-12 button-holder">

        </div>
        <div class="col-12 button-holder">
            <a href="#" class="button d-none">{{__('common.save')}}</a>
            <a href="" class="button d-none">Szerkesztés</a>
            @if($case->case_accepted_expert())
                <a class="button btn-radius float-right ml-3 d-flex" href="mailto:{{$case->case_accepted_expert()->email}}">
                    <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                         style="height:20px; margin-bottom: 3px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    {{__('common.send-mail')}}</a>
            @endif
        </div>
        <div class="col-12 button-holder mt-3">
            @if($case->case_accepted_expert() && $case->case_accepted_expert()->pivot->accepted == App\Enums\CaseExpertStatus::REJECTED->value)
                <button class="button mr-0"
                        onClick="revertExpertCannotAssign({{$case->case_accepted_expert()->id}},{{$case->id}}, this)">'A
                    szakértő nem vállalta az esetet' visszavonása
                </button>
            @endif
        </div>

        <div class="col-12 back-button mb-5">
            @if($case->getRawOriginal('status') != 'confirmed' && $case->getRawOriginal('status') != 'client_unreachable_confirmed')
                <a href="{{route('admin.cases.in_progress') . '?country_id=' . $case->country_id}}">{{__('common.back-to-list')}}</a>
            @else
                <a href="{{route('admin.cases.closed')}}">{{__('common.back-to-list')}}</a>
            @endif
        </div>
    </div>
@endsection

@section('modal')
    @foreach($case->values as $value)
        @if($value->input)
            @if($value->input->default_type != 'company_chooser' && $value->input->default_type != 'company_chooser' && $value->input->default_type != 'location' && $value->input->default_type != 'case_type'
            && $value->input->default_type != 'case_specialization' && $value->input->default_type != 'case_language_skill'
            && $value->input->default_type != 'clients_language')
                <div class="modal" tabindex="-1" id="case_input_{{$value->input->id}}" role="dialog">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">{{$value->input->translation->value}}</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form method="post" class="case_input_change">
                                    <input type="hidden" value="{{$value->input->id}}" name="input_id">
                                    {{csrf_field()}}
                                    @if($value->input->type == 'select')
                                        <select class="w-100" name="value">
                                            @foreach($value->input->values->where('visible',1) as $v)
                                                <option value="{{$v->id}}"
                                                        @if($v->id == $value->value) selected @endif>{{$v->translation->value}}</option>
                                            @endforeach
                                        </select>
                                    @elseif($value->input->type == 'date')
                                        <input type="text" class="w-100" name="value" class="datepicker" value="{{$value->value}}"
                                               placeholder="{{$value->input->translation->value}}"/>
                                    @elseif($value->input->type == 'integer')
                                        <input type="number" name="value" value="{{$value->value}}"
                                               placeholder="{{$value->input->translation->value}}"/>
                                    @elseif($value->input->type == 'double')
                                        <input type="text" class="w-100" name="value" value="{{$value->value}}"
                                               placeholder="{{$value->input->translation->value}}"/>
                                    @elseif($value->input->type == 'text')
                                        <textarea name="value" cols="30" rows="10">{{$value->value}}</textarea>
                                    @endif
                                    <button class="btn-radius float-right" style="--btn-margin-right: 0px;">
                                        <img class="mr-1" style="width:20px;" src="{{asset('assets/img/save.svg')}}">
                                        <span class="mt-1">{{__('common.save')}}</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif($value->input->default_type == 'location')
                <div class="modal" tabindex="-1" id="case_input_{{$value->input->id}}" role="dialog">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">{{$value->input->translation->value}}</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form method="post" class="case_input_change">
                                    <input type="hidden" value="{{$value->input->id}}" name="input_id">
                                    {{csrf_field()}}
                                    <select class="w-100" name="value">
                                        @foreach($case->country->cities->sortBy('name') as $city)
                                            <option value="{{$city->id}}"
                                                    @if($city->id == $value->value) selected @endif>{{$city->name}}</option>
                                        @endforeach
                                    </select>
                                    <button class="btn-radius float-right" style="--btn-margin-right: 0px;">
                                        <img class="mr-1" style="width:20px;" src="{{asset('assets/img/save.svg')}}">
                                        <span class="mt-1">{{__('common.save')}}</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif ($value->input->default_type == 'clients_language')
                <div class="modal" tabindex="-1" id="case_input_{{$value->input->id}}" role="dialog">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">{{$value->input->translation->value}}</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form method="post" class="case_input_change">
                                    <input type="hidden" value="{{$value->input->id}}" name="input_id">
                                    {{csrf_field()}}
                                    <select class="w-100" name="value">
                                        @foreach($language_skills as $language)
                                            <option value="{{$language->id}}"
                                                    @if($language->id == $value->value) selected @endif>{{$language->translation->value}}</option>
                                        @endforeach
                                    </select>
                                    <button class="btn-radius float-right" style="--btn-margin-right: 0px;">
                                        <img class="mr-1" style="width:20px;" src="{{asset('assets/img/save.svg')}}">
                                        <span class="mt-1">{{__('common.save')}}</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endif
    @endforeach
    <div class="modal" tabindex="-1" id="activity_code" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Activity code</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post" name="set_activity_code">
                        {{csrf_field()}}
                        <input type="text" class="w-100" name="activity_code" value="{{$case->activity_code}}">
                        <button class="btn-radius float-right" style="--btn-margin-right: 0px;">
                            <img class="mr-1" style="width:20px;" src="{{asset('assets/img/save.svg')}}">
                            <span class="mt-1">{{__('common.save')}}</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal" tabindex="-1" id="experts" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{__('common.expert-outsourcing')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post" name="assign-expert">
                        {{csrf_field()}}
                        <select name="experts">
                            @foreach($case->getAvailableExperts() as $expert)
                                <option value="{{$expert->id}}">{{$expert->name}}</option>
                            @endforeach
                        </select>
                        <button class="btn-radius float-right" style="--btn-margin-right: 0px;">
                            <img class="mr-1" style="width:20px;" src="{{asset('assets/img/save.svg')}}">
                            <span class="mt-1">{{__('common.save')}}</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal" tabindex="-1" id="status" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{__('common.status')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post" name="set-status">
                        {{csrf_field()}}
                        <select class="w-100" name="status">
                            <option value="opened" @if($case->getRawOriginal('status') == 'opened') selected @endif >Új
                            </option>
                            <option value="assigned_to_expert"
                                    @if($case->getRawOriginal('status') == 'assigned_to_expert') selected @endif>
                                {{__('common.referred-to-an-expert')}}
                            </option>
                            <option value="employee_contacted"
                                    @if($case->getRawOriginal('status') == 'employee_contacted') selected @endif>
                                Kapcsolatfelvétel megtörtént
                            </option>
                            <option value="client_unreachable"
                                    @if($case->getRawOriginal('status') == 'client_unreachable') selected @endif>{{__('common.client-is-unreachable')}}
                            </option>
                            <option value="confirmed"
                                    @if($case->getRawOriginal('status') == 'confirmed') selected @endif>
                                {{__('workshop.closed')}}
                            </option>
                            <option value="client_unreachable_confirmed"
                                    @if($case->getRawOriginal('status') == 'client_unreachable_confirmed') selected @endif>
                                {{__('common.the-client-is-unavailable-locked')}}
                            </option>
                            <option value="interrupted"
                                    @if($case->getRawOriginal('status') == 'interrupted') selected @endif>{{__('common.counseling-was-interrupted')}}
                            </option>
                            <option value="interrupted_confirmed"
                                    @if($case->getRawOriginal('status') == 'interrupted_confirmed') selected @endif>
                                {{__('common.counseling-was-interrupted-and-closed')}}
                            </option>
                        </select>
                        <button class="btn-radius float-right" style="--btn-margin-right: 0px;">
                            <img class="mr-1" style="width:20px;" src="{{asset('assets/img/save.svg')}}">
                            <span class="mt-1">{{__('common.save')}}</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal" tabindex="-1" id="consultation" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konzultáció időpontja</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post" name="add-consultation">
                        {{csrf_field()}}
                        <input type="text" class="w-100" name="date" placeholder="Konzultáció időpontja" class="datepicker">
                        <button class="btn-radius float-right" style="--btn-margin-right: 0px;">
                            <img class="mr-1" style="width:20px;" src="{{asset('assets/img/save.svg')}}">
                            <span class="mt-1">{{__('common.save')}}</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal" tabindex="-1" id="consultation_edit" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ülés módosítása</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post" name="edit-consultation">
                        {{csrf_field()}}
                        <input type="hidden" name="consultation_id" value="">
                        <input type="text" class="w-100 datepicker" name="consultation_date" placeholder="Konzultáció időpontja">
                        <button class="btn-radius float-right" style="--btn-margin-right: 0px;">
                            <img class="mr-1" style="width:20px;" src="{{asset('assets/img/save.svg')}}">
                            <span class="mt-1">{{__('common.save')}}</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
