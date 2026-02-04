@extends('layout.master')

@section('title')
    Operator Dashboard
@endsection

@section('extra_css')
    <link rel="stylesheet" href="/assets/css/cases/new.css?t={{ time() }}">
    <link rel="stylesheet" href="/assets/css/cases/datetime.css?t={{ time() }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.css" rel="stylesheet"/>

    <style>
        .inactive {
            display: none;
        }

        .active {
            display: contents;
        }

        .inactive-button {
            display: none !important;
        }

        .active-button {
            display: block;
        }

        select {
            -webkit-appearance: none !important;
        }

        #back_button {
            background-color: rgb(0, 87, 95) !important;
        }

        .delete-button-from-list {
            display: inline-block;
            overflow: hidden;
            border: 0px solid black;
            white-space: nowrap;
            text-overflow: ellipsis;
            color: white;
            text-transform: uppercase;
            background-color: rgb(222, 240, 241);
            outline: none !important;
            padding: 20px 20px;
            font-weight: bold;
        }

        .delete-button-from-list:hover {
            background: rgb(0, 87, 95);
            transition: all;
            transition-duration: 300ms;
        }

    </style>

@endsection

@section('extra_js')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.js" type="text/javascript"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>

    <script src="/assets/js/datetime.js" charset="utf-8"></script>
    <script src="/assets/js/safeNumericInput.js?t={{time()}}" charset="utf-8"></script>

    <script>
        $(function () {
            $('.datepicker').datepicker({
                format: 'yyyy-mm-dd',
                //startDate: '0d'
            });

            $('.timepicker').datetimepicker({
                format: 'HH:mm',
            });
        })

        var step = 0;
        var next_step = 1;
        var expert;

        // refresh expert list after selecting a country
        $("#country_id").on('change', function() {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'GET',
                url: '/ajax/get-outsource-experts',
                data: {
                    country_id: $(this).val()
                },
                success: function(data) {
                    select = $('#expert_id');
                    select.empty();

                    $.each(data, function(index, expert) {
                        select.append('<option value='+expert.user_id+'>'+expert.user_name+'</option>');
                    });
                }
            });
        });

        $("button#next_button").click(function (event) {
            if(next_step == 13 && expert && expert['is_cgp_employee']) {
                next_step++;
            }

            var original_next_step = next_step;

            var company_name = $("#company_id option:selected").text();
            var company_id = $("#company_id option:selected").val();
            const period_start = $("#company_id option:selected").attr('period-start');
            const period_end = $("#company_id option:selected").attr('period-end');
            const country_id = $("#company_id option:selected").attr('country_id');

            if (original_next_step == 1 && company_id) {
                $("#selected_company_name").text(company_name);
                $("#current-step").text("- {{ __('crisis.activity_id') }}");
                step++;
                next_step++;
                $('#quick_save_button').removeClass('d-none');

                var crisis_interventions = @json($crisis_interventions);

                var found_crisis_interventions = $.grep(crisis_interventions, function (v) {
                    return (v.country_id == country_id) && (v.company_id == company_id) && (new Date(v.created_at.split(' ')[0]).getTime() <= new Date(period_end).getTime()
                        && new Date(v.created_at.split(' ')[0]).getTime() >= new Date(period_start).getTime());
                });

                $("#activity_id").empty();

                for (var i = 0; i < found_crisis_interventions.length; i++) {
                    var option = found_crisis_interventions[i];
                    $("#activity_id").append("<option value='" + option['activity_id'] + "'>" + option[
                        'activity_id'] + "</option>");
                }
            }

            var activity_id = $("#activity_id option:selected").text();
            var activity_id_val = $("#activity_id option:selected").val();

            if (original_next_step == 2 && activity_id_val) {
                $("#selected_activity_id").text(activity_id);
                $("#current-step").text("- {{ __('crisis.company_email') }}");
                step++;
                next_step++;
            }

            var company_contact_email = document.getElementById("company_contact_email").value;
            $("#company_contact_email").text(company_contact_email);

            if (original_next_step == 3) {
                $("#selected_company_contact_email").text(company_contact_email);
                $("#current-step").text("- {{ __('crisis.company_phone') }}");
                step++;
                next_step++;
            }

            var company_contact_phone = document.getElementById("company_contact_phone").value;

            if (original_next_step == 4) {
                $("#selected_company_contact_phone").text(company_contact_phone);
                $("#current-step").text("- {{ __('crisis.country') }}");
                step++;
                next_step++;
            }

            var country_name = $("#country_id option:selected").text();

            if (original_next_step == 5) {
                $("#selected_country_name").text(country_name);
                $("#current-step").text("- {{ __('crisis.city') }}");
                step++;
                next_step++;
            }

            var city_name = $("#city_id option:selected").text();
            var city_id = $("#city_id option:selected").val();

            if (original_next_step == 6) {
                $("#selected_city_name").text(city_name);
                $("#current-step").text("- {{ __('crisis.expert') }}");
                step++;
                next_step++;
            }

            var expert_name = $("#expert_id option:selected").text();
            var expert_id = $("#expert_id option:selected").val();

            if (original_next_step == 7) {
                if (expert_id) {
                    var experts = @json($experts);
                    expert = experts.find(item => item.user_id == expert_id);
                    var mail = expert['user_email'];
                    $("#selected_expert_name").text(expert_name);
                    $("#selected_expert_mail").text(mail);

                    if(!(expert['currency'] == null || expert['currency'] == '')){
                        $("#currency option").each(function () {
                            $(this).hide();
                        });

                        $("#currency option[value='" + expert['currency'] + "']").show().prop('selected', true);

                        $("#currency option").each(function () {
                            if (!$(this).is(':selected')) {
                                $(this).remove();
                            }
                        });
                    }else{
                        $("#currency option").each(function () {
                            $(this).show();
                        });
                    }
                }
                $("#current-step").text("- {{ __('crisis.expert_phone') }}");
                step++;
                next_step++;
            }

            var expert_phone = document.getElementById("expert_phone").value;

            if (original_next_step == 8) {
                $("#selected_expert_phone").text(expert_phone);
                $("#current-step").text("- {{ __('crisis.date') }}");
                step++;
                next_step++;
            }

            var date = document.getElementById("date").value;

            if (original_next_step == 9) {
                $("#selected_date").text(date);
                $("#current-step").text("- {{ __('crisis.start_time') }}");
                step++;
                next_step++;
            }


            $("#start_time").on("dp.change", function (e) {
                $('#end_time').prop('disabled', false);

                $('#end_time').data("DateTimePicker").minDate(e.date);
                $("#end_time").data("DateTimePicker").date(null);
            });

            const start_time = $('#start_time').val();

            if (original_next_step == 10) {
                $("#selected_start_time").text(start_time);
                $("#current-step").text("- {{ __('crisis.end_time') }}");
                step++;
                next_step++;
            }

            const end_time = $('#end_time').val();

            if (start_time && end_time) {
                const start_date = new Date();
                const end_date = new Date();

                start_date.setHours(start_time.split(':')[0]);
                start_date.setMinutes(start_time.split(':')[1]);
                end_date.setHours(end_time.split(':')[0]);
                end_date.setMinutes(end_time.split(':')[1]);

                const full_time = new Date((((end_date.getTime() - start_date.getTime()) / 1000)) * 1000)
                    .toISOString().substr(11, 5);

                const readable = readableTime(full_time);

                if (readable[0]) {
                    $("#hour").css('display', 'inherit');
                    $("#hour span:first-child").text(readable[0]);
                } else {
                    $("#hour").css('display', 'none');
                }

                if (readable[1]) {
                    $("#minute").css('display', 'inherit');
                    $("#minute span:first-child").text(readable[1]);
                } else {
                    $("#minute").css('display', 'none');
                }

                $("#full_time").val(full_time);
                $("#selected_full_time").text(full_time);
            }

            if (original_next_step == 11) {
                $("#selected_end_time").text(end_time);
                $("#current-step").text("- {{ __('crisis.select_theme') }}");
                step++;
                next_step++;
            }

            var company_contact_name = document.getElementById("company_contact_name").value;

            if (original_next_step == 12) {
                $("#selected_company_contact_name").text(company_contact_name);
                $("#current-step").text("- {{ __('crisis.language') }}");
                $("#expert_mail").text(mail);
                step++;
                next_step++;
            }

            var language = $("#language").val();

            if (original_next_step == 13) {
                $("#selected_language").text(language);
                $("#current-step").text("- {{ __('crisis.expert_out_price') }}");
                step++;
                next_step++;
            }

            var price = $("#price").val();
            var currency = $("#currency").val();


            if (step !== 0) {
                $("#back_button").removeClass("inactive-button").addClass("active-button");
                $("#next_button").removeClass("inactive-button").addClass("active-button");
            }

            if (original_next_step == 14) {
                step++;
                next_step++;

                $("#next_button").removeClass("active-button").addClass("inactive-button");
                $("#save_button").removeClass("inactive-button").addClass("active-button");
            }


            $("div#step-" + step).removeClass("active").addClass("inactive");
            $("div#step-" + next_step).removeClass("inactive").addClass("active");
        });

        $("button#back_button").click(function () {
            if (step == 14 && expert && expert['is_cgp_employee']) {
                step--;
            }

            $("div#step-" + next_step).removeClass("active").addClass("inactive");
            $("div#step-" + step).removeClass("inactive").addClass("active");

            step--;
            next_step = step + 1;

            if (step < 1) {
                $('#quick_save_button').addClass('d-none');
            }

            if (step == 0) {
                $("#back_button").removeClass("active-button").addClass("inactive-button");
            } else {
                $("#next_button").removeClass("inactive-button").addClass("active-button");
            }
        });

        function readableTime(time) {
            const readable = [];

            time.split(':').forEach(function (unit) {
                let formatted = null;
                if (unit !== '00') {
                    if (unit[0] == '0') {
                        formatted = unit.substring(1, unit.length);
                    } else {
                        formatted = unit;
                    }
                }
                readable.push(formatted);
            });

            return readable;
        }

    </script>
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            {{ Breadcrumbs::render('crisis.create') }}
            <h1>{{ __('crisis.new_crisis') }} <span id="current-step">-
                    {{ __('crisis.company_name') }}</span><span id="all-steps">/</span></h1>
        </div>

        <form method="post" class="col-12 col-lg-8" name="case-create">
            {{ csrf_field() }}
            <div class="new-case-buttons row">
                <div class="steps d-block col-12" style="height: 64px!important">
                    <button type="button" id="back_button"
                            class="col-12 col-lg-2 mb-1 mt-1 mb-lg-0 mt-lg-0  next-button inactive-button btn-radius"
                            style="--btn-min-width:auto; --btn-margin-right: 0px; --btn-height: 100%;
                            --btn-margin-bottom: 0px;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 40px;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m18.75 4.5-7.5 7.5 7.5 7.5m-6-15L5.25 12l7.5 7.5" />
                        </svg>
                    </button>
                    <div class="active" id="step-1">
                        <select name="company_id" id="company_id" class="col-12 col-lg-6 h-100">
                            <option value="">{{ __('crisis.select_company_pls') }}</option>
                            @foreach ($active_companies as $active_company)
                                <option period-start="{{$active_company->period_start}}"
                                        period-end="{{$active_company->period_end}}"
                                        country_id="{{$active_company->country_id}}"
                                        value="{{ $active_company->company_id }}">{{ $active_company->company_name }}
                                    - {{ $active_company->country_code }} -
                                    <small>{{ $active_company->crisis_number }} {{__('crisis.available_crisis')}}</small>
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="inactive" id="step-2">
                        <select name="activity_id" id="activity_id" class="col-12 col-lg-6 h-100">
                        </select>
                    </div>
                    <div class="inactive" id="step-3">
                        <input type="text" name="company_contact_email" id="company_contact_email"
                               placeholder="{{ __('crisis.company_email') }}" class="col-12 col-lg-6 h-100">
                    </div>
                    <div class="inactive" id="step-4">
                        <input type="text" name="company_contact_phone" id="company_contact_phone"
                               placeholder="{{ __('crisis.company_phone') }}" class="col-12 col-lg-6 h-100">
                    </div>
                    <div class="inactive" id="step-5">
                        <select name="country_id" id="country_id" class="col-12 col-lg-6 h-100">
                            <option value="">{{ __('crisis.country') }}</option>
                            @foreach ($countries as $country)
                                <option value="{{ $country->id }}">{{ $country->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="inactive" id="step-6">
                        <select name="city_id" id="city_id" class="col-12 col-lg-6 h-100">
                            <option value="">{{ __('crisis.city') }}</option>
                            @foreach ($cities as $city)
                                <option value="{{ $city->id }}">{{ $city->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="inactive" id="step-7">
                        <select name="expert_id" id="expert_id" class="col-12 col-lg-6 h-100">
                            <option value="">{{ __('crisis.expert') }}</option>
                            @foreach ($experts as $expert)
                                <option value="{{ $expert->user_id }}">{{ $expert->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="inactive" id="step-8">
                        <input type="text" name="expert_phone" id="expert_phone"
                               placeholder="{{ __('crisis.expert_phone') }}" class="col-12 col-lg-6 h-100">
                    </div>
                    <div class="inactive" id="step-9">
                        <input type="text" name="date" id="date"
                               placeholder="{{ __('crisis.date') }}" class="datepicker col-12 col-lg-6 h-100">
                    </div>
                    <div class="inactive" id="step-10">
                        <input type="text" name="start_time" id="start_time"
                               placeholder="{{ __('crisis.start_time') }}" class="timepicker col-12 col-lg-6 h-100">
                    </div>
                    <div class="inactive" id="step-11">
                        <input type="text" name="end_time" id="end_time"
                               placeholder="{{ __('crisis.end_time') }}" class="timepicker col-12 col-lg-6 h-100">
                        <input type="hidden" name="full_time" id="full_time">
                    </div>
                    <div class="inactive" id="step-12">
                        <input type="text" name="company_contact_name" id="company_contact_name"
                               placeholder="{{ __('crisis.company_contact_name') }}" class="col-12 col-lg-6 h-100">
                    </div>
                    <div class="inactive" id="step-13">
                        <input type="text" name="language" id="language"
                               placeholder="{{ __('crisis.select_language') }}" class="col-12 col-lg-6 h-100">
                    </div>
                    <div class="inactive" id="step-14">
                        <input type="number" name="expert_price" id="expert_price"
                               placeholder="{{ __('crisis.expert_out_price') }}" class="price col-12 col-lg-3 h-100">
                        <select name="expert_currency" id="expert_currency"
                                class="valuta col-12 mt-1 mb-lg-0 mt-lg-0 col-lg-2 h-100">
                            <option value="">Valuta</option>
                            <option value="chf">CHF</option>
                            <option value="czk">CZK</option>
                            <option value="eur">EUR</option>
                            <option value="huf">HUF</option>
                            <option value="mdl">MDL</option>
                            <option value="oal">OAL</option>
                            <option value="pln">PLN</option>
                            <option value="ron">RON</option>
                            <option value="rsd">RSD</option>
                            <option value="usd">USD</option>
                        </select>
                    </div>
                    <div class="inactive" id="step-15">
                        <button type="submit" id="save_button"
                                class="next-button inactive-button btn-radius col-12 col-lg-6 mb-1 mb-lg-0 mt-lg-0 h-100" 
                                style="--btn-margin-right: 0px; vertical-align: baseline!important">
                                {{ __('crisis.save_crisis') }}
                            </button>
                    </div>
                    <button type="button" id="next_button"
                            class="next-button active-button col-12 col-lg-2 mb-1 mt-1 mb-lg-0 mt-lg-0 btn-radius"
                            style="--btn-min-width:auto; --btn-height: 100%; --btn-margin-bottom: 0px;
                            --btn-margin-right: 0px;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:40px">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m5.25 4.5 7.5 7.5-7.5 7.5m6-15 7.5 7.5-7.5 7.5" />
                        </svg>         
                    </button>

                    <button id="quick_save_button" type="submit"
                            class="col-12 col-lg-2 mb-1 mb-lg-0 mt-lg-0 delete-button-from-list d-none btn-radius"
                            style="--btn-min-width:110px; --btn-margin-right: 0px; --btn-height: 100%;
                            --btn-margin-bottom: 0px;">
                            <span class="mt-1">{{__('common.save')}}</span>
                    </button>
                </div>
            </div>
            <div id="" class="d-none">
                <p>{{ __('common.input-is-') }}</p>
            </div>
        </form>
        <div class="col-12 col-lg-4">
            <div id="permissions" class="right-side">
                <p class="title">{{ __('crisis.crisis_data') }}:</p>
                <div class="crisis-data">
                    <span>{{ __('crisis.company_name') }}: <span style="color: rgb(0,87,95)"
                                                                 id="selected_company_name"></span></span><br>
                    <span>{{ __('crisis.activity_id') }}: <span style="color: rgb(0,87,95)"
                                                                id="selected_activity_id"></span></span><br>
                    <span>{{ __('crisis.company_email') }}: <span style="color: rgb(0,87,95)"
                                                                  id="selected_company_contact_email"></span></span><br>
                    <span>{{ __('crisis.company_phone') }}: <span style="color: rgb(0,87,95)"
                                                                  id="selected_company_contact_phone"></span></span><br>
                    <span>{{ __('crisis.country') }}: <span style="color: rgb(0,87,95)"
                                                            id="selected_country_name"></span></span><br>
                    <span>{{ __('crisis.city') }}: <span style="color: rgb(0,87,95)" id="selected_city_name"></span></span><br>
                    <span>{{ __('crisis.expert') }}: <span style="color: rgb(0,87,95)"
                                                           id="selected_expert_name"></span></span><br>
                    <span>{{ __('crisis.expert_email') }}: <span style="color: rgb(0,87,95)"
                                                                 id="selected_expert_mail"></span></span><br>
                    <span>{{ __('crisis.expert_phone') }}: <span style="color: rgb(0,87,95)"
                                                                 id="selected_expert_phone"></span></span><br>
                    <span>{{ __('crisis.date') }}: <span style="color: rgb(0,87,95)" id="selected_date"></span></span><br>
                    <span>{{ __('crisis.start_time') }}: <span style="color: rgb(0,87,95)"
                                                               id="selected_start_time"></span></span><br>
                    <span>{{ __('crisis.end_time') }}: <span style="color: rgb(0,87,95)"
                                                             id="selected_end_time"></span></span><br>
                    <span>{{ __('crisis.full_time') }}:
                        <span style="color: rgb(0,87,95)" id="selected_full_time">
                            <span id="selected_hour" style="display: none">
                                <span></span>
                                {{__('crisis.hour')}}
                            </span>
                            <span id="selected_minute" style="display: none">
                                <span></span>
                                {{__('crisis.minute')}}
                            </span>
                        </span>
                    </span>
                    <br>
                    <span>{{ __('crisis.company_contact_name') }}: <span style="color: rgb(0,87,95)"
                                                                         id="selected_company_contact_name"></span></span><br>
                    <span>{{ __('crisis.language') }}: <span style="color: rgb(0,87,95)"
                                                             id="selected_language"></span></span><br>
                </div>
            </div>
        </div>
    </div>
@endsection
