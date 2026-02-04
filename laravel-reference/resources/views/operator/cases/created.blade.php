@extends('layout.master')

@section('title')
    Operator Dashboard
@endsection

@section('extra_css')
    <link rel="stylesheet" href="{{asset('assets/css/cases/new.css')}}?t={{time()}}">
    <link rel="stylesheet" href="{{asset('assets/css/cases/datetime.css')}}?t={{time()}}">
@endsection

@section('extra_js')
    <script>

        $( document ).ready(function() {
            @if($case->case_type->value != 17 && $case->company->id != config('companies.deutsche-telekom') && !in_array(auth()->user()->country_id, config('lifeworks-countries')))
                Swal.fire({
                    icon: 'success',
                    title: '{{__("common.case-successfully-assigned")}}!',
                });
            @endif
        });

        function lifeWorksMail(case_id, element){
            $(element).html('{{__("common.processing")}}...');

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: '/ajax/email-to-lifeworks',
                data: {
                    case_id: case_id,
                },
                success: function (data) {
                    if (data.status == 0) {
                        $(element).html(`<svg xmlns="http://www.w3.org/2000/svg" style="height:20px; width:20px;" class="mr-1 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
  <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
</svg>{{__("common.email-sent-successfully")}}!`);

                         Swal.fire({
                            icon: 'success',
                            title: '{{__("common.email-sent-successfully")}}!',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    }else{
                        Swal.fire({
                            icon: 'error',
                            title: '{{__("common.email-sent-failed")}}!',
                            showConfirmButton: false,
                            timer: 1500
                        });

                    }
                },
                error: function (error) {
                    $(element).html('{{__("common.error-occured")}}!');

                    Swal.fire({
                            icon: 'error',
                            title: '{{__("common.email-sent-failed")}}!',
                            showConfirmButton: false,
                            timer: 1500
                        });
                }
            });
        }

        function expertEmail(case_id, expert_id, element) {
            $(element).html('{{__("common.processing")}}...');
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: '/ajax/email-to-expert',
                data: {
                    case_id: case_id,
                    expert_id: expert_id
                },
                success: function (data) {
                    if (data.status == 0) {
                        $(element).html(`<svg xmlns="http://www.w3.org/2000/svg" style="height:20px; width:20px;" class="mr-1 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
  <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
</svg>{{__("common.email-sent-successfully")}}!`);

                         Swal.fire({
                            icon: 'success',
                            title: '{{__("common.email-sent-successfully")}}!',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    }else{
                        Swal.fire({
                            icon: 'error',
                            title: '{{__("common.email-sent-failed")}}!',
                            showConfirmButton: false,
                            timer: 1500
                        });

                    }
                },
                error: function (error) {
                    $(element).html('{{__("common.error-occured")}}!');

                    Swal.fire({
                            icon: 'error',
                            title: '{{__("common.email-sent-failed")}}!',
                            showConfirmButton: false,
                            timer: 1500
                        });
                }
            });
        }

        function assign_single_session_expert(case_id, expert_id)
        {
            Swal.fire({
                title: "{{ __('common.set-customer-satisfaction') }}",
                text: "{{ __('common.case-finalization-text') }}",
                input: 'select',
                inputOptions: {
                    '1': '1',
                    '2': '2',
                    '3': '3',
                    '4': '4',
                    '5': '5',
                    '6': '6',
                    '7': '7',
                    '8': '8',
                    '9': '9',
                    '10': '10',
                },
                inputPlaceholder: "{{ __('common.choose-a-rating') }}",
                showCancelButton: false,
                inputValidator: (value) => {
                    return new Promise((resolve) => {
                        if (value) {
                            resolve()
                        } else {
                            resolve("{{ __('common.rating-required') }}")
                        }
                    })
                }  
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: 'POST',
                        url: '/ajax/assign-single-session-expert',
                        data: {
                            case_id: case_id,
                            expert_id: expert_id,
                            customer_satisfaction: result.value
                        },
                        success: function (data) {
                            window.location.href = "{{route('operator.dashboard')}}";
                        }
                    });
                }
            });
        }
    </script>
@endsection

@section('content')
    <div class="row" style="padding-top:80px;">
        <div class="col-12 col-lg-4" id="success">
            <div style="margin-bottom: 30px;">
                @if($case->case_type->value != 17 && $case->company->id != config('companies.deutsche-telekom'))
                    <p>{{__('common.case-successfully-assigned')}}</p>
                @else
                    <p>{{__('common.case-successfully-recorded')}}</p>
                @endif
                <a class="btn-radius d-flex" style="--btn-margin-right: 0px" href="{{route('operator.cases.new')}}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="height: 20px; width: 20px" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>
                        {{__('common.new-case')}}
                    </span>
                </a>
            </div>
        </div>
        <div class="col-12 col-lg-4">
            <div class="right-side">
                <div class="col-12 case-details">
                    <ul>
                        <li>{{__('common.summarization')}}:</li>
                        @foreach($case->values as $key => $value)
                            @if ($case->case_type->value != 1 &&  $value->input->id == 66)
                                <!-- Skip specialization -->
                            @else
                                <li>
                                    <span class="font-weight-bold">{{$value->input->translation->value}} {{strpos($value->input->translation->value,'?') === FALSE ? ':' : null}}</span> {{$value->getValue()}}
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-4">
            <div id="permissions" class="right-side">
                <p class="title">{{__('common.authorizations')}}:</p>
                @foreach($case->company->permissions as $permission)
                    <div class="permission">
                        <p>{{$permission->translation->value}}</p>
                        <p>{{__('common.'.$permission->pivot->contact)}}</p>
                        <p>{{$permission->pivot->number}} {{__('common.occasion')}}</p>
                    </div>
                @endforeach
            </div>
            @if($case->case_type->value == 17 || in_array(auth()->user()->country_id, config('lifeworks-countries')))
                <div id="experts" class="right-side">
                    <p class="title">{{__('common.available-experts')}}:</p>
                    {{--
                        When the:
                    
                        - Case company is not in cgp-case-company.php
                        - Current user(operator)'s country is a Lifeworks/Telus country (lifeworks-countries.php) or user's company is PreZero Recycling AB (1283) AND country is Sweden (48)

                        Then send case to Lifeworks/Telus
                    --}}
                    @if(!in_array($case->company_id, config('cgp-case-company')) &&
                        (in_array(auth()->user()->country_id, config('lifeworks-countries')) || (auth()->user()->country_id === 48 && $case->company_id === 1283)) )
                        <div class="expert">
                            <span>{{trans('workshop.close_workshop')}}</span>
                            <button class="expert-email"
                                    onClick="lifeWorksMail({{$case->id}}, this)">{{__('common.send-mail')}}</button>
                        </div>
                    @else
                        @foreach($case->getAvailableExperts() as $expert)
                            <div class="expert">
                                <span>
                                    {{$expert->name}}
                                </span>
                                @if ($case->case_type->value == 17)
                                    <button class="expert-email ml-1"
                                        onClick="assign_single_session_expert({{$case->id}},{{$expert->id}}, this)">{{__('common.assign-to-expert')}}</button>
                                @else
                                    <button class="expert-email"
                                        onClick="expertEmail({{$case->id}},{{$expert->id}}, this)">{{__('common.send-mail')}}</button>
                                @endif
                            </div>
                        @endforeach
                    @endif
                </div>
            @endif
        </div>
    </div>
@endsection
