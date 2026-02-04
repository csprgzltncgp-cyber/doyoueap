@extends('layout.master')

@section('title')
Operator Dashboard
@endsection

@section('extra_css')
<link rel="stylesheet" href="/assets/css/cases/new.css?t={{ time() }}">
<link rel="stylesheet" href="/assets/css/cases/datetime.css?t={{ time() }}">
<style>
    @media only screen and (max-width: 1000px) {
        .mobile {
            margin-top: 150px !important
        }
    }
</style>
@endsection

@section('extra_js')
<script src="/assets/js/datetime.js" charset="utf-8"></script>
<script>
    var company_id, width, contract_holder_id;
        var form_submitted = 0;
        var city_case_input_id = {{ $case_inputs->firstWhere('default_type', 'location')->id }};
        $(function() {
            width = $('.select-button').outerWidth() != 0 ? $('.select-button').outerWidth() : $(
                '.new-case-buttons .steps:first-child input').outerWidth();
            $('.select-list').css('width', width);
            if (window.innerWidth >= 922) {
                $('.select-list').css('margin-left', $('.back-button').outerWidth() + 4);
            }
            editCurrentStepNumber();
            selectFromList();
            formSubmit();
            companyChangeHandler();
            permissionChangeHandler();
            languageSkillChangeHandler();
            specializationChangeHandler();
            crisisChangeHandler();
            consultationMinuteChangeHandler();
            clientLanguageChangeHandler();
            consultationTypeChangeHandler();
            problem_details_change_handler();

            const currentDate = new Date().toISOString().split('T')[0];

            $('.datepicker').datepicker({
                'format': 'yyyy-mm-dd',
                'startDate': currentDate,
                'endDate': currentDate,
            });

            // Prevent space in phone number
            $('input[name="inputs[17]"]').on('keypress', function(e) {
                if (e.which == 32){
                    return false;
                }
            });
        });

        function formSubmit() {
            $('form[name="case-create"]').on('submit', function(e) {
                if (form_submitted == 0) {
                    e.preventDefault();
                    form_submitted = 1;
                    $(this).submit();
                }
            });
        }

        let company_permissions;
        let application_checked = false;
        let application_code = null;

        function companyChangeHandler() {
            $('input#company_chooser').on('change', function() {
                $('#permissions .permission').remove();
                $('#permissions #loading').remove();
                $('#permissions').append('<p id="loading">Betöltés...</p>');
                company_id = $(this).val();
                let country_id = @js(auth()->user()->country_id);
                application_checked = false;

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: 'POST',
                    url: '/ajax/get-contract-holder-by-company',
                    data: {
                        company_id: company_id,
                        country_id: country_id,
                    },
                    success: function(data) {
                        if (data.status == 0) {
                            contract_holder_id = data.contract_holder_id;

                            $('li').each(function() {
                                if ($(this).data('contract-holder-id') && $(this).data(
                                        'contract-holder-id') != contract_holder_id) {
                                    $(this).addClass('d-none');
                                } else {
                                    $(this).removeClass('d-none');
                                }
                            });
                        }
                    }
                });

                //le kell kérni a cég inputjait és jogosultságaits
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: 'POST',
                    url: '/ajax/get-company-permissions-and-steps',
                    data: {
                        id: company_id,
                    },
                    success: function(data) {
                        if (data.status == 0) {
                            company_permissions = data.permissions
                            $.each(company_permissions, function(index, value) {
                                const html =
                                    '<div class="permission">\
                                        <p>' +
                                            value
                                            .name +
                                        '</p>\
                                        <p>' +
                                            value
                                            .pivot
                                            .contact +
                                        '</p>\
                                        <p>' +
                                            value
                                            .pivot
                                            .number +
                                            ' {{ __('common.occasion') }} / ' +
                                            value
                                            .pivot
                                            .duration +
                                        ' {{ __('common.minute') }}</p>\
                                    </div>';
                                $('#permissions').append(html);
                            });

                            eraseCompanyDependentInputs();

                            //step-ek hozzáadása
                            $.each(data.steps, function(index, value) {
                                var key = $('.new-case-buttons .steps:last-child()').data(
                                    'step');
                                key += 1;

                                let html = '<div class="col-12 steps d-none" style="height: 64px!important" data-step = "' + key +
                                    '" data-type="' + value.type +
                                    '">\
                                    <button type="button" name="button" class="back-button btn-radius col-12 col-lg-2 mb-1 mt-1 mb-lg-0 mt-lg-0" style="--btn-height: 100%; --btn-margin-bottom: 0px; min-width: auto!important; margin-right: 0px!important;"  onClick="backButton(this)">\
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 40px;">\
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m18.75 4.5-7.5 7.5 7.5 7.5m-6-15L5.25 12l7.5 7.5" />\
                                        </svg>\
                                    </button>';

                                if (value.type == 'select') {
                                    html +=
                                        '<button type="button" name="button" class="select-button col-lg-8 h-100" style="margin-left:4px" data-type="' +
                                        value.type + '" onClick=\'actionButton(this)\'>' + value
                                        .name +
                                        '</button>\
                                        <input type="hidden" name="inputs[' +
                                        value
                                        .id +
                                        ']" >';
                                } else if (value.type == 'date') {
                                    html += '<input type="text" name="inputs[' + value.id +
                                        ']" class="datepicker" placeholder="' + value.name +
                                        '"/>';
                                } else if (value.type == 'integer') {
                                    html += '<input type="number" name="inputs[' + value.id +
                                        ']" placeholder="' + value.name + '"/>';
                                } else if (value.type == 'double') {
                                    html += '<input type="text" name="inputs[' + value.id +
                                        ']" placeholder="' + value.name + '"/>';
                                } else if (value.type == 'text') {
                                    html += '<input type="text" name="inputs[' + value.id +
                                        ']" placeholder="' + value.name + '"/>';
                                }

                                html +=
                                    '<button type="button" name="button" class="next-button btn-radius col-12 col-lg-2 mb-1 mt-1 mb-lg-0 mt-lg-0" style="--btn-height: 100%; --btn-margin-bottom: 0px; min-width: auto!important;" onClick="nextButton(this)" data-defaulttype="' +
                                        value.default_type +
                                        '">\
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:40px">\
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m5.25 4.5 7.5 7.5-7.5 7.5m6-15 7.5 7.5-7.5 7.5" />\
                                        </svg> \
                                    </button>';
                                if (value.type == 'select') {
                                    html += '<ul class="select-list">';
                                    if (value.values) {
                                        $.each(value.values, function(i, v) {
                                            if (v['translation'] && v['translation'][
                                                    'value'
                                                ]) {
                                                html += '<li data-id="' + v['id'] +
                                                    '">' + v['translation']['value'] +
                                                    '</li>';
                                            }
                                        });
                                    }
                                    html += '</ul>';
                                }

                                html +=
                                    '</div>\
                                </div>';
                                $('.new-case-buttons').append(html);
                            });
                            $('.select-list').css({'width': width, 'margin-left':'94px'});
                        }
                    }
                });
            });
        }

        function eraseCompanyDependentInputs() {
            /* PROBLÉMA TÍPUSA */
            let case_type_input = $('input#case_type');
            case_type_input.val('');
            let button = case_type_input.closest('.steps').find('button.select-button');
            button.html(button.data('translation'));

            /* KONZULTÁCIÓK SZÁMA */
            let consultation_number_input = $('input#consultation_number');
            consultation_number_input.val('');
            button = consultation_number_input.closest('.steps').find('button.select-button');
            button.html(button.data('translation'));

            /* KONZULTÁCIÓK HOSSZA */
            let consultation_minute_input = $('input#consultation_minute');
            consultation_minute_input.val('');
            button = consultation_minute_input.closest('.steps').find('button.select-button');
            button.html(button.data('translation'));
        }

        let permission_id;
        let specialization_id;
        let language_skill_id;
        let consultation_id;
        let is_crisis;
        let consultation_select;
        let is_personal;
        let problem_details;

        function permissionChangeHandler() {
            $('input#case_type').on('change', function() {
                $('#loading').remove();
                $('#experts .expert').remove();
                //$('#experts').append('<p id="loading">{{ __('common.loading') }}...</p>');
                permission_id = $(this).val();
                const number = $('ul.select-list li.permission_' + permission_id).attr('data-number');
                const duration = $('ul.select-list li.permission_' + permission_id).attr('data-duration');

                $('li').each(function() {
                    if ($(this).data('permission-id') && $(this).data('permission-id') != permission_id) {
                        $(this).addClass('d-none');
                    } else {
                        if ($(this).data('contract-holder-id') && $(this).data('contract-holder-id') !=
                            contract_holder_id) {
                            return;
                        }

                        $(this).removeClass('d-none');
                    }
                })

                $('ul.select-list#consultation_minute li').each(function() {
                    //ha a szám egyezik vagy egyéb eset
                    if ($(this).html().trim() == duration || permission_id == 4) {
                        $(this).removeClass('d-none');
                    } else {
                        $(this).addClass('d-none');
                    }
                })

                $('ul.select-list#consultation_number li').each(function() {
                    //ha a szám egyezik vagy egyéb eset
                    if ($(this).html().trim() == number || permission_id == 4) {
                        $(this).removeClass('d-none');
                    } else {
                        $(this).addClass('d-none');
                    }
                })

                $('ul.select-list#case_specialization li').each(function() {
                    //ha a szám egyezik vagy egyéb eset
                    if ($(this).html().trim() == number || permission_id == 1) {
                        $(this).removeClass('d-none');
                    } else {
                        $(this).addClass('d-none');
                    }
                })

                if (language_skill_id != '' || is_crisis == 1) {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: 'POST',
                        url: '/ajax/get-available-experts',
                        data: {
                            language_skill_id: language_skill_id,
                            permission_id: permission_id,
                            city_id: $('input[name="inputs[' + city_case_input_id + ']"]').val(),
                            is_crisis: is_crisis,
                            is_personal: is_personal,
                            company_id: company_id
                        },
                        success: function(data) {
                            refreshExpertList(data);
                        }
                    });
                }

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: 'POST',
                    url: '/ajax/get-available-experts',
                    data: {
                        language_skill_id: language_skill_id,
                        permission_id: permission_id,
                        city_id: $('input[name="inputs[' + city_case_input_id + ']"]').val(),
                        is_crisis: is_crisis,
                        is_personal: is_personal,
                        company_id: company_id
                    },
                    success: function(data) {
                        refreshExpertList(data);
                    }
                });

                // Update available consultation types based on the selected permission and company
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: 'POST',
                    url: '/ajax/get-available-consultation-types',
                    data: {
                        permission_id: permission_id,
                        company_id: company_id
                    },
                    success: function(data) {
                        refreshConsultationTypesList(data);
                    }
                });
            });
        }

        function crisisChangeHandler() {
            $('input#case_is_crisis').on('change', function() {
                is_crisis = $(this).val();
                if (language_skill_id == '') return;

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: 'POST',
                    url: '/ajax/get-available-experts',
                    data: {
                        language_skill_id: language_skill_id,
                        permission_id: permission_id,
                        city_id: $('input[name="inputs[' + city_case_input_id + ']"]').val(),
                        is_crisis: is_crisis,
                        is_personal: is_personal,
                        company_id: company_id
                    },
                    success: function(data) {
                        refreshExpertList(data);
                    }
                });
            });
        }

        function specializationChangeHandler() {
            $('input#case_specialization').on('change', function() {
                specialization_id = $(this).val();
                if (language_skill_id) {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: 'POST',
                        url: '/ajax/get-available-experts',
                        data: {
                            language_skill_id: language_skill_id,
                            specialization_id: specialization_id,
                            permission_id: permission_id,
                            city_id: $('input[name="inputs[' + city_case_input_id + ']"]').val(),
                            is_crisis: is_crisis,
                            is_personal: is_personal,
                            company_id: company_id
                        },
                        success: function(data) {
                            refreshExpertList(data);
                        }
                    });
                }
            });
        }

        function languageSkillChangeHandler() {
            $('input#case_language_skill').on('change', function() {

                language_skill_id = $(this).val();

                if (is_crisis == 1) return;
                if (consultation_select == '') return;

                $('#loading').remove();
                $('#experts .expert').remove();
                $('#experts').append('<p id="loading">{{ __('common.loading') }}...</p>');


                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: 'POST',
                    url: '/ajax/get-available-experts',
                    data: {
                        language_skill_id: language_skill_id,
                        specialization_id: specialization_id,
                        permission_id: permission_id,
                        city_id: $('input[name="inputs[' + city_case_input_id + ']"]').val(),
                        is_crisis: is_crisis,
                        is_personal: is_personal,
                        company_id: company_id
                    },
                    success: function(data) {
                        refreshExpertList(data);
                    }
                });
            });
        }

        function consultationMinuteChangeHandler ()
        {
            if (is_crisis == 1) return;
            consultation_select = $('input[name="inputs[' + city_case_input_id + ']"]').val();
            $('#consultation_minute').on('change', function() {
                consultation_id = $(this).val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: 'POST',
                    url: '/ajax/get-available-experts',
                    data: {
                        language_skill_id: language_skill_id,
                        specialization_id: specialization_id,
                        permission_id: permission_id,
                        city_id: $('input[name="inputs[' + city_case_input_id + ']"]').val(),
                        is_crisis: is_crisis,
                        consultation_minute: $(this).val(),
                        is_personal,
                        company_id: company_id,
                        problem_details: problem_details,
                    },
                    success: function(data) {
                        refreshExpertList(data);
                    }
                });
            });
        }

        function clientLanguageChangeHandler ()
        {
            $('#clients_language').on('change', function() {

                $('#case_language_skill').val($(this).val());

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: 'POST',
                    url: '/ajax/get-available-experts',
                    data: {
                        language_skill_id: $(this).val(),
                        specialization_id: specialization_id,
                        permission_id: permission_id,
                        city_id: $('input[name="inputs[' + city_case_input_id + ']"]').val(),
                        is_crisis: is_crisis,
                        consultation_minute: consultation_id,
                        is_personal: is_personal,
                        company_id: company_id,
                        problem_details: problem_details,
                    },
                    success: function(data) {
                        refreshExpertList(data);
                    }
                });
            });
        }

        function problem_details_change_handler ()
        {
            $('#presenting_concern').on('change', function() {
                problem_details = $(this).val();

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: 'POST',
                    url: '/ajax/get-available-experts',
                    data: {
                        company_id: company_id,
                        problem_details: problem_details,
                    },
                    success: function(data) {
                        refreshExpertList(data);
                    }
                });
            });
        }

        function consultationTypeChangeHandler() {
            $('#case_consultation_type').on('change', function() {
                if (this.value == 80) {
                    is_personal = true;
                } else {
                    is_personal = false;
                }
            });
        }

        function refreshExpertList(data)
        {
            $('#loading').remove();
            $('.expert').remove();
            $.each(data.experts, function(index, value) {
                const html =
                    '<div class="expert">\
                        <span>' +
                        value
                        .name +
                        '</span>\
                        <span></span>\
                    </div>';
                $('#experts').append(html);
            });
        }

        function refreshConsultationTypesList(data)
        {
            list = $('#consultation_type_select');
            list.empty();

            $.each(data, function(index, type) {
                list.append('<li data-id='+type.id+'>'+type.translation.value+'</li>');
            });
        }

        function selectFromList() {
            $('.new-case-buttons').on('click', 'ul.select-list li', function() {
                const li = $(this);
                const steps = li.closest('.steps');
                const id = li.data('id');
                const text = li.html();
                steps.find('button.select-button').html(text);
                steps.find('input[type="hidden"]').val(id);
                steps.find('input[type="hidden"]').trigger('change');
                steps.find('.select-list').css('display', 'none');
            });
        }

        function nextButton(element) {
            var current_step = $(element).closest('.steps');

            // START  Deutsche Telekom IT Solutions / DT-ITS email check
            let current_input_name = $(current_step).children('input').attr('name');
            let current_input_id = (current_input_name) ? current_input_name.match(/\[(.*?)\]/)[1] : '';

            let email_allowed = true;
            
            // Deutsche Telekom IT Solutions / DT-ITS(1165), email input id (18)
            if (company_id == {{config('companies.deutsche-telekom')}} && current_input_id == 18) {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: 'POST',
                    url: '/ajax/check-telekom-email-address',
                    async: false,
                    data: {
                        email: current_step.find('input[name^="inputs["]').val()
                    },
                    success: function(response) {
                        if (!response.valid) {
                            email_allowed = false;

                            Swal.fire(
                                response.message,
                                '',
                                'error'
                            );
                        }
                    }
                });
            }
            
            // A valid email address is required for all cases
            if (current_input_id == 18) {
                var email = current_step.find('input[name^="inputs["]').val();
                const pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                
                if (!pattern.test(email)) {
                    email_allowed = false;
                    Swal.fire(
                        '{{__('common.email_required')}}',
                        '',
                        'error'
                    );
                }
            }
            // END

            if (!email_allowed) {
                return false;
            }


            // Application code check (after company selected)
            let application_allowed = true;

            if (current_input_id == 2 && !application_checked) {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: 'POST',
                    url: '/ajax/check-application-code-requirement',
                    async: false,
                    data: {
                        company_id: current_step.find('input[name^="inputs["]').val()
                    },
                    success: function(response) {
                        if (response.required) {
                            application_allowed = false;
                            Swal.fire({
                                title: '{{__('common.application_code_submit_request')}}',
                                input: "text",
                                inputAttributes: {
                                    autocapitalize: "off"
                                },
                                icon: 'warning',
                                showCancelButton: true,
                            }).then((result) => {
                                $.ajax({
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    },
                                    type: 'POST',
                                    url: '/ajax/check-application-code',
                                    async: false,
                                    data: {
                                        code: result.value,
                                        company_id: company_id
                                    },
                                    success: function(response) {
                                        if (!response.valid) {
                                            Swal.fire(
                                                '{{__('common.application_code_invalid')}}',
                                                '',
                                                'error'
                                            );
                                        } else {
                                            application_allowed = true;
                                            application_checked = true;
                                            $("#application_code").val(result.value);

                                            Swal.fire(
                                                '{{__('common.application_code_valid')}}',
                                                '',
                                                'success'
                                            );
                                        }
                                    }
                                });
                            });
                        }
                    }
                });
            }

            if (!application_allowed) {
                return false;
            }
            // APPLICATION CODE CHECK END

            if (current_step.find('input[name^="inputs["]').val() == '') {
                $('#required').removeClass('d-none');
                return false;
            }
            $('#required').addClass('d-none');
            let next_step = current_step.next('.steps');
            var input_name = $(next_step).children('input').attr('name');
            var input_id = (input_name) ? input_name.match(/\[(.*?)\]/)[1] : '';


            // Skip city and location input when consultation type is not personal
            if (is_personal === false && input_id == 5 || is_personal === false && input_id == 54) {
                next_step = next_step.next('.steps');
                specialization_id = '';

                var key = $(this).data('step');
                editCurrentStepNumber(1);
                $('#current-step').html(key);

                // Skip specialization (input 66) IF case type is not 1 (psychological) OR skip language skill input
                if (input_id == 7 && permission_id != 1 || input_id == 54) {
                    next_step = next_step.next('.steps');
                    specialization_id = '';

                    var key = $(this).data('step');
                    editCurrentStepNumber(1);
                    $('#current-step').html(key);
                }

            } else {
                // Skip specialization (input 66) IF case type is not 1 (psychological) OR skip language skill input
                if (input_id == 66 && permission_id != 1 || input_id == 65) {
                    next_step = next_step.next('.steps');
                    specialization_id = '';

                    var key = $(this).data('step');
                    editCurrentStepNumber(1);
                    $('#current-step').html(key);
                }
            }

            // Skip UCMS case identifier AND additional client info IF company contract holder is not WPO/Telus
            // Submit form instead.
            if (contract_holder_id != 6 && next_step.attr('data-default-type') == 'ucms_case_identifier' ) {
                next_step = next_step.next('.steps');
                var key = $(this).data('step');
                editCurrentStepNumber(1);
                $('#current-step').html(key);
            }

            if (contract_holder_id != 6 && next_step.attr('data-default-type') == 'additional_information' ) {
                next_step = next_step.next('.steps');
                var key = $(this).data('step');
                editCurrentStepNumber(1);
                $('#current-step').html(key);
            }
            
            // VALIDATE UCMS case identifier
            if (current_step.attr('data-default-type') == 'ucms_case_identifier' && contract_holder_id == 6) {
                var value = current_step.find('input[name^="inputs["]').val();

                // Regex - must be 7 digit, cannot be 0000000 or 1111111
                var isValid = /^(?!0000000$)(?!1111111$)\d{7}$/.test(value);

                if (!isValid) {
                    Swal.fire(
                        '{{__('common.ucms_required')}}',
                        '',
                        'error'
                    );

                    return false;
                }
            }
            
            if (next_step.length > 0) {
                current_step.removeClass('d-block').addClass('d-none');
                current_step.find('.select-list').css('display', 'none');
                next_step.addClass('d-block').removeClass('d-none');
                next_step.trigger('show');
            } else {
                $(element).prop('disabled', 'disabled');
                $('form').submit();
            }

            if (input_id == 17 && company_id == 843) {
                showSpecialPhoneModal();
                $('input[name="inputs[17]"]').prop('readonly', true);
            }
        }

        async function showSpecialPhoneModal() {
            const {
                value: formValues
            } = await Swal.fire({
                html: `<select id="country_code" class="swal2-input">
                        <option data-countryCode="DZ" value="213">Algeria (+213)</option>
                        <option data-countryCode="AD" value="376">Andorra (+376)</option>
                        <option data-countryCode="AO" value="244">Angola (+244)</option>
                        <option data-countryCode="AI" value="1264">Anguilla (+1264)</option>
                        <option data-countryCode="AG" value="1268">Antigua &amp; Barbuda (+1268)</option>
                        <option data-countryCode="AR" value="54">Argentina (+54)</option>
                        <option data-countryCode="AM" value="374">Armenia (+374)</option>
                        <option data-countryCode="AW" value="297">Aruba (+297)</option>
                        <option data-countryCode="AU" value="61">Australia (+61)</option>
                        <option data-countryCode="AT" value="43">Austria (+43)</option>
                        <option data-countryCode="AZ" value="994">Azerbaijan (+994)</option>
                        <option data-countryCode="BS" value="1242">Bahamas (+1242)</option>
                        <option data-countryCode="BH" value="973">Bahrain (+973)</option>
                        <option data-countryCode="BD" value="880">Bangladesh (+880)</option>
                        <option data-countryCode="BB" value="1246">Barbados (+1246)</option>
                        <option data-countryCode="BY" value="375">Belarus (+375)</option>
                        <option data-countryCode="BE" value="32">Belgium (+32)</option>
                        <option data-countryCode="BZ" value="501">Belize (+501)</option>
                        <option data-countryCode="BJ" value="229">Benin (+229)</option>
                        <option data-countryCode="BM" value="1441">Bermuda (+1441)</option>
                        <option data-countryCode="BT" value="975">Bhutan (+975)</option>
                        <option data-countryCode="BO" value="591">Bolivia (+591)</option>
                        <option data-countryCode="BA" value="387">Bosnia Herzegovina (+387)</option>
                        <option data-countryCode="BW" value="267">Botswana (+267)</option>
                        <option data-countryCode="BR" value="55">Brazil (+55)</option>
                        <option data-countryCode="BN" value="673">Brunei (+673)</option>
                        <option data-countryCode="BG" value="359">Bulgaria (+359)</option>
                        <option data-countryCode="BF" value="226">Burkina Faso (+226)</option>
                        <option data-countryCode="BI" value="257">Burundi (+257)</option>
                        <option data-countryCode="KH" value="855">Cambodia (+855)</option>
                        <option data-countryCode="CM" value="237">Cameroon (+237)</option>
                        <option data-countryCode="CA" value="1">Canada (+1)</option>
                        <option data-countryCode="CV" value="238">Cape Verde Islands (+238)</option>
                        <option data-countryCode="KY" value="1345">Cayman Islands (+1345)</option>
                        <option data-countryCode="CF" value="236">Central African Republic (+236)</option>
                        <option data-countryCode="CL" value="56">Chile (+56)</option>
                        <option data-countryCode="CN" value="86">China (+86)</option>
                        <option data-countryCode="CO" value="57">Colombia (+57)</option>
                        <option data-countryCode="KM" value="269">Comoros (+269)</option>
                        <option data-countryCode="CG" value="242">Congo (+242)</option>
                        <option data-countryCode="CK" value="682">Cook Islands (+682)</option>
                        <option data-countryCode="CR" value="506">Costa Rica (+506)</option>
                        <option data-countryCode="HR" value="385">Croatia (+385)</option>
                        <option data-countryCode="CU" value="53">Cuba (+53)</option>
                        <option data-countryCode="CY" value="90392">Cyprus North (+90392)</option>
                        <option data-countryCode="CY" value="357">Cyprus South (+357)</option>
                        <option data-countryCode="CZ" value="42">Czech Republic (+42)</option>
                        <option data-countryCode="DK" value="45">Denmark (+45)</option>
                        <option data-countryCode="DJ" value="253">Djibouti (+253)</option>
                        <option data-countryCode="DM" value="1809">Dominica (+1809)</option>
                        <option data-countryCode="DO" value="1809">Dominican Republic (+1809)</option>
                        <option data-countryCode="EC" value="593">Ecuador (+593)</option>
                        <option data-countryCode="EG" value="20">Egypt (+20)</option>
                        <option data-countryCode="SV" value="503">El Salvador (+503)</option>
                        <option data-countryCode="GQ" value="240">Equatorial Guinea (+240)</option>
                        <option data-countryCode="ER" value="291">Eritrea (+291)</option>
                        <option data-countryCode="EE" value="372">Estonia (+372)</option>
                        <option data-countryCode="ET" value="251">Ethiopia (+251)</option>
                        <option data-countryCode="FK" value="500">Falkland Islands (+500)</option>
                        <option data-countryCode="FO" value="298">Faroe Islands (+298)</option>
                        <option data-countryCode="FJ" value="679">Fiji (+679)</option>
                        <option data-countryCode="FI" value="358">Finland (+358)</option>
                        <option data-countryCode="FR" value="33">France (+33)</option>
                        <option data-countryCode="GF" value="594">French Guiana (+594)</option>
                        <option data-countryCode="PF" value="689">French Polynesia (+689)</option>
                        <option data-countryCode="GA" value="241">Gabon (+241)</option>
                        <option data-countryCode="GM" value="220">Gambia (+220)</option>
                        <option data-countryCode="GE" value="7880">Georgia (+7880)</option>
                        <option data-countryCode="DE" value="49">Germany (+49)</option>
                        <option data-countryCode="GH" value="233">Ghana (+233)</option>
                        <option data-countryCode="GI" value="350">Gibraltar (+350)</option>
                        <option data-countryCode="GR" value="30">Greece (+30)</option>
                        <option data-countryCode="GL" value="299">Greenland (+299)</option>
                        <option data-countryCode="GD" value="1473">Grenada (+1473)</option>
                        <option data-countryCode="GP" value="590">Guadeloupe (+590)</option>
                        <option data-countryCode="GU" value="671">Guam (+671)</option>
                        <option data-countryCode="GT" value="502">Guatemala (+502)</option>
                        <option data-countryCode="GN" value="224">Guinea (+224)</option>
                        <option data-countryCode="GW" value="245">Guinea - Bissau (+245)</option>
                        <option data-countryCode="GY" value="592">Guyana (+592)</option>
                        <option data-countryCode="HT" value="509">Haiti (+509)</option>
                        <option data-countryCode="HN" value="504">Honduras (+504)</option>
                        <option data-countryCode="HK" value="852">Hong Kong (+852)</option>
                        <option data-countryCode="HU" value="36">Hungary (+36)</option>
                        <option data-countryCode="IS" value="354">Iceland (+354)</option>
                        <option data-countryCode="IN" value="91">India (+91)</option>
                        <option data-countryCode="ID" value="62">Indonesia (+62)</option>
                        <option data-countryCode="IR" value="98">Iran (+98)</option>
                        <option data-countryCode="IQ" value="964">Iraq (+964)</option>
                        <option data-countryCode="IE" value="353">Ireland (+353)</option>
                        <option data-countryCode="IL" value="972">Israel (+972)</option>
                        <option data-countryCode="IT" value="39">Italy (+39)</option>
                        <option data-countryCode="JM" value="1876">Jamaica (+1876)</option>
                        <option data-countryCode="JP" value="81">Japan (+81)</option>
                        <option data-countryCode="JO" value="962">Jordan (+962)</option>
                        <option data-countryCode="KZ" value="7">Kazakhstan (+7)</option>
                        <option data-countryCode="KE" value="254">Kenya (+254)</option>
                        <option data-countryCode="KI" value="686">Kiribati (+686)</option>
                        <option data-countryCode="KP" value="850">Korea North (+850)</option>
                        <option data-countryCode="KR" value="82">Korea South (+82)</option>
                        <option data-countryCode="KW" value="965">Kuwait (+965)</option>
                        <option data-countryCode="KG" value="996">Kyrgyzstan (+996)</option>
                        <option data-countryCode="LA" value="856">Laos (+856)</option>
                        <option data-countryCode="LV" value="371">Latvia (+371)</option>
                        <option data-countryCode="LB" value="961">Lebanon (+961)</option>
                        <option data-countryCode="LS" value="266">Lesotho (+266)</option>
                        <option data-countryCode="LR" value="231">Liberia (+231)</option>
                        <option data-countryCode="LY" value="218">Libya (+218)</option>
                        <option data-countryCode="LI" value="417">Liechtenstein (+417)</option>
                        <option data-countryCode="LT" value="370">Lithuania (+370)</option>
                        <option data-countryCode="LU" value="352">Luxembourg (+352)</option>
                        <option data-countryCode="MO" value="853">Macao (+853)</option>
                        <option data-countryCode="MK" value="389">Macedonia (+389)</option>
                        <option data-countryCode="MG" value="261">Madagascar (+261)</option>
                        <option data-countryCode="MW" value="265">Malawi (+265)</option>
                        <option data-countryCode="MY" value="60">Malaysia (+60)</option>
                        <option data-countryCode="MV" value="960">Maldives (+960)</option>
                        <option data-countryCode="ML" value="223">Mali (+223)</option>
                        <option data-countryCode="MT" value="356">Malta (+356)</option>
                        <option data-countryCode="MH" value="692">Marshall Islands (+692)</option>
                        <option data-countryCode="MQ" value="596">Martinique (+596)</option>
                        <option data-countryCode="MR" value="222">Mauritania (+222)</option>
                        <option data-countryCode="YT" value="269">Mayotte (+269)</option>
                        <option data-countryCode="MX" value="52">Mexico (+52)</option>
                        <option data-countryCode="FM" value="691">Micronesia (+691)</option>
                        <option data-countryCode="MD" value="373">Moldova (+373)</option>
                        <option data-countryCode="MC" value="377">Monaco (+377)</option>
                        <option data-countryCode="MN" value="976">Mongolia (+976)</option>
                        <option data-countryCode="MS" value="1664">Montserrat (+1664)</option>
                        <option data-countryCode="MA" value="212">Morocco (+212)</option>
                        <option data-countryCode="MZ" value="258">Mozambique (+258)</option>
                        <option data-countryCode="MN" value="95">Myanmar (+95)</option>
                        <option data-countryCode="NA" value="264">Namibia (+264)</option>
                        <option data-countryCode="NR" value="674">Nauru (+674)</option>
                        <option data-countryCode="NP" value="977">Nepal (+977)</option>
                        <option data-countryCode="NL" value="31">Netherlands (+31)</option>
                        <option data-countryCode="NC" value="687">New Caledonia (+687)</option>
                        <option data-countryCode="NZ" value="64">New Zealand (+64)</option>
                        <option data-countryCode="NI" value="505">Nicaragua (+505)</option>
                        <option data-countryCode="NE" value="227">Niger (+227)</option>
                        <option data-countryCode="NG" value="234">Nigeria (+234)</option>
                        <option data-countryCode="NU" value="683">Niue (+683)</option>
                        <option data-countryCode="NF" value="672">Norfolk Islands (+672)</option>
                        <option data-countryCode="NP" value="670">Northern Marianas (+670)</option>
                        <option data-countryCode="NO" value="47">Norway (+47)</option>
                        <option data-countryCode="OM" value="968">Oman (+968)</option>
                        <option data-countryCode="PW" value="680">Palau (+680)</option>
                        <option data-countryCode="PA" value="507">Panama (+507)</option>
                        <option data-countryCode="PG" value="675">Papua New Guinea (+675)</option>
                        <option data-countryCode="PY" value="595">Paraguay (+595)</option>
                        <option data-countryCode="PE" value="51">Peru (+51)</option>
                        <option data-countryCode="PH" value="63">Philippines (+63)</option>
                        <option data-countryCode="PL" value="48">Poland (+48)</option>
                        <option data-countryCode="PT" value="351">Portugal (+351)</option>
                        <option data-countryCode="PR" value="1787">Puerto Rico (+1787)</option>
                        <option data-countryCode="QA" value="974">Qatar (+974)</option>
                        <option data-countryCode="RE" value="262">Reunion (+262)</option>
                        <option data-countryCode="RO" value="40">Romania (+40)</option>
                        <option data-countryCode="RU" value="7">Russia (+7)</option>
                        <option data-countryCode="RW" value="250">Rwanda (+250)</option>
                        <option data-countryCode="SM" value="378">San Marino (+378)</option>
                        <option data-countryCode="ST" value="239">Sao Tome &amp; Principe (+239)</option>
                        <option data-countryCode="SA" value="966">Saudi Arabia (+966)</option>
                        <option data-countryCode="SN" value="221">Senegal (+221)</option>
                        <option data-countryCode="CS" value="381">Serbia (+381)</option>
                        <option data-countryCode="SC" value="248">Seychelles (+248)</option>
                        <option data-countryCode="SL" value="232">Sierra Leone (+232)</option>
                        <option data-countryCode="SG" value="65">Singapore (+65)</option>
                        <option data-countryCode="SK" value="421">Slovak Republic (+421)</option>
                        <option data-countryCode="SI" value="386">Slovenia (+386)</option>
                        <option data-countryCode="SB" value="677">Solomon Islands (+677)</option>
                        <option data-countryCode="SO" value="252">Somalia (+252)</option>
                        <option data-countryCode="ZA" value="27">South Africa (+27)</option>
                        <option data-countryCode="ES" value="34">Spain (+34)</option>
                        <option data-countryCode="LK" value="94">Sri Lanka (+94)</option>
                        <option data-countryCode="SH" value="290">St. Helena (+290)</option>
                        <option data-countryCode="KN" value="1869">St. Kitts (+1869)</option>
                        <option data-countryCode="SC" value="1758">St. Lucia (+1758)</option>
                        <option data-countryCode="SD" value="249">Sudan (+249)</option>
                        <option data-countryCode="SR" value="597">Suriname (+597)</option>
                        <option data-countryCode="SZ" value="268">Swaziland (+268)</option>
                        <option data-countryCode="SE" value="46">Sweden (+46)</option>
                        <option data-countryCode="CH" value="41">Switzerland (+41)</option>
                        <option data-countryCode="SI" value="963">Syria (+963)</option>
                        <option data-countryCode="TW" value="886">Taiwan (+886)</option>
                        <option data-countryCode="TJ" value="7">Tajikstan (+7)</option>
                        <option data-countryCode="TH" value="66">Thailand (+66)</option>
                        <option data-countryCode="TG" value="228">Togo (+228)</option>
                        <option data-countryCode="TO" value="676">Tonga (+676)</option>
                        <option data-countryCode="TT" value="1868">Trinidad &amp; Tobago (+1868)</option>
                        <option data-countryCode="TN" value="216">Tunisia (+216)</option>
                        <option data-countryCode="TR" value="90">Turkey (+90)</option>
                        <option data-countryCode="TM" value="7">Turkmenistan (+7)</option>
                        <option data-countryCode="TM" value="993">Turkmenistan (+993)</option>
                        <option data-countryCode="TC" value="1649">Turks &amp; Caicos Islands (+1649)</option>
                        <option data-countryCode="TV" value="688">Tuvalu (+688)</option>
                        <option data-countryCode="UG" value="256">Uganda (+256)</option>
                        <option data-countryCode="UA" value="380">Ukraine (+380)</option>
                        <option data-countryCode="AE" value="971">United Arab Emirates (+971)</option>
                        <option data-countryCode="UY" value="598">Uruguay (+598)</option>
                        <option data-countryCode="UZ" value="7">Uzbekistan (+7)</option>
                        <option data-countryCode="VU" value="678">Vanuatu (+678)</option>
                        <option data-countryCode="VA" value="379">Vatican City (+379)</option>
                        <option data-countryCode="VE" value="58">Venezuela (+58)</option>
                        <option data-countryCode="VN" value="84">Vietnam (+84)</option>
                        <option data-countryCode="VG" value="84">Virgin Islands - British (+1284)</option>
                        <option data-countryCode="VI" value="84">Virgin Islands - US (+1340)</option>
                        <option data-countryCode="WF" value="681">Wallis &amp; Futuna (+681)</option>
                        <option data-countryCode="YE" value="969">Yemen (North)(+969)</option>
                        <option data-countryCode="YE" value="967">Yemen (South)(+967)</option>
                        <option data-countryCode="ZM" value="260">Zambia (+260)</option>
                        <option data-countryCode="ZW" value="263">Zimbabwe (+263)</option>
                    </select>` +
                    '<input id="phone_number" placeholder="123456789" class="swal2-input">',
                focusConfirm: false,
                showCancelButton: true,
                cancelButtonText: '{{__('popup.case_no_phone_number')}}',
                confirmButtonText: '{{__('popup.case_save_phone_number')}}',
                allowOutsideClick: false,
                preConfirm: () => {
                    if (!document.getElementById('country_code').value || !document.getElementById(
                            'phone_number').value) {
                        Swal.showValidationMessage(
                            `{{__('popup.case_field_required')}}`
                        )
                    }

                    if (document.getElementById('phone_number').value.match(/\D/g)) {
                        Swal.showValidationMessage(
                            `{{__('popup.case_phone_number_only_number_error')}}`
                        )
                    }

                    if (document.getElementById('phone_number').value.length > 15) {
                        Swal.showValidationMessage(
                            `{{__('popup.case_phone_number_length_error')}}`
                        )
                    }

                    return [
                        document.getElementById('country_code').value,
                        document.getElementById('phone_number').value
                    ]
                }
            })

            if (formValues) {
                formValues[0] = formValues[0].replace(/\D/g, '');
                formValues[1] = formValues[1].replace(/\D/g, '');

                $('input[name="inputs[17]"]').val('+' + formValues[0] + formValues[1]);
            } else {
                $('input[name="inputs[17]"]').val('-');
            }
        }

        function backButton(element) {
            var current_step = $(element).closest('.steps');
            var prev_step = current_step.prev('.steps');

            var input_name = $(prev_step).children('input').attr('name');
            var input_id = input_name.match(/\[(.*?)\]/)[1];

            // Skip city and location input when consultation type is not personal
            if (is_personal === false && input_id == 5 || is_personal === false && input_id == 54) {
                prev_step = prev_step.prev('.steps');
                specialization_id = '';

                var key = $(this).data('step');
                editCurrentStepNumber(1);
                $('#current-step').html(key);

                // Skip specialization (input 66) IF case type is not 1 (psychological) OR skip language skill input
                if (input_id == 7 && permission_id != 1 || input_id == 54) {
                    prev_step = prev_step.prev('.steps');
                    specialization_id = '';

                    var key = $(this).data('step');
                    editCurrentStepNumber(1);
                    $('#current-step').html(key);
                }

            } else {
                //  Skip specialization (input 66) IF case type is not 1 (psychological) OR skip language skill input
                if (input_id == 66 && permission_id != 1 || input_id == 65) {
                    prev_step = prev_step.prev('.steps');
                    specialization_id = '';

                    var key = $(this).data('step');
                    editCurrentStepNumber(1);
                    $('#current-step').html(key);
                }
            }

            if (prev_step.length > 0) {
                current_step.removeClass('d-block').addClass('d-none');
                current_step.find('.select-list').css('display', 'none');
                prev_step.addClass('d-block').removeClass('d-none');
                prev_step.trigger('show');
            }
        }

        function editCurrentStepNumber(offset = 0) {
            $('.new-case-buttons').on('show', '.steps', function() {
                var key = $(this).data('step');
                $('#current-step').html(key - offset);
            });
        }

        function actionButton(element) {
            const type = $(element).data('type');
            var input_type = element.getAttribute('case_input');

            if (input_type == 'case_type') {
                get_company_permissions();
            }

            if (type == 'select') {
                $(element).closest('.steps').find('ul.select-list').show();
            } else if (type == 'date') {
                $(element).closest('.steps').find('input[type="hidden"]').trigger('click');
            }
        }

        //Set (select) list of the company's permissions
        function get_company_permissions() {
            $('#permissions #loading').remove();
            $('.permissions_select_list').addClass('d-none');
            $('input[type="hidden"][id="case_type"]').val('');

            let case_type_select_button = $('input[type="hidden"][id="case_type"]')
                .prev('button.select-button');
            case_type_select_button.html(case_type_select_button.data('translation'));

            $.each(company_permissions, function(index, value) {;
                //megjelenítjük az adott jogosultságot
                $('.permissions_select_list.permission_' + value.id)
                    .removeClass('d-none');
                //rögzítjük, hogy adott jogosultsághoz hány tanácsadás alkalom jár és azok milyen hosszúak
                $('.permissions_select_list.permission_' + value.id).attr(
                    'data-number', value.pivot.number);
                $('.permissions_select_list.permission_' + value.id).attr(
                    'data-duration', value.pivot.duration);
            });

            //EGYÉB ESETEKET MEG KELL JELENÍTENI MINDEGYIK ESETBEN
            $('.permissions_select_list.permission_4').removeClass('d-none');
        }
</script>
@endsection

@section('content')
<div class="row mt-5">
    <div class="col-12">
        <h1>{{ __('common.new-case') }} - <span id="current-step">1</span><span id="all-steps">/</span></h1>
    </div>
    <form method="post" class="col-12 col-lg-8" style="margin-bottom: 30px" name="case-create">
        {{ csrf_field() }}
        <div class="new-case-buttons row" style="z-index: 3">
            @php
            $case_inputs->splice(2, 0, [$case_inputs[6]]); // Problem type
            $case_inputs->forget(7);
            $case_inputs->splice(3, 0, [$case_inputs[22]]); // Consultation type
            $case_inputs->forget(22);
            $case_inputs->splice(8, 0, [$case_inputs[27]]); // Specialization
            $case_inputs->forget(27);
            @endphp
            @foreach ($case_inputs as $key => $case_input)
            <div class="col-12 steps @if ($key == 0) d-block @else d-none @endif" data-step="{{ $key + 1 }}"
                data-type="{{ $case_input->type }}" data-default-type="{{ $case_input->default_type }}" style="height: 64px!important">
                <button type="button" name="button"
                    class="back-button btn-radius col-12 col-lg-2 mb-1 mt-1 mb-lg-0 mt-lg-0"
                    style="--btn-height: 100%; --btn-margin-bottom: 0px; min-width: auto!important; margin-right: 0px!important;"
                    onClick="backButton(this)">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" style="width: 40px;">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m18.75 4.5-7.5 7.5 7.5 7.5m-6-15L5.25 12l7.5 7.5" />
                    </svg>
                </button>
                @if ($case_input->default_type == 'company_chooser')
                <button type="button" name="button" class="select-button col-12 col-lg-8 h-100"
                    data-type="{{ $case_input->type }}" onClick='actionButton(this)'>{{ $case_input->translation != null
                    ? $case_input->translation->value : null }}</button>
                <input type="hidden" class="h-100" id="company_chooser" name="inputs[{{ $case_input->id }}]">
                @elseif($case_input->id == 24)
                <button type="button" name="button" class="select-button col-lg-8 h-100"
                    data-type="{{ $case_input->type }}"
                    data-translation="{{ $case_input->translation != null ? $case_input->translation->value : null }}"
                    onClick='actionButton(this)'>{{ $case_input->translation != null ? $case_input->translation->value :
                    null }}</button>
                <input type="hidden" id="case_consultation_type" name="inputs[{{ $case_input->id }}]">
                @elseif($case_input->default_type == 'case_type')
                <button type="button" name="button" class="select-button col-lg-8 h-100"
                    data-type="{{ $case_input->type }}" case_input="case_type"
                    data-translation="{{ $case_input->translation != null ? $case_input->translation->value : null }}"
                    onClick='actionButton(this)'>{{ $case_input->translation != null ? $case_input->translation->value :
                    null }}</button>
                <input class="h-100" type="hidden" id="case_type" name="inputs[{{ $case_input->id }}]">
                @elseif($case_input->default_type == 'case_language_skill')
                <button type="button" name="button" class="select-button col-lg-8 h-100"
                    data-type="{{ $case_input->type }}"
                    data-translation="{{ $case_input->translation != null ? $case_input->translation->value : null }}"
                    onClick='actionButton(this)'>{{ $case_input->translation != null ? $case_input->translation->value :
                    null }} </button>
                <input type="hidden" class="h-100" id="case_language_skill" name="inputs[{{ $case_input->id }}]">
                @elseif($case_input->default_type == 'clients_language')
                <button type="button" name="button" class="select-button col-lg-8 h-100"
                    data-type="{{ $case_input->type }}"
                    data-translation="{{ $case_input->translation != null ? $case_input->translation->value : null }}"
                    onClick='actionButton(this)'>{{ $case_input->translation != null ? $case_input->translation->value :
                    null }} </button>
                <input type="hidden" id="clients_language" name="inputs[{{ $case_input->id }}]">
                @elseif($case_input->default_type == 'case_specialization')
                <button type="button" name="button" class="select-button col-lg-8 h-100"
                    data-type="{{ $case_input->type }}"
                    data-translation="{{ $case_input->translation != null ? $case_input->translation->value : null }}"
                    onClick='actionButton(this)'>{{ $case_input->translation != null ? $case_input->translation->value :
                    null }}</button>
                <input type="hidden" class="h-100" id="case_specialization" name="inputs[{{ $case_input->id }}]">
                @elseif($case_input->default_type == 'is_crisis')
                <button type="button" name="button" class="select-button col-lg-8 h-100"
                    data-type="{{ $case_input->type }}"
                    data-translation="{{ $case_input->translation != null ? $case_input->translation->value : null }}"
                    onClick='actionButton(this)'>{{ $case_input->translation != null ? $case_input->translation->value :
                    null }}</button>
                <input type="hidden" id="case_is_crisis" name="inputs[{{ $case_input->id }}]">
                @elseif($case_input->default_type == 'presenting_concern')
                <button type="button" name="button" class="select-button col-lg-8 h-100"
                    data-type="{{ $case_input->type }}"
                    data-translation="{{ $case_input->translation != null ? $case_input->translation->value : null }}"
                    onClick='actionButton(this)'>{{ $case_input->translation != null ? $case_input->translation->value :
                    null }} </button>
                <input type="hidden" id="presenting_concern" name="inputs[{{ $case_input->id }}]">
                @elseif($case_input->type == 'select')
                <button type="button" name="button" class="select-button col-12 col-lg-8 h-100"
                    data-type="{{ $case_input->type }}"
                    data-translation="{{ $case_input->translation != null ? $case_input->translation->value : null }}"
                    onClick='actionButton(this)'>{{ $case_input->translation != null ? $case_input->translation->value :
                    null }}</button>
                <input class="h-100" type="hidden" name="inputs[{{ $case_input->id }}]"
                    id="{{ $case_input->input_id }}">
                @elseif($case_input->type == 'date')
                <input type="text" name="inputs[{{ $case_input->id }}]" id="{{ $case_input->input_id }}"
                    class="datepicker col-12 col-lg-8 h-100" placeholder="{{ $case_input->translation->value }}"
                    readonly />
                @elseif($case_input->type == 'integer')
                <input type="number" class="col-12 col-lg-8 h-100" name="inputs[{{ $case_input->id }}]"
                    id="{{ $case_input->input_id }}" placeholder="{{ $case_input->translation->value }}" />
                @elseif($case_input->type == 'double')
                <input type="text" class="col-12 col-lg-8 h-100" name="inputs[{{ $case_input->id }}]"
                    id="{{ $case_input->input_id }}" placeholder="{{ $case_input->translation->value }}" />
                @elseif($case_input->type == 'text')
                <input type="text" class="col-12 col-lg-8 h-100" name="inputs[{{ $case_input->id }}]"
                    id="{{ $case_input->input_id }}" placeholder="{{ $case_input->translation->value }}" />
                @endif
                <button type="button" name="button"
                    class="next-button btn-radius col-12 col-lg-2 mb-1 mt-1 mb-lg-0 mt-lg-0"
                    style="--btn-height: 100%; --btn-margin-bottom: 0px; min-width: auto!important;"
                    onClick="nextButton(this)" data-defaulttype="{{ $case_input->default_type }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" style="width:40px">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m5.25 4.5 7.5 7.5-7.5 7.5m6-15 7.5 7.5-7.5 7.5" />
                    </svg>
                </button>
                @if ($case_input->type == 'select')
                <ul class="select-list" @if($case_input->id == 24) {{-- Consultation type --}}
                    id="consultation_type_select"
                    @else
                    id="{{ $case_input->input_id }}"
                    @endif>
                    @if ($case_input->default_type == 'company_chooser')
                    @foreach ($companies as $company)
                    <li data-id="{{ $company->id }}">{{ $company->name }}</li>
                    @endforeach
                    @elseif($case_input->default_type == 'case_type')
                    @foreach ($permissions as $permission)
                    <li data-id="{{ $permission->id }}"
                        class="permissions_select_list permission_{{ $permission->id }} d-none">
                        {{ $permission->translation->value }}</li>
                    @endforeach
                    @elseif($case_input->default_type == 'is_crisi')
                    @foreach ($permissions as $permission)
                    <li data-id="{{ $permission->id }}"
                        class="permissions_select_list permission_{{ $permission->id }} d-none">
                        {{ $permission->translation->value }}</li>
                    @endforeach
                    @elseif($case_input->default_type == 'location')
                    @foreach ($cities as $city)
                    <li data-id="{{ $city->id }}">{{ $city->name }}</li>
                    @endforeach
                    @elseif($case_input->default_type == 'case_language_skill')
                    @foreach ($languageSkills as $language)
                    <li data-id="{{ $language->id }}" class="">
                        {{ $language->translation->value }}</li>
                    @endforeach
                    @elseif($case_input->default_type == 'clients_language')
                    @foreach ($languageSkills as $language)
                    <li data-id="{{ $language->id }}" class="">
                        {{ $language->translation->value }}</li>
                    @endforeach
                    @elseif($case_input->default_type == 'case_specialization')
                    @foreach ($specializations as $specialization)
                    <li data-id="{{ $specialization->id }}"
                        class="permissions_select_list permission_{{ $specialization->id }} d-none">
                        {{ optional($specialization->translation)->value }}</li>
                    @endforeach
                    @elseif($case_input->id == 24)
                    @foreach ($consultation_types_values as $consultation_types_value)
                    <li data-id="{{ $consultation_types_value->id }}" class="">
                        {{ optional($consultation_types_value->translation)->value }}</li>
                    @endforeach
                    @else
                    @php
                    $case_input_values = $case_input->values->where('visible', 1)->sortBy(function ($case_input_value,
                    $key) {
                    return $case_input_value->translation ? $case_input_value->translation->value : null;
                    });

                    @endphp
                    @foreach ($case_input_values as $case_input_value)
                    @if ($case_input_value->translation)
                    <li @if ($case_input->id == 16) data-permission-id="{{ $case_input_value->permission_id }}" @endif
                        data-id="{{ $case_input_value->id }}"
                        data-contract-holder-id="{{ $case_input_value->contract_holder_id }}">
                        {{ $case_input_value->translation->value }}
                    </li>
                    @endif
                    @endforeach
                    @endif
                </ul>
                @endif
            </div>
            @endforeach
        </div>
        <div id="required" class="d-none">
            <p>{{ __('common.input-is-required') }}</p>
        </div>
        <input type="hidden" name="application_code" id="application_code">
    </form>
    <div class="col-12 col-lg-4 mobile" style="z-index: 0">
        <div id="permissions" class="right-side">
            <p class="title">{{ __('common.authorizations') }}:</p>
        </div>
        <div id="experts" class="right-side">
            <p class="title">{{ __('common.available-experts') }}:</p>
        </div>
    </div>
</div>
@endsection
