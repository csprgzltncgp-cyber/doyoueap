@extends('layout.master')

@section('title')
    Expert Dashboard
@endsection

@section('extra_js')
    <script src="/assets/js/datetime.js" charset="utf-8"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9.7.1/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.full.min.js"
        integrity="sha512-AIOTidJAcHBH2G/oZv9viEGXRqDNmfdPVPYOYKGy3fti0xIplnlgMHUGfuNRzC6FkzIo0iIxgFnr9RikFxK+sw=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        let consultation_li;
        let type;
        let wos_count = {{ count($case->wos_answers) }};
        let consultation_count = {{ count($case->consultations) }};
        let appointment_id = null;
        let current_consultation_count = {{ ($online_appointment_booking) ? $case->consultations()->withTrashed()->count() : $case->consultations()->get()->count() }}

        let rawDate = @js($case->created_at->format('Y-m-d'));
        let caseDate = new Date(rawDate);
        let startDate = new Date('2023-04-03');

        const case_id = {{ $case->id }};
        const appointment_booking = @json($online_appointment_booking);
        const available_consultations = {{ optional(optional($case->values->where('case_input_id', 21)->first())->input_value)->value }};

        let room_id = null;
        let eap_user_id = null;

        function after_first_consultation_popup ()
        {
            warning_msg = ({{ $case->consultations->count() }} <= 1) ? '{{ __('common.expert_case_deletion_warning') }}' : '{{ __('common.operation-cannot-undone') }}'

            Swal.fire({
                title: '{{ __('common.expert_case_add_consultation_contact') }}',
                icon: 'warning',
                showCancelButton: false,
            });
        }

        @if ($online_appointment_booking)
            room_id = "{{ $online_appointment_booking->room_id }}";
            eap_user_id = "{{ $online_appointment_booking->user_id }}"
        @elseif($intake_online_booking)
            room_id = "{{ $intake_online_booking->room_id }}";
            eap_user_id = "{{ $intake_online_booking->user_id }}";
        @endif

        $(function() {
            $('.datepicker').datetimepicker({
                'format': 'Y-m-d H:i',
                step: 1
            });
            customerSatisfaction();
            customerSatisfactionNotPossible();
            addConsultation();
            editConsultation();
            sendQuestionToOperator();
            case_input_change_form();

            $('#add-consultation').on('click', function() {
                @if (empty($case->phq9_opening) &&
                        count($case->consultations) == 1 &&
                        $case->case_company_contract_holder() == 4 &&
                        $case->case_type->value == 1 &&
                        in_array(intval($case->country_id), [3, 7, 6]))
                    $('#phq9_opening').addClass('need-to-close');
                    Swal.fire(
                        '{{ __('phq9.phq9_opening_validation') }}',
                        '',
                        'error'
                    );
                @else
                    $('#consultation').modal('show');
                @endif
            });
        });

        function cantAssignCase() {
            Swal.fire({
                title: '{{__('popup.cant_assign_case.title')}}',
                html: `
                <form class="swal-survey p-4 px-4">
                    @if(optional(optional(auth()->user())->invoice_datas)->invoicing_type == App\Enums\InvoicingType::TYPE_NORMAL)
                        <div class="d-flex align-items-center" style="margin-top: 10px">
                            <div class="d-flex align-items-center" style="margin-right:10px">
                                <input class="swal2-input answer-input" type="radio" name="reason" value="{{App\Enums\CantAssignCaseReasonEnum::NOT_AVAILABLE->value}}">
                            </div>
                            <div class="text-dark d-flex align-items-center survey-answer">
                                <span>{{__('popup.cant_assign_case.not_available_1')}}</span>
                                <input class="swal2-input" style="width: 75px !important; margin: 0px 10px;" type="number" placeholder="5" name="days" min="1"></input>
                                <span>{{__('popup.cant_assign_case.not_available_2')}}</span>
                            </div>
                        </div>
                    @endif
                    <div class="d-flex align-items-center" style="margin-top: 10px">
                        <div class="d-flex align-items-center" style="margin-right:10px">
                            <input class="swal2-input answer-input" type="radio" name="reason" value="{{App\Enums\CantAssignCaseReasonEnum::PROFESSIONAL_REASONS->value}}">
                        </div>
                        <div class="text-dark survey-answer">
                            {{__('popup.cant_assign_case.professional')}}
                        </div>
                    </div>
                    <div class="d-flex align-items-center" style="margin-top: 10px">
                        <div class="d-flex align-items-center" style="margin-right:10px">
                            <input class="swal2-input answer-input" type="radio" name="reason" value="{{App\Enums\CantAssignCaseReasonEnum::ETHICAL_REASONS->value}}">
                        </div>
                        <div class="text-dark survey-answer">
                            {{__('popup.cant_assign_case.ethical')}}
                        </div>
                    </div>
                </form>
                `,
                showCancelButton: true,
                width: '800px',
                preConfirm: () => {
                    const reason = $('.answer-input:checked').val();
                    const days = $('input[name="days"]').val();

                    if(!reason) {
                        return Swal.showValidationMessage('Kérjük válasszon egy okot!');
                    }

                    if (reason == "{{App\Enums\CantAssignCaseReasonEnum::NOT_AVAILABLE->value}}" && (days == '' || days < 1)) {
                        return Swal.showValidationMessage('Kérjük adja meg a napok számát!');
                    }

                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: 'POST',
                        url: '/ajax/expert/cant-assign',
                        data: {
                            case_id: case_id,
                            reason: reason,
                            days: days
                        },
                        success: function(data) {
                            $('#cant-assing-case').addClass('active');
                            window.location.replace('{{ route('expert.cases.in_progress') }}');
                        }
                    });
                }
            });
        }

        function showNestleQuestionnaire() {
            Swal.fire({
                title: "Dear Partner!",
                html: 'We would like to ask your client to fill out a questionnaire!' +
                    '<br>' +
                    'This requires your client’s consent. Please ask your client if you can email him/her the questionnaire. If the client agrees, ask for an email address, enter it in the box below, and then click send. The questionnaire will be automatically sent to the email address provided by the client.' +
                    '<br><br>' +
                    '<input id="nestle-questionnaire-email" autofocus minlength="3" class="form-control" type="email" placeholder="Email">',
                showCancelButton: true,
                confirmButtonText: 'Send',
                closeOnConfirm: false,
                animation: "slide-from-top",
                inputPlaceholder: "Write something"
            }).then(function(result) {
                if (result.value) {
                    let email = $('#nestle-questionnaire-email').val();
                    if (email) {
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            type: 'post',
                            url: '/ajax/send-nestle-questionnaire',
                            data: {
                                email: email,
                                case_id: "{{ $case->id }}"
                            },
                            success: function(data) {
                                Swal.fire(
                                    'Email sent successfully!',
                                    '',
                                    'success'
                                );
                            },
                            error: async function(error) {
                                let errors = $.parseJSON(error.responseText);
                                Swal.fire(
                                    errors.message,
                                    '',
                                    'error'
                                );
                            }
                        });
                    }
                }
            });
        }

        function showCustomerSatisfactionModal(show) {
            if (show) {
                Swal.fire(
                    '',
                    '{{ __('common.satisfaction-level-needed') }}',
                    'error'
                );
            }
        }

        function caseInterrupted(element, caseId) {
            Swal.fire({
                text: "{{ __('popup.close-case') }}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '{{ __('common.yes') }}',
                cancelButtonText: '{{ __('common.no') }}',
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: 'PUT',
                        url: '/ajax/case-interrupted/' + caseId,
                        success: function(data) {
                            if (data.status == 0) {
                                window.location.replace('{{ route('expert.cases.in_progress') }}');
                            } else {
                                Swal.fire(
                                    '{{ __('common.case-closing-failed') }}',
                                    '{{ __('common.client_contacted_must_be_pushed') }}',
                                    'error'
                                );
                            }
                        },
                        error: function(error) {
                            Swal.fire(
                                '{{ __('common.error') }}!',
                                '{{ __('common.modification-failed-error-code') }} S3R',
                                'error'
                            );
                        }
                    });
                }
            });
        }

        function closeCase(element, caseId) {
            Swal.fire({
                text: "{{ __('popup.close-case') }}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '{{ __('common.yes') }}',
                cancelButtonText: '{{ __('common.no') }}',
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: 'GET',
                        url: '/ajax/close-case/' + caseId,
                        success: function(data) {
                            if (data.status == 0) {
                                if (data.case.closeable == 1) {
                                    window.location.replace('{{ route('expert.cases.in_progress') }}');
                                } else {
                                    let msg;

                                    $('.need-to-close').removeClass('need-to-close');
                                    if (data.case.details.consultations !== null && data.case.details
                                        .consultations == 0) {
                                        $('#add-consultation').addClass('need-to-close');
                                        msg = '{{ __('common.add-session-to-close') }}';
                                    } else if (data.case.details.customer_satisfaction !== null && data
                                        .case.details.customer_satisfaction == 0) {
                                        $('#customer_satisfaction').addClass('need-to-close');
                                        msg = '{{ __('common.satisfaction-level-needed') }}';
                                    } else if (data.case.details.wos_survey !== null && data.case
                                        .details.wos_survey == 0) {
                                        $('#wos_survey').addClass('need-to-close');
                                        msg = '{{ __('common.wos_survey_validation') }}';
                                    } else if (data.case.details.phq9_closing !== null && data.case
                                        .details.phq9_closing == 0) {
                                        $('#phq9_closing').addClass('need-to-close');
                                        msg =
                                            '{{ __('phq9.phq9_closing_validation', [], get_phq9_language($case)) }}';
                                    } else if (data.case.details.phq9_opening !== null && data.case
                                        .details.phq9_opening == 0) {
                                        $('#phq9_opening').addClass('need-to-close');
                                        msg =
                                            '{{ __('phq9.phq9_opening_validation', [], get_phq9_language($case)) }}';
                                    } else if (data.case.details.last_consultation !== null && data.case
                                        .details.last_consultation == 0) {
                                        msg =
                                            '{{ __('common.last_consultation_validation', ['date' => ':date']) }}';
                                        msg = msg.replace(':date', data.case.details
                                            .last_consultation_date);
                                    } else if (data.case.details.wos_survey_cgp !== null && data.case
                                        .details.wos_survey_cgp == 0) {
                                        $('#wos_survey_cgp').addClass('need-to-close');
                                        msg = '{{ __('popup.wos_case_close_warning') }}';
                                    } else if (Object.keys(data.case.details).length > 1) {
                                        Object.values(data.case.details).map(id => {
                                            $(`#${id}`).addClass('need-to-close');
                                        });
                                    } else if (data.case.details.length >= 1) {
                                        data.case.details.map(id => {
                                            $(`#${id}`).addClass('need-to-close');
                                        });
                                    }
                                    Swal.fire(
                                        '{{ __('common.case-closing-failed') }}',
                                        msg,
                                        'error'
                                    );
                                }
                            }
                        },

                        error: function(error) {
                            Swal.fire(
                                '{{ __('common.error') }}!',
                                '{{ __('common.modification-failed-error-code') }} S3R',
                                'error'
                            );
                        }
                    });
                }
            });
        }

        function clientUnreachable(id, element) {
            Swal.fire({
                text: "{{ __('popup.close-case') }}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '{{ __('common.yes') }}',
                cancelButtonText: '{{ __('common.no') }}',
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: 'POST',
                        url: '/ajax/client-unreachable',
                        data: {
                            case_id: id,
                        },
                        success: function(data) {
                            if (data.status == 0) {
                                window.location.replace('{{ route('expert.cases.in_progress') }}');
                            } else {
                                Swal.fire(
                                    '{{ __('common.case-closing-failed') }}',
                                    '{{ __('common.client_contacted_must_be_pushed') }}',
                                    'error'
                                );
                            }
                        },
                        error: function(error) {
                            Swal.fire(
                                '{{ __('common.error') }}!',
                                '{{ __('common.modification-failed-error-code') }} S3R',
                                'error'
                            );
                        }
                    });
                }
            });
        }

        function showEditConsultation(element, consultation_id, consultation_date) {
            $('#consultation_edit form input[name="consultation_id"]').val(consultation_id);
            $('#consultation_edit form input[name="consultation_date"]').val(consultation_date);
            $('#consultation_edit').modal('show');
            consultation_li = element;
        }

        function customerSatisfactionNotPossible() {
            $('input[name="customer-satisfaction-not-possible"]').on('change', function() {
                const checked = $(this).is(':checked');
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: 'POST',
                    url: '/ajax/customer-satisfaction-not-possible',
                    data: {
                        case_id: case_id,
                        checked: checked ? 1 : 0
                    },
                    success: function(data) {
                        if (checked == 1) {
                            $('#not-possible').removeClass('need-to-close');
                            $('li#elegedettsegi').removeClass('need-to-close');
                        }
                    },
                    error: function(error) {
                        alert('{{ __('common.modification-failed') }}')
                    }
                });
            });
        }

        function sendQuestionToOperator() {
            $('form[name="mail-to-operator"]').on('submit', function(e) {
                e.preventDefault();
                const form = $(this);
                // const question = form.find('input[name="question"]').val();
                const question = form.find('textarea[name="question"]').val();

                if (question == '') {
                    return false;
                }
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: 'POST',
                    url: '/ajax/send-question-to-operator',
                    data: {
                        question: question,
                        case_id: case_id
                    },
                    success: function(data) {
                        $('#mail-to-operator').modal('hide');
                        form.find('textarea[name="question"]').val('');
                        Swal.fire(
                            '{{ __('common.email-sent-successfully-2') }}',
                            '',
                            'success'
                        );
                    }
                });
            });
        }

        function wos_survey_clicked() {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: '/ajax/wos-survey-clicked',
                data: {
                    case_id: case_id
                },
                success: function(data) {
                    $('#wos_survey').removeClass('need-to-close');
                }
            })
        }

        async function phq9_clicked(type) {
            const {
                value: formValues
            } = await Swal.fire({
                width: 800,
                html: `
                    <form style="display:flex; flex-direction:column; align-items:flex-start;">
                        <p style="color: #76c4c5; float:left; font-size:15px;">{{ __('phq9.open_text', [], get_phq9_language($case), [], get_phq9_language($case)) }}</p>

                        <p style="color:black; font-weight: bold; float:left; margin-top:15px;">{{ __('phq9.open_question', [], get_phq9_language($case)) }}</p>

                        <label style="float: left; text-align: initial; margin-top:15px; font-style: italic;" for="currency">{{ __('phq9.question_0', [], get_phq9_language($case)) }}</label>

                        <div style="display:flex; flex-direction:column; align-items:flex-start;  margin-top:5px;">
                            <label style="display: flex;">
                                <input style="width: auto; margin-right:10px;" required id="q0_0" type="radio" name="q0" value="0">
                                <span style="font-size:15px; width: auto;" class="swal2-label">{{ __('phq9.answer_0', [], get_phq9_language($case)) }}</span>
                            </label>

                            <label style="display: flex;">
                                <input style="width: auto; margin-right:10px;" required id="q0_1" type="radio" name="q0" value="1">
                                <span style="font-size:15px; width: auto;" class="swal2-label">{{ __('phq9.answer_1', [], get_phq9_language($case)) }}</span>
                            </label>

                            <label style="display: flex;">
                                <input style="width: auto; margin-right:10px;" required id="q0_2" type="radio" name="q0" value="2">
                                <span style="font-size:15px; width: auto;" class="swal2-label">{{ __('phq9.answer_2', [], get_phq9_language($case)) }}</span>
                            </label>

                            <label style="display: flex;">
                                <input style="width: auto; margin-right:10px;" required id="q0_3" type="radio" name="q0" value="3">
                                <span style="font-size:15px; width: auto;" class="swal2-label">{{ __('phq9.answer_3', [], get_phq9_language($case)) }}</span>
                            </label>
                        </div>

                        <label style="float: left; text-align: initial; margin-top:15px; font-style: italic;" for="currency">{{ __('phq9.question_1', [], get_phq9_language($case)) }}</label>

                        <div style="display:flex; flex-direction:column; align-items:flex-start;  margin-top:5px;">
                            <label style="display: flex;">
                                <input style="width: auto; margin-right:10px;" required id="q1_0" type="radio" name="q1" value="0">
                                <span style="font-size:15px; width: auto;" class="swal2-label">{{ __('phq9.answer_0', [], get_phq9_language($case)) }}</span>
                            </label>

                            <label style="display: flex;">
                                <input style="width: auto; margin-right:10px;" required id="q1_1" type="radio" name="q1" value="1">
                                <span style="font-size:15px; width: auto;" class="swal2-label">{{ __('phq9.answer_1', [], get_phq9_language($case)) }}</span>
                            </label>

                            <label style="display: flex;">
                                <input style="width: auto; margin-right:10px;" required id="q1_2" type="radio" name="q1" value="2">
                                <span style="font-size:15px; width: auto;" class="swal2-label">{{ __('phq9.answer_2', [], get_phq9_language($case)) }}</span>
                            </label>

                            <label style="display: flex;">
                                <input style="width: auto; margin-right:10px;" required id="q1_3" type="radio" name="q1" value="3">
                                <span style="font-size:15px; width: auto;" class="swal2-label">{{ __('phq9.answer_3', [], get_phq9_language($case)) }}</span>
                            </label>
                        </div>

                        <label style="float: left; text-align: initial; margin-top:15px; font-style: italic;" for="currency">{{ __('phq9.question_2', [], get_phq9_language($case)) }}</label>

                        <div style="display:flex; flex-direction:column; align-items:flex-start;  margin-top:5px;">
                            <label style="display: flex;">
                                <input style="width: auto; margin-right:10px;" required id="q2_0" type="radio" name="q2" value="0">
                                <span style="font-size:15px; width: auto;" class="swal2-label">{{ __('phq9.answer_0', [], get_phq9_language($case)) }}</span>
                            </label>

                            <label style="display: flex;">
                                <input style="width: auto; margin-right:10px;" required id="q2_1" type="radio" name="q2" value="1">
                                <span style="font-size:15px; width: auto;" class="swal2-label">{{ __('phq9.answer_1', [], get_phq9_language($case)) }}</span>
                            </label>

                            <label style="display: flex;">
                                <input style="width: auto; margin-right:10px;" required id="q2_2" type="radio" name="q2" value="2">
                                <span style="font-size:15px; width: auto;" class="swal2-label">{{ __('phq9.answer_2', [], get_phq9_language($case)) }}</span>
                            </label>

                            <label style="display: flex;">
                                <input style="width: auto; margin-right:10px;" required id="q2_3" type="radio" name="q2" value="3">
                                <span style="font-size:15px; width: auto;" class="swal2-label">{{ __('phq9.answer_3', [], get_phq9_language($case)) }}</span>
                            </label>
                        </div>

                        <label style="float: left; text-align: initial; margin-top:15px; font-style: italic;" for="currency">{{ __('phq9.question_3', [], get_phq9_language($case)) }}</label>

                        <div style="display:flex; flex-direction:column; align-items:flex-start;  margin-top:5px;">
                            <label style="display: flex;">
                                <input style="width: auto; margin-right:10px;" required id="q3_0" type="radio" name="q3" value="0">
                                <span style="font-size:15px; width: auto;" class="swal2-label">{{ __('phq9.answer_0', [], get_phq9_language($case)) }}</span>
                            </label>

                            <label style="display: flex;">
                                <input style="width: auto; margin-right:10px;" required id="q3_1" type="radio" name="q3" value="1">
                                <span style="font-size:15px; width: auto;" class="swal2-label">{{ __('phq9.answer_1', [], get_phq9_language($case)) }}</span>
                            </label>

                            <label style="display: flex;">
                                <input style="width: auto; margin-right:10px;" required id="q3_2" type="radio" name="q3" value="2">
                                <span style="font-size:15px; width: auto;" class="swal2-label">{{ __('phq9.answer_2', [], get_phq9_language($case)) }}</span>
                            </label>

                            <label style="display: flex;">
                                <input style="width: auto; margin-right:10px;" required id="q3_3" type="radio" name="q3" value="3">
                                <span style="font-size:15px; width: auto;" class="swal2-label">{{ __('phq9.answer_3', [], get_phq9_language($case)) }}</span>
                            </label>
                        </div>

                        <label style="float: left; text-align: initial; margin-top:15px; font-style: italic;" for="currency">{{ __('phq9.question_4', [], get_phq9_language($case)) }}</label>

                        <div style="display:flex; flex-direction:column; align-items:flex-start;  margin-top:5px;">
                            <label style="display: flex;">
                                <input style="width: auto; margin-right:10px;" required id="q4_0" type="radio" name="q4" value="0">
                                <span style="font-size:15px; width: auto;" class="swal2-label">{{ __('phq9.answer_0', [], get_phq9_language($case)) }}</span>
                            </label>

                            <label style="display: flex;">
                                <input style="width: auto; margin-right:10px;" required id="q4_1" type="radio" name="q4" value="1">
                                <span style="font-size:15px; width: auto;" class="swal2-label">{{ __('phq9.answer_1', [], get_phq9_language($case)) }}</span>
                            </label>

                            <label style="display: flex;">
                                <input style="width: auto; margin-right:10px;" required id="q4_2" type="radio" name="q4" value="2">
                                <span style="font-size:15px; width: auto;" class="swal2-label">{{ __('phq9.answer_2', [], get_phq9_language($case)) }}</span>
                            </label>

                            <label style="display: flex;">
                                <input style="width: auto; margin-right:10px;" required id="q4_3" type="radio" name="q4" value="3">
                                <span style="font-size:15px; width: auto;" class="swal2-label">{{ __('phq9.answer_3', [], get_phq9_language($case)) }}</span>
                            </label>
                        </div>

                        <label style="float: left; text-align: initial; margin-top:15px; font-style: italic;" for="currency">{{ __('phq9.question_5', [], get_phq9_language($case)) }}</label>

                        <div style="display:flex; flex-direction:column; align-items:flex-start;  margin-top:5px;">
                            <label style="display: flex;">
                                <input style="width: auto; margin-right:10px;" required id="q5_0" type="radio" name="q5" value="0">
                                <span style="font-size:15px; width: auto;" class="swal2-label">{{ __('phq9.answer_0', [], get_phq9_language($case)) }}</span>
                            </label>

                            <label style="display: flex;">
                                <input style="width: auto; margin-right:10px;" required id="q5_1" type="radio" name="q5" value="1">
                                <span style="font-size:15px; width: auto;" class="swal2-label">{{ __('phq9.answer_1', [], get_phq9_language($case)) }}</span>
                            </label>

                            <label style="display: flex;">
                                <input style="width: auto; margin-right:10px;" required id="q5_2" type="radio" name="q5" value="2">
                                <span style="font-size:15px; width: auto;" class="swal2-label">{{ __('phq9.answer_2', [], get_phq9_language($case)) }}</span>
                            </label>

                            <label style="display: flex;">
                                <input style="width: auto; margin-right:10px;" required id="q5_3" type="radio" name="q5" value="3">
                                <span style="font-size:15px; width: auto;" class="swal2-label">{{ __('phq9.answer_3', [], get_phq9_language($case)) }}</span>
                            </label>
                        </div>

                        <label style="float: left; text-align: initial; margin-top:15px; font-style: italic;" for="currency">{{ __('phq9.question_6', [], get_phq9_language($case)) }}</label>

                        <div style="display:flex; flex-direction:column; align-items:flex-start;  margin-top:5px;">
                            <label style="display: flex;">
                                <input style="width: auto; margin-right:10px;" required id="q6_0" type="radio" name="q6" value="0">
                                <span style="font-size:15px; width: auto;" class="swal2-label">{{ __('phq9.answer_0', [], get_phq9_language($case)) }}</span>
                            </label>

                            <label style="display: flex;">
                                <input style="width: auto; margin-right:10px;" required id="q6_1" type="radio" name="q6" value="1">
                                <span style="font-size:15px; width: auto;" class="swal2-label">{{ __('phq9.answer_1', [], get_phq9_language($case)) }}</span>
                            </label>

                            <label style="display: flex;">
                                <input style="width: auto; margin-right:10px;" required id="q6_2" type="radio" name="q6" value="2">
                                <span style="font-size:15px; width: auto;" class="swal2-label">{{ __('phq9.answer_2', [], get_phq9_language($case)) }}</span>
                            </label>

                            <label style="display: flex;">
                                <input style="width: auto; margin-right:10px;" required id="q6_3" type="radio" name="q6" value="3">
                                <span style="font-size:15px; width: auto;" class="swal2-label">{{ __('phq9.answer_3', [], get_phq9_language($case)) }}</span>
                            </label>
                        </div>

                        <label style="float: left; text-align: initial; margin-top:15px; font-style: italic;" for="currency">{{ __('phq9.question_7', [], get_phq9_language($case)) }}</label>

                        <div style="display:flex; flex-direction:column; align-items:flex-start;  margin-top:5px;">
                            <label style="display: flex;">
                                <input style="width: auto; margin-right:10px;" required id="q7_0" type="radio" name="q7" value="0">
                                <span style="font-size:15px; width: auto;" class="swal2-label">{{ __('phq9.answer_0', [], get_phq9_language($case)) }}</span>
                            </label>

                            <label style="display: flex;">
                                <input style="width: auto; margin-right:10px;" required id="q7_1" type="radio" name="q7" value="1">
                                <span style="font-size:15px; width: auto;" class="swal2-label">{{ __('phq9.answer_1', [], get_phq9_language($case)) }}</span>
                            </label>

                            <label style="display: flex;">
                                <input style="width: auto; margin-right:10px;" required id="q7_2" type="radio" name="q7" value="2">
                                <span style="font-size:15px; width: auto;" class="swal2-label">{{ __('phq9.answer_2', [], get_phq9_language($case)) }}</span>
                            </label>

                            <label style="display: flex;">
                                <input style="width: auto; margin-right:10px;" required id="q7_3" type="radio" name="q7" value="3">
                                <span style="font-size:15px; width: auto;" class="swal2-label">{{ __('phq9.answer_3', [], get_phq9_language($case)) }}</span>
                            </label>
                        </div>

                        <label style="float: left; text-align: initial; margin-top:15px; font-style: italic;" for="currency">{{ __('phq9.question_8', [], get_phq9_language($case)) }}</label>

                        <div style="display:flex; flex-direction:column; align-items:flex-start;  margin-top:5px;">
                            <label style="display: flex;">
                                <input style="width: auto; margin-right:10px;" required id="q8_0" type="radio" name="q8" value="0">
                                <span style="font-size:15px; width: auto;" class="swal2-label">{{ __('phq9.answer_0', [], get_phq9_language($case)) }}</span>
                            </label>

                            <label style="display: flex;">
                                <input style="width: auto; margin-right:10px;" required id="q8_1" type="radio" name="q8" value="1">
                                <span style="font-size:15px; width: auto;" class="swal2-label">{{ __('phq9.answer_1', [], get_phq9_language($case)) }}</span>
                            </label>

                            <label style="display: flex;">
                                <input style="width: auto; margin-right:10px;" required id="q8_2" type="radio" name="q8" value="2">
                                <span style="font-size:15px; width: auto;" class="swal2-label">{{ __('phq9.answer_2', [], get_phq9_language($case)) }}</span>
                            </label>

                            <label style="display: flex;">
                                <input style="width: auto; margin-right:10px;" required id="q8_3" type="radio" name="q8" value="3">
                                <span style="font-size:15px; width: auto;" class="swal2-label">{{ __('phq9.answer_3', [], get_phq9_language($case)) }}</span>
                            </label>
                        </div>
                    </form>
                    `,
                focusConfirm: false,
                preConfirm: () => {
                    const q0 = document.querySelector('input[name="q0"]:checked');
                    const q1 = document.querySelector('input[name="q1"]:checked');
                    const q2 = document.querySelector('input[name="q2"]:checked');
                    const q3 = document.querySelector('input[name="q3"]:checked');
                    const q4 = document.querySelector('input[name="q4"]:checked');
                    const q5 = document.querySelector('input[name="q5"]:checked');
                    const q6 = document.querySelector('input[name="q6"]:checked');
                    const q7 = document.querySelector('input[name="q7"]:checked');
                    const q8 = document.querySelector('input[name="q8"]:checked');

                    if (q0 && q1 && q2 && q3 && q4 && q5 && q6 && q7 && q8) {
                        return {
                            q0: q0.value,
                            q1: q1.value,
                            q2: q2.value,
                            q3: q3.value,
                            q4: q4.value,
                            q5: q5.value,
                            q6: q6.value,
                            q7: q7.value,
                            q8: q8.value,
                        }
                    } else {
                        Swal.showValidationMessage(
                            '{{ __('phq9.required_validation', [], get_phq9_language($case)) }}')
                    }
                }
            });

            if (formValues) {
                let sum = 0;
                for (const [key, value] of Object.entries(formValues)) {
                    sum += parseInt(value);
                }

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: 'POST',
                    url: '/ajax/set-phq9',
                    data: {
                        sum: sum,
                        type: type,
                        case_id: case_id
                    },
                    success: function(data) {
                        Swal.fire(
                            '{{ __('phq9.success') }}',
                            '',
                            'success'
                        ).then(() => {
                            location.reload();
                        });
                    },
                    error: function(error) {
                        Swal.fire(
                            '{{ __('common.error-occured') }}',
                            '',
                            'error'
                        );
                    }
                });
            }
        }

        function editConsultation() {
            $('form[name="edit-consultation"]').on('submit', function(e) {
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
                        case_id: case_id,
                        room_id: room_id,
                        eap_user_id: eap_user_id
                    },
                    success: function(data) {
                        if (data.consultation_today_exists) {
                            const error =
                                "<p class=\"error\">{{ __('common.consultation_already_exists_for_given_day') }}</p>";
                            $('form[name="edit-consultation"] input[name="consultation_date"]').after(
                                error);
                            return false;
                        }

                        if (data.custom_consultation_date_exists) {
                            const error =
                                "<p class=\"error\">{{ __('common.custom_online_appointment_already_exists') }}</p>";
                            $('form[name="add-consultation"] input[name="date"]').after(error);
                            return false;
                        }

                        $('form[name="edit-consultation"] p.error').remove();

                        $('#consultation_edit').modal('hide');
                        $(consultation_li).find('span').html(date);

                        if (!data.more_consultation_can_be_added) {
                            $('#add-consultation').hide();
                        } else {
                            $('#add-consultation').show();
                        }
                    }
                });
            });
        }

        function addConsultation() {
            $('form[name="add-consultation"]').on('submit', function(e) {
                e.preventDefault();
                const form = $(this);
                const input = form.find('input[name="date"]');
                const date_picker = input.val();
                const date_select = $("#date_select option:selected").val()
                const date = (date_picker) ? date_picker : date_select;

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
                        date: date,
                        appointment_id: appointment_id,
                        room_id: room_id,
                        eap_user_id: eap_user_id
                    },
                    success: function(data) {
                        if (data.consultation_today_exists) {
                            const error =
                                "<p class=\"error\">{{ __('common.consultation_already_exists_for_given_day') }}</p>";
                            $('form[name="add-consultation"] input[name="date"]').after(error);
                            return false;
                        }

                        if (data.custom_consultation_date_exists) {
                            const error =
                                "<p class=\"error\">{{ __('common.custom_online_appointment_already_exists') }}</p>";
                            $('form[name="add-consultation"] input[name="date"]').after(error);
                            return false;
                        }

                        $('form[name="add-consultation"] p.error').remove();

                        if (data.id === null) {
                            if (!data.more_consultation_can_be_added) {
                                $('#add-consultation').hide();
                            }
                            return false;
                        }

                        del_consultation_btn = '';

                        // IF online booking appointment, the added consultation cannot be delete by expert
                        if (!appointment_booking) {
                            del_consultation_btn =
                                '<button onClick="deleteConsultation(this,'+data.id+')">' +
                                    `<svg class="mr-1" xmlns="http://www.w3.org/2000/svg" style="height:20px; margin-bottom: 3px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>`+
                                '</button>';
                        } else { // Hide add session/consultation button after creating the next appointment
                            $('#add-consultation').hide();
                        }

                        const html =
                            `<li class="consultation">\
                                <button onClick="showEditConsultation(this,`+data.id+`, '`+data.time+`')">` +
                                    `<svg class="mr-1" xmlns="http://www.w3.org/2000/svg" style="height:20px; margin-bottom: 3px"""" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>` +
                                    ' {{ __('common.date-and-time-of-session') }} ' + parseInt(current_consultation_count+1) +
                                    '{{ __('common.date-and-time-of-session-after') }}: <span> ' + data.time +
                                    ' </span>'  +
                                `</button>` +
                                del_consultation_btn +
                            `</li>`;

                        if ($('li.consultation').length) {
                            $($('li.consultation')[$('li.consultation').length - 1]).after(html);
                        } else {
                            $('span#number_of_consultations').closest('li').after(html);
                        }

                        input.val('');
                        $('#add-consultation').removeClass('need-to-close');
                        form.closest('.modal').modal('hide');
                        consultation_count = parseInt($('#number_of_consultations').html()) + 1;
                        $('#number_of_consultations').html(consultation_count);
                        $('#cant-assing-case').remove();
                        $('#set-status-button').addClass('purple-button');
                        if (!$('#set-status-button').find('i').length) {
                            $('#set-status-button').prepend(
                                '<i class="fas fa-check-circle" style="margin-right:5px;"></i>');
                        }

                        if (!data.more_consultation_can_be_added) {
                            $('#add-consultation').hide();
                        }

                        // Check if WOS card needs to be shown
                        showHideWosCard();
                        wosReminder(consultation_count);

                        if (data.room_id) {
                            window.location.replace('{{ route('expert.cases.view', ['id' => $case->id ]) }}');
                        }
                    }
                });
            });
        }


        function setStatus() {
            var button_text = $('#set-status-button').html();
            $('#set-status-button').attr('disabled', 'disabled');
            const status = $(this).find('select[name="status"]').val();
            $('#status').modal('hide');
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: '/ajax/set-status',
                data: {
                    case_id: case_id,
                    status: 'employee_contacted'
                },
                success: function(data) {
                    $('#set-status-button').removeAttr('disabled');
                    $('#cant-assing-case').remove();
                    $('#set-status-button').html(button_text).addClass('purple-button').removeAttr('onClick');
                },
                error: function(error) {
                    $('#set-status-button').removeAttr('disabled');
                }
            });
        }

        function customerSatisfaction() {
            $('form[name="customer-satisfaction"]').on('submit', function(e) {
                e.preventDefault();
                const score = $(this).find('select[name="score"]').val();
                $('#experts').modal('hide');
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: 'POST',
                    url: '/ajax/customer-satisfaction',
                    data: {
                        score: score,
                        case_id: case_id
                    },
                    success: function(data) {
                        $('#customer_satisfaction_modal').modal('hide');
                        $('#customer_satisfaction_button #score').html(': ' + score);
                        $('#customer_satisfaction').removeClass('need-to-close');
                    }
                });
            });
        }

        function deleteConsultation(element, id, consultation_count = null, booking_id = null) {

            warning_msg = (booking_id && consultation_count == 1) ? '{{ __('common.expert_case_deletion_warning') }}' : '{{ __('common.operation-cannot-undone') }}'

            Swal.fire({
                title: '{{ __('common.are-you-sure-to-delete') }}',
                text: warning_msg,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '{{ __('common.yes-delete-it') }}',
                cancelButtonText: '{{ __('common.cancel') }}'
            }).then(function(result) {
                if (result.value) {
                    var li = $(element).closest('li');
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: 'DELETE',
                        url: '/ajax/expert-delete-consultation',
                        data: {
                            consultation_id: id,
                            booking_id: booking_id,
                            room_id: room_id,
                            eap_user_id: eap_user_id
                        },
                        success: function(data) {
                            li.remove();
                            if ($('#add-consultation').length == 0 && available_consultations > $(
                                    '.consultation').length) {
                                const text = "{{ __('common.add-session') }}";
                                $($('li.consultation')[$('li.consultation').length - 1]).after(
                                    '<li id="add-consultation"><button data-toggle="modal" data-target="#consultation"><svg xmlns="http://www.w3.org/2000/svg" class="mr-1"  style="heigth:20px; width:20px" fill="none" viewBox="0 0 24 24" stroke="currentColor">' +
                                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />' +
                                    +'</svg> ' + text + '</button></li>');
                            }

                            consultation_count = parseInt($('#number_of_consultations').html()) - 1;
                            $('#number_of_consultations').html(consultation_count);
                            showHideWosCard();
                            if (data.case_deleted == 1) {
                                window.location.replace('{{ route('expert.cases.in_progress') }}');
                            }
                        }
                    });
                }
            });
        }

        @if ($online_appointment_booking)
            function delete_last_online_consultation (case_id)
            {
                warning_msg = ({{ $case->consultations->count() }} <= 1) ? '{{ __('common.expert_case_deletion_warning') }}' : '{{ __('common.operation-cannot-undone') }}'

                Swal.fire({
                    title: '{{ __('common.are-you-sure-to-delete') }}',
                    text: warning_msg,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: '{{ __('common.yes-delete-it') }}',
                    cancelButtonText: '{{ __('common.cancel') }}'
                }).then(function(result) {
                    if (result.value) {
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            type: 'DELETE',
                            url: '/ajax/expert-delete-online-consultation',
                            data: {
                                case_id: case_id,
                                booking_id: {{$online_appointment_booking->id}},
                                eap_user_id: eap_user_id,
                                room_id: room_id
                            },
                            success: function(data) {
                                if (data.case_deleted == 1) {
                                    window.location.replace('{{ route('expert.cases.in_progress') }}');
                                } else {
                                    if (data.status == 0) {
                                        window.location.replace('{{ route('expert.cases.view', ['id' => $case->id ]) }}');
                                    }
                                }

                            }
                        });
                    }
                });
            }

            function delete_last_online_consultation_contact (case_id)
            {
                warning_msg = ({{ $case->consultations->count() }} <= 1) ? '{{ __('common.expert_case_deletion_warning') }}' : '{{ __('common.operation-cannot-undone') }}'

                Swal.fire({
                    title: '{{ __('common.expert_case_deletion_contact') }}',
                    icon: 'warning',
                    showCancelButton: false,
                });
            }

        @endif

        // Check if CGP wos card needs to be shown (Only for cases that were created after the startDate)
        const showHideWosCard = () => {
            if (caseDate.getTime() >= startDate.getTime()) {

                // Show wos survey button if there are zero answers after and there are at least 1 consultation added to the case
                // Show wos survey button if there are two or more consultation added but the number of wos answers is only one
                if (consultation_count >= 1 && wos_count <= 1) {
                    $('#wos_survey_cgp').show();
                    $('#wos_survey_cgp').removeClass('need-to-close');
                    return;
                }

                $('#wos_survey_cgp').hide();
            }
        }

        // WOS reminder after each new consultation
        const wosReminder = (count) => {
            if (caseDate.getTime() < startDate.getTime()) {
                return;
            };

            if ({{ $case->case_company_contract_holder() ?? 'null' }} == 2 &&
                {{ $case->case_type->value }} == 1) {

                if (wos_count < 2) {
                    Swal.fire({
                        text: (count == 1) ? '{{ __('popup.first_consultation_wos_warning') }}' :
                            '{{ __('popup.last_consultation_wos_warning') }}',
                        icon: 'warning',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK',
                    });
                }
            }
        }

        // IF WOS survey was not answered after the first or last consultation show warning.
        const wosWarning = () => {
            if (caseDate.getTime() < startDate.getTime()) {
                return true;
            };

            if ({{ $case->case_company_contract_holder() ?? 'null' }} == 2 &&
                {{ $case->case_type->value }} == 1) {

                if (consultation_count == 1 && wos_count < 1) {
                    Swal.fire({
                        text: (consultation_count == 1) ? '{{ __('popup.wos_first_consultation_warning') }}' :
                            '{{ __('popup.wos_case_close_warning') }}',
                        icon: 'warning',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK',
                    }).then(function(result) {
                        $('#wos_survey_cgp').addClass('need-to-close');
                    });

                    return false;
                } else {
                    return true;
                }
            }
            return true;
        }

        showHideWosCard();

        // CGP WOS survey
        function cgpWosSurvey() {

            let answer_1;
            let answer_2;
            let answer_3;
            let answer_4;
            let answer_5;
            let answer_6;

            Swal.fire({
                html: `
                <form class="swal-survey p-4 px-4">
                    <div class="text-left" style="margin-bottom:40px; color:rgb(89,198,198); font-size: 16px">
                        {{ __('popup.wos_instruction') }}
                    </div>
                    <div class="text-left text-dark">
                        {{ __('popup.wos_questions.1') }}
                    </div>
                    <div class="d-flex align-items-center" style="margin-top: 10px">
                        <div class="d-flex align-items-center" style="margin-right:10px">
                            <input class="swal2-input answer-input" type="radio" name="answers1"
                            value="1">
                        </div>
                        <div class="text-dark survey-answer">
                            {{ __('popup.wos_answers.1.1') }}
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="d-flex align-items-center" style="margin-right:10px">
                            <input class="swal2-input answer-input" type="radio" name="answers1"
                            value="2">
                        </div>
                        <div class="text-dark survey-answer">
                            {{ __('popup.wos_answers.1.2') }}
                        </div>
                    </div>
                    <div class="text-left text-dark" style="text-align: left; margin-top:30px; max-height: 100px">
                        {{ __('popup.wos_questions.2') }}
                    </div>
                    <div class="d-flex align-items-center" style="margin-top: 10px">
                        <div class="d-flex align-items-center" style="margin-right:10px">
                            <input class="swal2-input answer-input" type="radio" name="answers2"
                            value="1">
                        </div>
                        <div class="text-dark survey-answer">
                            {{ __('popup.wos_answers.2.1') }}
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="d-flex align-items-center" style="margin-right:10px">
                            <input class="swal2-input answer-input" type="radio" name="answers2"
                            value="2">
                        </div>
                        <div class="text-dark survey-answer">
                            {{ __('popup.wos_answers.2.2') }}
                        </div>
                    </div>
                    <div class="d-flex align-items-center" >
                        <div class="d-flex align-items-center" style="margin-right:10px">
                            <input class="swal2-input answer-input" type="radio" name="answers2"
                            value="3">
                        </div>
                        <div class="text-dark survey-answer">
                            {{ __('popup.wos_answers.2.3') }}
                        </div>
                    </div>
                    <div class="d-flex align-items-center" >
                        <div class="d-flex align-items-center" style="margin-right:10px">
                            <input class="swal2-input answer-input" type="radio" name="answers2"
                            value="4">
                        </div>
                        <div class="text-dark survey-answer">
                            {{ __('popup.wos_answers.2.4') }}
                        </div>
                    </div>

                    <div class="text-left text-dark" style="text-align: left; margin-top:30px">
                        {{ __('popup.wos_questions.3') }}
                    </div>
                    <div class="d-flex align-items-center" style="margin-top: 10px">
                        <div class="d-flex align-items-center" style="margin-right:10px">
                            <input class="swal2-input answer-input" type="radio" name="answers3"
                            value="1">
                        </div>
                        <div class="text-dark survey-answer">
                            {{ __('popup.wos_answers.3.1') }}
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="d-flex align-items-center" style="margin-right:10px">
                            <input class="swal2-input answer-input" type="radio" name="answers3"
                            value="2">
                        </div>
                        <div class="text-dark survey-answer">
                            {{ __('popup.wos_answers.3.2') }}
                        </div>
                    </div>
                    <div class="d-flex align-items-center" >
                        <div class="d-flex align-items-center" style="margin-right:10px">
                            <input class="swal2-input answer-input" type="radio" name="answers3"
                            value="3">
                        </div>
                        <div class="text-dark survey-answer">
                            {{ __('popup.wos_answers.3.3') }}
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="d-flex align-items-center" style="margin-right:10px">
                            <input class="swal2-input answer-input" type="radio" name="answers3"
                            value="4">
                        </div>
                        <div class="text-dark survey-answer">
                            {{ __('popup.wos_answers.3.4') }}
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="d-flex align-items-center" style="margin-right:10px">
                            <input class="swal2-input answer-input" type="radio" name="answers3"
                            value="5">
                        </div>
                        <div class="text-dark survey-answer">
                            {{ __('popup.wos_answers.3.5') }}
                        </div>
                    </div>

                    <div class="text-left text-dark" style="text-align: left; margin-top:30px">
                        {{ __('popup.wos_questions.4') }}
                    </div>
                    <div class="d-flex align-items-center" style="margin-top: 10px">
                        <div class="d-flex align-items-center" style="margin-right:10px">
                            <input class="swal2-input answer-input" type="radio" name="answers4"
                            value="1">
                        </div>
                        <div class="text-dark survey-answer">
                            {{ __('popup.wos_answers.3.1') }}
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="d-flex align-items-center" style="margin-right:10px">
                            <input class="swal2-input answer-input" type="radio" name="answers4"
                            value="2">
                        </div>
                        <div class="text-dark survey-answer">
                            {{ __('popup.wos_answers.3.2') }}
                        </div>
                    </div>
                    <div class="d-flex align-items-center" >
                        <div class="d-flex align-items-center" style="margin-right:10px">
                            <input class="swal2-input answer-input" type="radio" name="answers4"
                            value="3">
                        </div>
                        <div class="text-dark survey-answer">
                            {{ __('popup.wos_answers.3.3') }}
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="d-flex align-items-center" style="margin-right:10px">
                            <input class="swal2-input answer-input" type="radio" name="answers4"
                            value="4">
                        </div>
                        <div class="text-dark survey-answer">
                            {{ __('popup.wos_answers.3.4') }}
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="d-flex align-items-center" style="margin-right:10px">
                            <input class="swal2-input answer-input" type="radio" name="answers4"
                            value="5">
                        </div>
                        <div class="text-dark survey-answer">
                            {{ __('popup.wos_answers.3.5') }}
                        </div>
                    </div>

                    <div class="text-left text-dark" style="text-align: left; margin-top:30px">
                        {{ __('popup.wos_questions.5') }}
                    </div>
                    <div class="d-flex align-items-center" style="margin-top: 10px">
                        <div class="d-flex align-items-center" style="margin-right:10px">
                            <input class="swal2-input answer-input" type="radio" name="answers5"
                            value="1">
                        </div>
                        <div class="text-dark survey-answer">
                            {{ __('popup.wos_answers.3.1') }}
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="d-flex align-items-center" style="margin-right:10px">
                            <input class="swal2-input answer-input" type="radio" name="answers5"
                            value="2">
                        </div>
                        <div class="text-dark survey-answer">
                            {{ __('popup.wos_answers.3.2') }}
                        </div>
                    </div>
                    <div class="d-flex align-items-center" >
                        <div class="d-flex align-items-center" style="margin-right:10px">
                            <input class="swal2-input answer-input" type="radio" name="answers5"
                            value="3">
                        </div>
                        <div class="text-dark survey-answer">
                            {{ __('popup.wos_answers.3.3') }}
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="d-flex align-items-center" style="margin-right:10px">
                            <input class="swal2-input answer-input" type="radio" name="answers5"
                            value="4">
                        </div>
                        <div class="text-dark survey-answer">
                            {{ __('popup.wos_answers.3.4') }}
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="d-flex align-items-center" style="margin-right:10px">
                            <input class="swal2-input answer-input" type="radio" name="answers5"
                            value="5">
                        </div>
                        <div class="text-dark survey-answer">
                            {{ __('popup.wos_answers.3.5') }}
                        </div>
                    </div>

                    <div class="text-left text-dark" style="text-align: left; margin-top:30px">
                        {{ __('popup.wos_questions.6') }}
                    </div>
                    <div class="d-flex align-items-center" style="margin-top: 10px">
                        <div class="d-flex align-items-center" style="margin-right:10px">
                            <input class="swal2-input answer-input" type="radio" name="answers6"
                            value="1">
                        </div>
                        <div class="text-dark survey-answer">
                            {{ __('popup.wos_answers.3.1') }}
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="d-flex align-items-center" style="margin-right:10px">
                            <input class="swal2-input answer-input" type="radio" name="answers6"
                            value="2">
                        </div>
                        <div class="text-dark survey-answer">
                            {{ __('popup.wos_answers.3.2') }}
                        </div>
                    </div>
                    <div class="d-flex align-items-center" >
                        <div class="d-flex align-items-center" style="margin-right:10px">
                            <input class="swal2-input answer-input" type="radio" name="answers6"
                            value="3">
                        </div>
                        <div class="text-dark survey-answer">
                            {{ __('popup.wos_answers.3.3') }}
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="d-flex align-items-center" style="margin-right:10px">
                            <input class="swal2-input answer-input" type="radio" name="answers6"
                            value="4">
                        </div>
                        <div class="text-dark survey-answer">
                            {{ __('popup.wos_answers.3.4') }}
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="d-flex align-items-center" style="margin-right:10px">
                            <input class="swal2-input answer-input" type="radio" name="answers6"
                            value="5">
                        </div>
                        <div class="text-dark survey-answer">
                            {{ __('popup.wos_answers.3.5') }}
                        </div>
                    </div>
                </form>
                `,
                width: '800px',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK',
                showConfirmButton: true,
                showCloseButton: true,
                focusConfirm: false,
                onOpen: function() {
                    // Disable confirm button initially.
                    $(Swal.getConfirmButton()).prop('disabled', true);

                    // IF every question is answered enbale confirm button
                    $('.answer-input').on('click', function() {

                        answer_1 = Swal.getPopup().querySelector('input[name="answers1"]:checked');
                        answer_2 = Swal.getPopup().querySelector('input[name="answers2"]:checked');
                        answer_3 = Swal.getPopup().querySelector('input[name="answers3"]:checked');
                        answer_4 = Swal.getPopup().querySelector('input[name="answers4"]:checked');
                        answer_5 = Swal.getPopup().querySelector('input[name="answers5"]:checked');
                        answer_6 = Swal.getPopup().querySelector('input[name="answers6"]:checked');

                        if (answer_1 && answer_2 && answer_3 &&
                            answer_4 && answer_5 && answer_6) {
                            $(Swal.getConfirmButton()).prop('disabled', false);
                        }
                    });
                },
                preConfirm: () => {

                    if (!answer_1 || !answer_2 || !answer_3 ||
                        !answer_4 || !answer_5 || !answer_6) {
                        Swal.showValidationMessage("{{ __('popup.wos_survey_warning') }}");
                    }
                    return {
                        answer_1: answer_1.value,
                        answer_2: answer_2.value,
                        answer_3: answer_3.value,
                        answer_4: answer_4.value,
                        answer_5: answer_5.value,
                        answer_6: answer_6.value
                    }
                }
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: 'POST',
                        url: '/ajax/add-wos-to-case',
                        data: {
                            case_id: case_id,
                            answers: result.value,
                        },
                        success: function(data) {

                            if (data.max_wos_per_case) {
                                Swal.fire({
                                    text: "{{ __('popup.wos_max_survey_warning') }}",
                                    icon: 'warning',
                                    confirmButtonColor: '#3085d6',
                                    confirmButtonText: 'OK',
                                });
                            }

                            if (data.invalid_wos_save) {
                                Swal.fire({
                                    text: "{{ __('popup.invalid_wos_save') }}",
                                    icon: 'warning',
                                    confirmButtonColor: '#3085d6',
                                    confirmButtonText: 'OK',
                                });
                            }

                            if (data.more_consultation_can_be_added === false) {
                                $("#wos_survey_cgp").hide();
                            }

                            wos_count = data.count;
                            showHideWosCard();
                        }
                    });
                }
            });
        }

        function case_input_change_form() {
            $('form.case_input_change').on('submit', function(e) {
                const form = $(this);
                e.preventDefault();
                const input_id = $(this).find('input[name="input_id"]').val();

                var value = $(this).find('select[name="value"]').length != 0 ? $(this).find('select[name="value"]')
                    .val() : $(this).find('input[name="value"]').val();


                if (value == null) {
                    var value = $(this).find('textarea[name="value"]').val();
                }

                const text = $(this).find('select[name="value"]').length != 0 ? $(this).find(
                    'select[name="value"] option:selected').html() : value;

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: 'POST',
                    url: '/ajax/expert/assing-new-value-to-case-input',
                    data: {
                        input_id: input_id,
                        value: value,
                        case_id: case_id
                    },
                    success: function(data) {
                        if (data.status == 0) {
                            form.closest('.modal').modal('hide');
                            $('#case_input_' + input_id + '_value_holder').html(text);
                            $('#case_input_' + input_id + '_field').removeClass('need-to-close');
                        }
                    },
                    error: function() {
                        Swal.fire(
                            '{{ __('common.error-occured') }}',
                            '',
                            'error'
                        );
                    }
                });
            });
        }

        // ONLINE booking chat Warning message and connection
        @if ($online_appointment_booking)
            function warningBeforeVideoTherapy() {
                Swal.fire({
                    title: '{{ __('common.system-message') }}!',
                    text: '{{ ($online_appointment_booking->consultation_type == 83) ? __('eap-online.video_therapy.expert-waring') :  __('eap-online.chat_therapy.expert-waring')}}',
                    imageUrl: '/assets/img/info.png',
                    imageHeight: 78,
                    confirmButtonText: '{{ ($online_appointment_booking->consultation_type == 83) ? __('eap-online.video_therapy.join_therapy') : __('eap-online.chat_therapy.join_therapy') }}',
                }).then(function() {
                    @if ($online_appointment_booking->consultation_type == 83) // video chat
                        window.open(
                            '{{ route('admin.eap-online.video_chat', ['client_id' => $online_appointment_booking->user_id, 'room_id' => $online_appointment_booking->room_id]) }}',
                            'name', 'width=1000,height=800');
                    @else
                        window.open(
                                '{{ route('admin.eap-online.online_chat', ['client_id' => $online_appointment_booking->user_id, 'room_id' => $online_appointment_booking->room_id]) }}',
                                'name', 'width=1000,height=800');
                    @endif
                });
            }
        @endif

        // INTAKE booking chat Warning message and connection
        @if ($intake_online_booking && $intake_online_booking->room_id)
            function warningBeforeVideoTherapy() {
                Swal.fire({
                    title: '{{ __('common.system-message') }}!',
                    text: '{{ ($consultation_type == 83) ? __('eap-online.video_therapy.expert-waring') :  __('eap-online.chat_therapy.expert-waring')}}',
                    imageUrl: '/assets/img/info.png',
                    imageHeight: 78,
                    confirmButtonText: '{{ ($consultation_type == 83) ? __('eap-online.video_therapy.join_therapy') : __('eap-online.chat_therapy.join_therapy') }}',
                }).then(function() {
                    @if ($consultation_type == 83) // video chat
                        window.open(
                            '{{ route('admin.eap-online.video_chat', ['client_id' => $intake_online_booking->user_id, 'room_id' => $intake_online_booking->room_id]) }}',
                            'name', 'width=1000,height=800');
                    @else
                        window.open(
                                '{{ route('admin.eap-online.online_chat', ['client_id' => $intake_online_booking->user_id, 'room_id' => $intake_online_booking->room_id]) }}',
                                'name', 'width=1000,height=800');
                    @endif
                });
            }
        @endif

        $('#date_select').on('change', function() {
            appointment_id = event.target.options[event.target.selectedIndex].dataset.appointment;
        });

        document.addEventListener("DOMContentLoaded", function () {
            show_online_consultation_infromation();
        });

        function show_online_consultation_infromation ()
        {
            @if ($online_appointment_booking && ($consultation_type == 83 || $consultation_type == 82))
                var text = {!! json_encode(($online_appointment_booking->consultation_type == 83) ? __('eap-online.online_video_consultation_information') :  __('eap-online.online_chat_consultation_information')) !!};
                Swal.fire({
                    title: '{{ __('common.system-message') }}!',
                    html: text,
                    imageUrl: '/assets/img/info.png',
                    imageHeight: 78,
                    confirmButtonText: '{{  __('eap-online.consultation_information_confirmation')  }}',
                    width: '850px',
                });
            @endif

            @if ($intake_online_booking && $consultation_type == 82)
                var text = {!! json_encode(__('eap-online.intake_chat_consultation_information')) !!};
                Swal.fire({
                    title: '{{ __('common.system-message') }}!',
                    html: text,
                    imageUrl: '/assets/img/info.png',
                    imageHeight: 78,
                    confirmButtonText: '{{  __('eap-online.consultation_information_confirmation')  }}',
                    width: '850px',
                });
            @endif
        }

    </script>
@endsection

@section('extra_css')
    <link rel="stylesheet" href="/assets/css/cases/view.css?t={{ time() }}">
    <link rel="stylesheet" href="/assets/css/cases/datetime.css?t={{ time() }}">
    <link rel="stylesheet" href="/assets/css/cases/swal_survey.css?t={{ time() }}">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.min.css"
        integrity="sha512-f0tzWhCwVFS3WeYaofoLWkTP62ObhewQ1EZn65oSYDZUg1+CyywGKkWzm8BxaJj5HGKI72PnMH9jYyIFz+GH7g=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="{{ asset('assets/css/cases/pulse.css') }}">
    <link rel="stylesheet" href="{{asset('assets/css/datetimepicker.css')}}">
    <style>
        .purple-button,
        button.closeable {
            background-color: #7c2469 !important;
        }

        .case-details ul li.need-to-close {
            box-shadow: inset 0px 0px 0px 3px #f00;
        }
    </style>
@endsection

@php
    $hidden_inputs = [];

    if ($case->case_company_contract_holder() == 1 && $case->case_type->value == 1) {
        $hidden_inputs = [5, 6, 8, 9, 12, 13, 36, 28, 64, 96, 97];
    } else {
        $hidden_inputs = [2, 5, 6, 8, 9, 12, 13, 36, 28, 64, 96, 97];
    }

    // if the case is not psychological(1), coaching(11), health coaching(7) or Well Being Coaching(16) hide the consultation numbers field
    if (!in_array((int) optional($case->values->where('case_input_id', 7)->first())->value, [1,11,7,16])) {
        array_push($hidden_inputs, 21);
    }
@endphp

@section('content')
    <div class="row">
        <div class="col-12">
            <h1>{{ __('common.case-view') }}</h1>
        </div>
        <div class="col-12 case-title">
            <p>{{ $case->values->where('case_input_id', 1)->first()->value }}
                - {{ \Auth::user()->name }}
                - {{ $case->case_type != null ? $case->case_type->getValue() : null }}
                - {{ $case->case_client_name != null ? $case->case_client_name->getValue() : null }}</p>
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

                <li>{{ __('common.identifier') }}: {{ $case->case_identifier }}</li>
                @if ($case->case_company_contract_holder() === 1)
                    <li>Org id : {{ $case->orgId() }}</li>
                    <li>Vendor id : {{ config('morneau-shepell-vendor-id.' . $case->country_id) }}</li>
                @endif
                @foreach ($case->values as $value)
                    @if (
                            !in_array($value->case_input_id, $hidden_inputs) &&
                            empty($value->input->company_id) &&
                            $value->input &&
                            $value->showAbleAfter3Months()
                        )

                        @if ($case->case_type->value != 1 && $value->input->default_type == 'case_specialization')
                            <!-- Skip specialization if case type is not psychological -->
                        @else
                            @if ($value->input->default_type == 'case_creation_time')
                                <li id="case_input_{{ $value->case_input_id }}_field">
                                    @if (empty($value->getValue()))
                                        <button data-toggle="modal" data-target="#case_input_{{ $value->input->id }}"><i
                                                class="fas fa-edit"></i>
                                            {{ optional($value->input->translation)->value }}: <span
                                                id="case_input_{{ $value->input->id }}_value_holder">
                                                {{ $value->getValue() }}</span>
                                        </button>
                                    @else
                                        {{ $value->input->translation ? optional($value->input->translation)->value : null }}
                                        : {{ $value->getValue() }}
                                    @endif
                                </li>
                            @elseif ($value->input->default_type == 'case_specialization' ||$value->input->default_type == 'case_language_skill')
                                <li id="case_input_{{ $value->case_input_id }}_field">
                                    @if (empty($value->getValue()))
                                        <button data-toggle="modal" data-target="#case_input_{{ $value->input->id }}">
                                            {{ optional($value->input->translation)->value }}: <span
                                                id="case_input_{{ $value->input->id }}_value_holder">{{ $value->getValue() }}</span>
                                        </button>
                                    @else
                                        {{ $value->input->translation ? optional($value->input->translation)->value : null }}
                                        : {{ $value->getValue() }}
                                    @endif
                                </li>
                            @elseif ($value->input->default_type == 'clients_language')
                                <li id="case_input_{{ $value->case_input_id }}_field">
                                    @if (empty($value->getValue()))
                                        <button data-toggle="modal" data-target="#case_input_{{ $value->input->id }}">
                                            <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                                                style="height:20px; margin-bottom: 3px" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            {{ optional($value->input->translation)->value }}: <span
                                                id="case_input_{{ $value->input->id }}_value_holder">{{ $value->getValue() }}</span>
                                        </button>
                                    @else
                                        {{ $value->input->translation ? optional($value->input->translation)->value : null }}
                                        : {{optional($language_skills->where('id', $value->value)->first()->translation)->value}}
                                    @endif
                                </li>
                            @else
                                <li id="case_input_{{ $value->case_input_id }}_field">
                                    @if (empty($value->getValue()))
                                        <button data-toggle="modal" data-target="#case_input_{{ $value->input->id }}">
                                            <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                                                style="height:20px; margin-bottom: 3px" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            {{ optional($value->input->translation)->value }}: <span
                                                id="case_input_{{ $value->input->id }}_value_holder">{{ $value->getValue() }}</span>
                                        </button>
                                    @else
                                        {{ $value->input->translation ? optional($value->input->translation)->value : null }}
                                        : {{ $value->getValue() }}
                                    @endif
                                </li>
                            @endif
                        @endif
                    @endif
                @endforeach
                <li>{{ __('common.expert-outsourced') }}: {{ \Auth::user()->name }}</li>


                <li>{{ __('common.number-of-sessions') }}: <span
                        id="number_of_consultations">{{ $case->consultations->count() }}</span></li>

                @php
                    $used = $case->permissionCount - $case->consultations->count();
                    $deleteUntil = $case->permissionCount - $used;
                @endphp
                @foreach (($online_appointment_booking) ? $case->consultations()->withTrashed()->get()->sortBy('id') : $case->consultations()->get()->sortBy('created_at') as $consultation)
                    @php $date = \Carbon\Carbon::parse($consultation->created_at)->format('Y-m-d H:i') @endphp
                    <li class="consultation
                        @if ($consultation->deleted_at) consultation--deleted @endif">
                        @if ($online_appointment_booking)
                            {{ __('common.date-and-time-of-session') }} {{$loop->index + 1}}. :
                            <span>{{ $date }}</span>
                        @else
                            <button
                                @if ($consultation->expert && Auth::user()->id == $consultation->expert->id) onClick="showEditConsultation(this,{{ $consultation->id }},'{{ $date }}')" @endif
                                @if ($case->status == 'Lezárt') disabled style="color: black" @endif>
                                @if ($consultation->expert && Auth::user()->id == $consultation->expert->id)
                                    @if ($case->status != 'Lezárt')
                                        <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                                            style="height:20px; margin-bottom: 3px" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    @endif
                                @endif
                                {{ __('common.date-and-time-of-session') }}
                                {{ $loop->index + 1 }}{{ __('common.date-and-time-of-session-after') }}
                                :
                                <span>{{ $date }}</span>
                                @if ($consultation->expert && Auth::user()->id != $consultation->expert->id)
                                    - {{ $consultation->expert->name }}
                                @endif
                            </button>
                            @if ($consultation->expert && Auth::user()->id == $consultation->expert->id)
                                <button onClick="deleteConsultation(this,{{ $consultation->id }})"
                                    @if ($case->status == 'Lezárt') disabled
                                        style="color: #000000" @endif>
                                    @if ($case->status != 'Lezárt')
                                        <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                                            style="height:20px; margin-bottom: 3px" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    @endif
                                </button>
                            @endif
                        @endif
                    </li>
                @endforeach

                <!-- WOS survey for CGP contractor -->
                @if ($case->case_company_contract_holder() == 2 &&
                        $case->case_type->value == 1)
                    <li id="wos_survey_cgp" onclick="cgpWosSurvey()">
                        <a target="_blank" href="#" onclick="event.preventDefault()"
                            class="button float-right d-flex align-items-center mr-2">
                            <svg class="mr-1" xmlns="http://www.w3.org/2000/svg" style="height:20px; margin-bottom: 3px"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                            {{ __('common.wos_survey') }}
                        </a>
                    </li>
                @endif
                <div>
                </div>
                {{-- IF the case is not closed and the last consultation date is in the past --}}
                @if ($case->status != 'Lezárt'
                &&  $case->can_add_more_consultation( ($online_appointment_booking) ? true : false ) || !$case->consultations()->exists() )
                    {{-- IF the case legal(2) or financial(3) and has one or more consultation only show popup. Do not allow creating another consultation --}}
                    @if($case->consultations()->count() >=1 && in_array($case->case_type->value, [2,3]))
                        <li>
                            <button onclick="after_first_consultation_popup()">
                                <svg xmlns="http://www.w3.org/2000/svg" style="height: 20px; margin-bottom: 3px"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ __('common.add-session') }}
                            </button>
                        </li>
                    @elseif ($case->has_more_consultations( ($online_appointment_booking) ? true : false ) && $case->can_add_more_consultation(($online_appointment_booking) ? true : false))
                        <li id="add-consultation">
                            <button
                                @if ($case->status == 'Lezárt') disabled
                                    style="color: black" @endif>
                                @if ($case->status == 'Lezárt')
                                @endif
                                <svg xmlns="http://www.w3.org/2000/svg" style="height: 20px; margin-bottom: 3px"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ __('common.add-session') }}
                            </button>
                        </li>
                    @endif
                @endif


                @if ($case->company_id != 843)
                    <li id="customer_satisfaction">
                        <button data-toggle="modal" data-target="#customer_satisfaction_modal"
                            id="customer_satisfaction_button"
                            @if ($case->status == 'Lezárt') disabled
                                style="color: black" @endif>
                            @if ($case->status != 'Lezárt')
                                <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                                    style="height:20px; margin-bottom: 3px" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            @endif {{ __('common.satisfaction-level') }}<span id="score">:
                                {{ $case->customer_satisfaction }}</span>
                        </button>
                    </li>
                @endif

                @if ($case->case_company_contract_holder() == 4 &&
                        $case->case_type->value == 1 &&
                        in_array(intval($case->country_id), [3, 7, 6]))
                    <li id="phq9_opening"
                        @if (empty($case->phq9_opening)) onclick="phq9_clicked('opening')" style="cursor: pointer;" @endif>
                        {{ __('phq9.phq9_opening') }}: <span
                            id="phq9_opening_score">{{ optional($case)->phq9_opening }}</span>
                    </li>

                    <li id="phq9_closing"
                        @if (empty($case->phq9_closing)) onclick="phq9_clicked('closing')" style="cursor: pointer;" @endif>
                        {{ __('phq9.phq9_closing') }}: <span
                            id="phq9_closing_score">{{ optional($case)->phq9_closing }}</span>
                    </li>
                @endif

                @if ($case->case_company_contract_holder() == 1 &&
                        $case->case_type->value == 1)
                    <li id="wos_survey" onclick="wos_survey_clicked()">
                        <a target="_blank" href=" https://morneaushepell.ca1.qualtrics.com/jfe/form/SV_dgp8UXqG0KQ0oFn"
                            class="button float-right d-flex align-items-center mr-2">
                            <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                                style="height:20px; margin-bottom: 3px" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                            {{ __('common.wos_survey') }}
                        </a>
                    </li>
                @endif

                @if ($case->case_company_contract_holder() == 1 &&
                        $case->company->id == 227 &&
                        $case->case_type->value == 1)
                    <li onclick="showNestleQuestionnaire()">
                        <a href="#" class="button float-right d-flex align-items-center mr-2">
                            <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                                style="height:20px; margin-bottom: 3px" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                            Questionnaire
                        </a>
                    </li>
                @endif
            </ul>
        </div>
        <div class="col-12 button-holder">
            <a href="#" class="button d-none">{{ __('common.save') }}</a>
            <a href="#" class="button d-none">{{ __('common.edit') }}</a>
            @if (!$online_therapy_appointments && $case->getRawOriginal('status') == 'assigned_to_expert')
                <a class="button btn-radius float-right d-flex align-items-center @if (sizeof($case->isCaseNotAccepted) > 0) active @endif"
                    id="cant-assing-case" onClick="cantAssignCase()" >
                    <svg class="mr-1" xmlns="http://www.w3.org/2000/svg" style="height:20px; margin-bottom: 3px"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    {{ __('common.i-will-not-take-the-case') }}
                </a>
            @endif
            @if ($online_appointment_booking)
                <button class="button btn-radius float-right d-flex align-items-center" href="" rel="noopener noreferrer"
                    target="popup"  onclick="warningBeforeVideoTherapy()" style="background-color: rgb(255 171 1)">
                    <svg class="mr-1" xmlns="http://www.w3.org/2000/svg" style="height:20px; margin-bottom: 3px"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                    {{-- if video chat --}}
                    @if ($online_appointment_booking->consultation_type == 83)
                        {{ __('eap-online.video_therapy.join_therapy') }}
                    @else
                        {{ __('eap-online.chat_therapy.join_therapy') }}
                    @endif

                </button>
                <button class="button btn-radius float-right d-flex align-items-center consultation--deleted" onClick="delete_last_online_consultation_contact({{$online_appointment_booking->case_id}})"
                    @if (Carbon\Carbon::now()->gte(optional($case->consultations()->orderByDesc('id')->first())->created_at))
                        disabled
                        style="opacity: 0.33;"
                    @endif
                    @if ($case->status == 'Lezárt')
                        disabled
                        style="opacity: 0.33;"
                    @endif
                    >
                    @if ($case->status != 'Lezárt')
                        <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                            style="height:20px; margin-bottom: 3px" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        <span>
                            {{__('common.delete_next_online_consultation')}}
                        </span>
                    @endif
                </button>
            @endif
            @if ($intake_online_booking && $intake_online_booking->room_id)
                <button class="button btn-radius float-right d-flex align-items-center" href="" rel="noopener noreferrer"
                    target="popup"  onclick="warningBeforeVideoTherapy()"  style="background-color: rgb(255 171 1)">
                    <svg class="mr-1" xmlns="http://www.w3.org/2000/svg" style="height:20px; margin-bottom: 3px"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                    {{-- if video chat --}}
                    @if ($consultation_type == 83)
                        {{ __('eap-online.video_therapy.join_therapy') }}
                    @else
                        {{ __('eap-online.chat_therapy.join_therapy') }}
                    @endif

                </button>
            @endif
            @if (!in_array($case->getRawOriginal('status'), ['client_unreachable', 'confirmed']))
                <button class="button btn-radius float-right d-flex align-items-center" id="client-unreachable"
                    onClick="clientUnreachable({{ $case->id }}, this)"
                    >
                    <svg class="mr-1" xmlns="http://www.w3.org/2000/svg" style="height:20px; margin-bottom: 3px"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                    </svg>
                    {{ __('common.client-unavailable') }}
                </button>
            @elseif($case->getRawOriginal('status') == 'client_unavailable')
                <button class="button btn-radius float-right purple-button d-flex align-items-center"
                    >
                    <svg class="mr-1" xmlns="http://www.w3.org/2000/svg" style="height:20px; margin-bottom: 3px"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                    </svg>
                    {{ __('common.client-unavailable') }}
                </button>
            @endif
            @if ($case->getRawOriginal('status') == 'assigned_to_expert')
                <button onClick="setStatus()" id="set-status-button" class="button btn-radius float-right d-flex align-items-center"
                    >
                    <svg class="mr-1" xmlns="http://www.w3.org/2000/svg" style="height:20px; margin-bottom: 3px"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    {{ __('common.client-contacted') }}
                </button>
            @elseif($case->getRawOriginal('status') == 'employee_contacted')
                <a class="button btn-radius float-right purple-button d-flex align-items-center"
                    >
                    <svg class="mr-1" xmlns="http://www.w3.org/2000/svg" style="height:20px; margin-bottom: 3px;"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    {{ __('common.client-contacted') }}
                </a>
            @endif
        </div>
        <div class="col-12 button-holder">
            @if (empty($case->confirmed_by))
                <button
                    class="{{ $case->isCloseable()['closeable'] ? 'pulse-container' : '' }} button btn-radius float-right d-flex align-items-center mr-0"
                    id="closeCaseButton" onClick="closeCase(this, {{ $case->id }});"
                    >
                    <svg class="mr-1" xmlns="http://www.w3.org/2000/svg" style="height:20px; margin-bottom: 3px"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                    {{ __('common.close-case') }}
                </button>
            @endif
            <button class="button btn-radius float-right d-flex align-items-center" data-toggle="modal"
                data-target="#mail-to-operator" >
                <svg class="mr-1" xmlns="http://www.w3.org/2000/svg" style="height:20px; margin-bottom: 3px"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                {{ __('common.send-mail-to-operator') }}
            </button>
            @if ($case->getRawOriginal('status') == 'interrupted')
                <button class="button float-right  closeable"
                    >{{ __('common.interrupted') }}!
                </button>
            @else
                <button class="button btn-radius float-right d-flex align-items-center"
                    onClick="caseInterrupted(this, {{ $case->id }});"
                    >
                    <svg class="mr-1" xmlns="http://www.w3.org/2000/svg" style="height:20px; margin-bottom: 3px"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ __('common.interrupted') }}
                </button>
            @endif
        </div>
        <div class="col-12 back-button mb-5">
            @if ($case->getRawOriginal('status') != 'confirmed' && $case->getRawOriginal('status') != 'client_unreachable_confirmed')
                <a href="{{ route('expert.cases.in_progress') }}">{{ __('common.back-to-list') }}</a>
            @endif
        </div>
    </div>
@endsection

@section('modal')
    @foreach ($case->values as $value)
        @if ($value->input)
            @if (
                $value->input->default_type != 'company_chooser' &&
                    $value->input->default_type != 'company_chooser' &&
                    $value->input->default_type != 'location' &&
                    $value->input->default_type != 'case_type' &&
                    $value->input->default_type != 'case_language_skill' &&
                    $value->input->default_type != 'case_specialization')
                <div class="modal" tabindex="-1" id="case_input_{{ $value->input->id }}" role="dialog">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">{{ optional($value->input->translation)->value }}</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form method="post" class="case_input_change">
                                    <input type="hidden" value="{{ $value->input->id }}" name="input_id">
                                    {{ csrf_field() }}
                                    @if ($value->input->type == 'select')
                                        <select class="w-100" name="value">
                                            @foreach ($value->input->values->where('visible', 1) as $v)
                                                <option value="{{ $v->id }}"
                                                    @if ($v->id == $value->value) selected @endif>
                                                    {{ optional($v->translation)->value }}</option>
                                            @endforeach
                                        </select>
                                    @elseif($value->input->type == 'date')
                                        <input type="text" name="value" class="datepicker"
                                            value="{{ $value->value }}"
                                            placeholder="{{ optional($value->input->translation)->value }}" />
                                    @elseif($value->input->type == 'integer')
                                        <input type="number" name="value" value="{{ $value->value }}"
                                            placeholder="{{ optional($value->input->translation)->value }}" />
                                    @elseif($value->input->type == 'double')
                                        <input type="text" name="value" value="{{ $value->value }}"
                                            placeholder="{{ optional($value->input->translation)->value }}" />
                                    @elseif($value->input->type == 'text')
                                        <textarea name="value" cols="30" rows="10">{{ $value->value }}</textarea>
                                        {{--                    <input type="text" name="value" value="{{$value->value}}" placeholder="{{optional($value->input->translation)->value}}"/> --}}
                                    @endif
                                    <button class="btn-radius float-right" style="--btn-margin-right:0px;">
                                        <img class="mr-1" src="{{asset('assets/img/save.svg')}}" class="mr-1" style="height: 20px; width: 20px" alt="">
                                        <span class="mt-1">{{__('common.save')}}</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif($value->input->default_type == 'location')
                <div class="modal" tabindex="-1" id="case_input_{{ $value->input->id }}" role="dialog">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">{{ optional($value->input->translation)->value }}</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form method="post" class="case_input_change">
                                    <input type="hidden" value="{{ $value->input->id }}" name="input_id">
                                    {{ csrf_field() }}
                                    <select class="w-100" name="value">
                                        @foreach ($case->country->cities->sortBy('name') as $city)
                                            <option value="{{ $city->id }}"
                                                @if ($city->id == $value->value) selected @endif>{{ $city->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button class="btn-radius float-right" style="--btn-margin-right:0px;">
                                        <img class="mr-1" src="{{asset('assets/img/save.svg')}}" class="mr-1" style="height: 20px; width: 20px" alt="">
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

    <div class="modal" tabindex="-1" id="customer_satisfaction_modal" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('common.satisfaction-level') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post" name="customer-satisfaction">
                        {{ csrf_field() }}
                        <select class="w-100" name="score">
                            @for ($i = 1; $i <= 10; $i++)
                                <option value="{{ $i }}" @if ($i == $case->customer_satisfaction) selected @endif>
                                    {{ $i }}</option>
                            @endfor
                        </select>
                        <button class="btn-radius float-right" style="--btn-margin-right:0px;">
                            <img class="mr-1" src="{{asset('assets/img/save.svg')}}" class="mr-1" style="height: 20px; width: 20px" alt="">
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
                    <h5 class="modal-title">{{ __('common.expert-outsourcing') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post" name="assign-expert">
                        {{ csrf_field() }}
                        <select class="w-100" name="experts">
                            @foreach ($case->getAvailableExperts() as $expert)
                                <option value="{{ $expert->id }}">{{ $expert->name }}</option>
                            @endforeach
                        </select>
                        <button class="btn-radius float-right" style="--btn-margin-right:0px;">
                            <img class="mr-1" src="{{asset('assets/img/save.svg')}}" class="mr-1" style="height: 20px; width: 20px" alt="">
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
                    <h5 class="modal-title">{{ __('common.add-session') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post" name="add-consultation">
                        {{ csrf_field() }}
                        @if ($online_appointment_booking)
                            <select class="w-100" name="date_select" id="date_select">
                                <option value="">{{__('common.timetable-date')}}</option>
                                @foreach ($online_therapy_appointments as $appointment)
                                    <option data-appointment="{{$appointment['appointment_id']}}" value="{{Illuminate\Support\Str::replace('.', '-', $appointment['date'].' '.$appointment['from'])}}">
                                        <span>{{$appointment['date']}}</span>
                                        <span>{{$appointment['from']}}</span>
                                    </option>
                                @endforeach
                            </select>

                            <p class="pl-1 my-3">{{__('common.or')}}</p>

                            <input type="text" name="date" placeholder="{{ __('common.custom-date') }}" class="datepicker w-100" style="border-radius: 0px;">
                        @else
                            <input type="text" name="date" placeholder="{{ __('common.date-of-session') }}"
                                class="datepicker w-100"  style="border-radius: 0px;">
                        @endif
                        <button class="btn-radius float-right" style="--btn-margin-right:0px;">
                            <img class="mr-1" src="{{asset('assets/img/save.svg')}}" class="mr-1" style="height: 20px; width: 20px" alt="">
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
                    <h5 class="modal-title">{{ __('common.edit-session') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post" name="edit-consultation">
                        {{ csrf_field() }}
                        <input type="hidden" name="consultation_id" value="">
                        <input type="text" name="consultation_date" placeholder="{{ __('common.date-of-session') }}"
                            class="w-100 datepicker">
                        <button class="btn-radius float-right" style="--btn-margin-right:0px;">
                            <img class="mr-1" src="{{asset('assets/img/save.svg')}}" class="mr-1" style="height: 20px; width: 20px" alt="">
                            <span class="mt-1">{{__('common.save')}}</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" tabindex="-1" id="mail-to-operator" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('common.send-mail-to-operator') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post" name="mail-to-operator">
                        {{ csrf_field() }}
                        {{--          <input type="text" name="question" required placeholder="{{__('common.write-here-email-text')}}"/> --}}
                        <textarea name="question" cols="30" rows="10">{{ __('common.write-here-email-text') }}</textarea>
                        <button class="btn-radius">{{ __('common.send') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
