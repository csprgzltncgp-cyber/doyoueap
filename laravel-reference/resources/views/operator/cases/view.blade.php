@extends('layout.master')

@section('title')
    Operator Dashboard
@endsection

@section('extra_js')
    <script src="/assets/js/datetime.js" charset="utf-8"></script>
    <script>
        $(function () {
            assignExpert();
        })
        const case_id = {{$case->id}};

        $(function () {
            $('.datepicker').datepicker({
                'format': 'yyyy-mm-dd'
            });
            case_input_change_form();
        });

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
                        $('#expert-assing-button').html('{{__("common.expert-outsourced")}}: ' + name);
                        $('#expert-did-not-assign').remove();
                    }
                });
            });
        }

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
                        $(element).html(
                            `<svg xmlns="http://www.w3.org/2000/svg" style="height:20px; width:20px;" class="mr-1 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>{{__("common.email-sent-successfully")}}!`
                        );

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
    </script>
@endsection

@section('extra_css')
    <link rel="stylesheet" href="/assets/css/cases/view.css?t={{time()}}">
    <link rel="stylesheet" href="/assets/css/cases/datetime.css?t={{time()}}">
@endsection

@section('content')
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
                    <form method="POST" action="{{route('operator.cases.generate-new-cases')}}">
                        @csrf
                        <Label>Szakértő kiválasztása</Label>
                        <select name="experts" style="width: 100%;">
                            @foreach($case->getAvailableExperts() as $expert)
                                <option value="{{$expert->id}}">{{$expert->name}}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="old_case_id" value="{{$case->id}}">
                        <button class="button mr-0 float-right"></button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    {{--        end hidden post helper conten--}}
    <div class="row">
        <div class="col-12">
            <h1>{{__('common.case-view')}}</h1>
        </div>
        <div class="col-12 case-title">
            <p>{{$case->created_at}} - {{$case->company != null ? $case->company->name : ''}}
                - {{$case->case_accepted_expert() ? $case->case_accepted_expert()->name : ''}}
                - {{$case->case_type != null ? $case->case_type->getValue() : null}}
                - {{$case->case_location != null ? $case->case_location->getValue() : null}}
                - {{$case->case_client_name != null ? $case->case_client_name->getValue() : null}}</p>
        </div>
        <div class="col-12 case-details">
            <ul>
                <li>{{__('common.status')}}: {{$case->status}}</li>
                <li>{{__('common.id')}}: {{$case->case_identifier}}</li>
                @foreach($case->values as $value)
                    @if($value->input)
                        @if ($case->case_type->value != 1 && $value->input->default_type == 'case_specialization')
                            <!-- Skip specialization -->
                        @else
                            <li>
                                @if($value->input->default_type != 'company_chooser' && $value->input->default_type != 'company_chooser' /*&& $value->input->default_type != 'location'*/
                                && $value->input->default_type != 'case_type' && $value->input->default_type != 'case_specialization' && $value->input->default_type != 'case_language_skill')
                                    @if($value->input->default_type == 'case_creation_time')
                                        <button data-toggle="modal" data-target="#case_input_{{$value->input->id}}">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-1"
                                                style="heigth:20px; width:20px" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                            {{$value->input->translation->value}}: <span
                                                    id="case_input_{{$value->input->id}}_value_holder">{{$case->created_at}}</span>
                                        </button>
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
                                        <button data-toggle="modal" data-target="#case_input_{{$value->input->id}}">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-1"
                                                style="heigth:20px; width:20px" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                            {{$value->input->translation->value}}: <span
                                                    id="case_input_{{$value->input->id}}_value_holder">{{$value->getValue()}}</span>
                                        </button>
                                    @endif
                                @else
                                    <span class="not-editable">{{$value->input->translation->value}}: <span
                                    id="case_input_{{$value->input->id}}_value_holder">{{$value->getValue()}}</span></span>
                                @endif
                            </li>
                        @endif
                    @endif
                @endforeach
                @if($case->case_accepted_expert())
                    <li>
                        <button data-toggle="modal" data-target="#experts" id="expert-assing-button">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="heigth:20px; width:20px"
                                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg> {{__('common.expert-outsourced')}}: {{$case->case_accepted_expert()->name}}</button>
                    </li>
                @else
                    <li>
                        <button data-toggle="modal" data-target="#experts" id="expert-assing-button">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="heigth:20px; width:20px"
                                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg> {{__('common.outsource-new-expert')}}</button>
                    </li>
                @endif

            </ul>
        </div>
        <div class="col-12 button-holder">
            {{--      <button data-toggle="modal" data-target="#ccase-re-generate" id="ccase-re-generate-button" class="button float-right ml-3">Új esetlap létrehozása</button>--}}

            <a href="#" class="button d-none">{{__('common.save')}}</a>
            <a href="{{route('operator.cases.edit',['id' => $case->id])}}"
               class="button d-none">{{__('common.edit')}}</a>
            <a href="#" class="button d-none"></a>
            @if($case->experts->first() && $case->experts->first()->pivot->accepted == App\Enums\CaseExpertStatus::REJECTED->value)
                <a class="button btn-radius d-flex" id="expert-did-not-assign">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="height:20px; width:20px" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg> {{__('common.expert-rejected-case')}}!
                    <svg xmlns="http://www.w3.org/2000/svg" class="ml-1" style="height:20px; width:20px" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </a>
            @endif
        </div>
        <div class="w-100 flex justify-content-end">
            <div class="button-holder">
                {{-- IF operator's country is a "lifeworks/telus" country
                    OR country is Sweden(48) and the case company is PreZero Recycling AB (1283)
                --}}
                @if(in_array(auth()->user()->country_id, config('lifeworks-countries')) || (auth()->user()->country_id === 48 && $case->company_id === 1283))
                    <div class="expert">
                        <button class="expert-email button btn-radius" onClick="lifeWorksMail({{$case->id}}, this)"
                            style="bg">
                            {{__('common.send_lifeworks_mail')}}
                        </button>
                    </div>
                @endif
            </div>
        </div>
        <div class="col-12 back-button mb-5">
            @if(
              $case->getRawOriginal('status') != 'confirmed' &&
              $case->getRawOriginal('status') != 'employee_contacted' &&
              $case->getRawOriginal('status') != 'client_unreachable_confirmed')
                <a href="{{route('operator.cases.in_progress')}}">{{__('common.back-to-list')}}</a>
            @else
                <a href="{{route('operator.cases.in_progress')}}">{{__('common.back-to-list')}}</a>
            @endif
        </div>
    </div>
@endsection

@section('modal')
    @foreach($case->values as $value)
        @if($value->input)
            @if($value->input->default_type != 'company_chooser' && $value->input->default_type != 'company_chooser' && $value->input->default_type != 'location' && $value->input->default_type != 'case_type'
            && $value->input->default_type != 'case_specialization' && $value->input->default_type != 'case_language_skill' && $value->input->default_type != 'clients_language')
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
                                                        @if($v->id == $value->value) selected @endif>{{$v->translation ? $v->translation->value : 'N/A'}}</option>
                                            @endforeach
                                        </select>
                                    @elseif($value->input->type == 'date')
                                        <input type="text" name="value" class="datepicker" value="{{$value->value}}"
                                               placeholder="{{$value->input->translation->value}}"/>
                                    @elseif($value->input->type == 'integer')
                                        <input type="number" name="value" value="{{$value->value}}"
                                               placeholder="{{$value->input->translation->value}}"/>
                                    @elseif($value->input->type == 'double')
                                        <input type="text" name="value" value="{{$value->value}}"
                                               placeholder="{{$value->input->translation->value}}"/>
                                    @elseif($value->input->type == 'text')
                                        {{--                    <input type="text" name="value" value="{{$value->value}}" placeholder="{{$value->input->translation->value}}"/>--}}
                                        <textarea name="value" cols="30" rows="10">{{$value->value}}</textarea>
                                    @endif
                                    <button class="btn-radius float-right" style="--btn-margin-right: 0px;">
                                        <img class="mr-1" src="{{asset('assets/img/save.svg')}}" class="mr-1" style="height: 20px; width: 20px" alt="">
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
                                    <select name="value">
                                        @foreach($case->country->cities->sortBy('name') as $city)
                                            <option value="{{$city->id}}"
                                                    @if($city->id == $value->value) selected @endif>{{$city->name}}</option>
                                        @endforeach
                                    </select>
                                    <button class="btn-radius">
                                        <img class="mr-1" src="{{asset('assets/img/save.svg')}}" class="mr-1" style="height: 20px; width: 20px" alt="">
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
                        <select class="w-100" name="experts">
                            @foreach($case->getAvailableExperts() as $expert)
                                <option value="{{$expert->id}}">{{$expert->name}}</option>
                            @endforeach
                        </select>
                        <button class="btn-radius float-right" style="--btn-margin-right: 0px;">
                            <img class="mr-1" src="{{asset('assets/img/save.svg')}}" class="mr-1" style="height: 20px; width: 20px" alt="">
                            <span class="mt-1">{{__('common.save')}}</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
