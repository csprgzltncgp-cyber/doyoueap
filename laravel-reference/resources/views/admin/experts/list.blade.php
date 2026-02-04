@extends('layout.master')

@section('extra_css')
    <link rel="stylesheet" href="{{asset('assets/css/list.css')}}?v={{time()}}">
    <link href="{{asset('assets/css/chosen.css')}}" rel="stylesheet" type="text/css">
    <style>
        .list-element {
            cursor: pointer;;
        }

        a.mail {
            margin-left: 10px;
        }

        .loginAs {
            background: transparent;
            border: 0px solid black;
            outline: none !important;
        }

        button.batchmail {
            border: 0px solid black;
            background: transparent;
            outline: none;
        }
    </style>
@endsection

@section('title')
    Admin Dashboard
@endsection

@section('extra_js')
    <script src="{{asset('assets/js/toggleActive.js')}}?v={{time()}}" charset="utf-8"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9.7.1/dist/sweetalert2.all.min.js"></script>
    <script>
        $(function () {
            $('[data-toggle="tooltip"]').tooltip();
        })
        function batchMail(id) {
            Swal.fire({
                title: 'Biztos, hogy ki szeretné küldeni a regisztrációs emaileket?',
                text: "{{__('common.operation-cannot-undone')}}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Igen, kiküldöm!',
                cancelButtonText: '{{__('common.cancel')}}',
            }).then(function (result) {
                if (result.value) {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: 'GET',
                        url: '/ajax/send-welcome-mail-to-country/' + id,
                        success: function (data) {
                            if (data.status == 0) {
                                Swal.fire(
                                    'Az emailek kiküldése sikeres!',
                                    '',
                                    'success'
                                );
                            }
                        },
                        error: function (error) {
                            Swal.fire(
                                'Az emailek kiküldése sikertelen!',
                                'SERVER ERROR!',
                                'error'
                            );
                        },
                    });
                }
            });
        }

        function loginAs(id) {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: '/ajax/login-as',
                data: {
                    id: id,
                    type: 'expert'
                },
                success: function (data) {
                    if (data.status == 0) {
                        window.location.replace(data.redirect);
                    }
                },
                error: function (error) {
                    Swal.fire(
                        'Az bejelentkezés kiküldése sikertelen!',
                        'SERVER ERROR!',
                        'error'
                    );
                }
            });
        }

        function resendEmail(id) {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: '/ajax/expert-registration-email-resend',
                data: {
                    id: id
                },
                success: function (data) {
                    if (data.status == 0) {
                        Swal.fire(
                            'Az email kiküldése sikeres!',
                            '',
                            'success'
                        );
                    } else {
                        Swal.fire(
                            'Az email kiküldése sikertelen!',
                            '',
                            'error'
                        );
                    }
                },
                error: function (error) {
                    Swal.fire(
                        'Az email kiküldése sikertelen!',
                        'SERVER ERROR!',
                        'error'
                    );
                }
            });
        }

        function deleteExpert(id, element) {
            Swal.fire({
                title: '{{__('common.are-you-sure-to-delete')}}',
                text: "{{__('common.operation-cannot-undone')}}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '{{__('common.yes-delete-it')}}',
                cancelButtonText: '{{__('common.cancel')}}',
                //}).then((result) => {
            }).then(function (result) {
                if (result.value) {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: 'DELETE',
                        url: '/ajax/delete-expert/' + id,
                        success: function (data) {
                            if (data.status == 0) {
                                $(element).closest('.list-element').remove();
                            }
                        }
                    });
                }
            });
        }

        function cancel_contract(id, element) {
            Swal.fire({
                title: "{{__('common.cancel_contract_warning')}}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: "{{__('common.yes')}}",
                cancelButtonText: "{{__('common.cancel')}}",
            }).then(function (result) {
                if (result.value) {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: 'DELETE',
                        url: '/ajax/cancel-expert-contract/' + id,
                        success: function (response) {
                            if (response == 1) {
                                $("#contract_status_"+id).text("{{__('common.lbl_contract_canceled')}}");
                                $("#contract_status_"+id).parent().addClass("deactivated");
                                $("#contract_status_"+id).html(`
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="height:20px; margin-bottom: 3px;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m6.75 12H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                    </svg>

                                  {{__('common.lbl_contract_canceled')}}
                                `);

                                Swal.fire({
                                    title: "{{__('common.cancel_contract_success')}}",
                                    icon: 'success',
                                    showCancelButton: false,
                                    confirmButtonText: "Ok",
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
    <div class="row m-0">
        {{ Breadcrumbs::render('experts') }}
        <h1 class="col-12 pl-0">{{__('common.list-of-experts')}}</h1>
        <a href="{{route(\Auth::user()->type . '.experts.new')}}"
           class="col-12 pl-0 d-block">{{__('common.add-new-expert')}}</a>
        @foreach($countries as $country)
            <div class="list-element case-list-in mb-0 col-12 group" onClick="toggleList({{$country->id}}, this, event)">
                {{$country->code}}
                <a class="mail"
                   href="mailto:{{implode(',',$activeExperts->where('country_id',$country->id)->pluck('email')->toArray())}}">
                    <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                         style="height:20px; margin-bottom: 3px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    {{__('common.send-mail')}}</a>
                <button class="caret-left float-right">
                    <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px;" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
            </div>
            @foreach($experts as $expert)
                @if($expert->isInCountry($country->id))
                    <div class="list-element col-12 d-none" data-country="{{$country->id}}">
                        <span>{{$expert->name}}</span>

                        <button class="float-right delete-button" onClick="deleteExpert({{$expert->id}}, this)">
                            <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                                 style="height:20px; margin-bottom: 3px" fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>

                        <button class="float-right  activate-button @if($expert->contract_canceled) deactivated @endif" onClick="cancel_contract({{$expert->id}}, this)" @if ($expert->contract_canceled) disabled @endif>
                            <span id="contract_status_{{$expert->id}}">
                                @if ($expert->contract_canceled)
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="height:20px; margin-bottom: 3px;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m6.75 12H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                    </svg>

                                    {{__('common.lbl_contract_canceled')}}
                                @else

                                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="height:20px; margin-bottom: 3px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.125 2.25h-4.5c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125v-9M10.125 2.25h.375a9 9 0 0 1 9 9v.375M10.125 2.25A3.375 3.375 0 0 1 13.5 5.625v1.5c0 .621.504 1.125 1.125 1.125h1.5a3.375 3.375 0 0 1 3.375 3.375M9 15l2.25 2.25L15 12" />
                                  </svg>

                                    {{__('common.btn_cancel_contract')}}
                                @endif
                            </span>
                        </button>
                        @if($expert->last_login_at)
                            <button @if($expert->inactivity) data-toggle="tooltip" data-placement="top" title="Inkatív: {{$expert->inactivity->until}}-ig" @endif class="float-right activate-button @if(!$expert->active) deactivated @endif"
                                    onClick="toggleActive({{$expert->id}}, this)">@if($expert->active)
                                    <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                                         style="height:20px; margin-bottom: 3px" fill="none" viewBox="0 0 24 24"
                                         stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    {{__('crisis.active')}}
                                @else
                                @if($expert->inactivity)
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="height:20px; margin-bottom: 3px">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                                  </svg>
                                @else
                                <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                                        style="height:20px; margin-bottom: 3px" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                @endif
                                  {{__('common.inactive')}}
                                @endif
                            </button>

                            <button class="float-right activate-button @if($expert->locked) deactivated @endif" onClick="toggleLocked({{$expert->id}}, this)">
                                @if(!$expert->locked)
                                    <svg xmlns="http://www.w3.org/2000/svg" style="height:20px; margin-bottom: 3px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z" />
                                    </svg>
                                    {{__('common.unlocked')}}
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" style="height:20px; margin-bottom: 3px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                    {{__('common.locked')}}
                                @endif
                            </button>
                        @else
                            <span class="float-right activate-button pending">
                                <svg class="mr-1"
                                    xmlns="http://www.w3.org/2000/svg"
                                    style="height:20px; margin-bottom: 3px"
                                    fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                {{__('common.pending')}}
                                <button onClick="resendEmail({{$expert->id}})" class="mail-resend-button">
                                    <svg
                                        class="mr-1"
                                        xmlns="http://www.w3.org/2000/svg"
                                        style="height:20px; margin-bottom: 3px"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    {{__('common.send-mail')}}
                                </button>
                            </span>
                        @endif
                        <a class="float-right"
                           href="{{route(\Auth::user()->type . '.experts.edit',['user' => $expert->id])}}">
                            <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                                 style="height:20px; margin-bottom: 3px" fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            {{__('common.edit')}}</a>
                        <button class="float-right loginAs" onClick="loginAs({{$expert->id}})">
                            <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                                 style="height:20px; margin-bottom: 3px" fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                            </svg>
                            {{__('common.login')}}
                        </button>
                        @if($expert->has_missing_expert_data())
                            <a class="float-right mr-2" class="float-right" style="color: rgb(219, 11, 32);"
                                href="{{route(\Auth::user()->type . '.experts.edit',['user' => $expert->id])}}">
                                <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                                    style="height:20px; margin-bottom: 3px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                  </svg>
                                {{__('common.expert_data_missing')}}
                            </a>
                        @else
                            <a class="float-right activate-button mr-2" class="float-right"
                            href="{{route(\Auth::user()->type . '.experts.edit',['user' => $expert->id])}}">
                            <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                            style="height:20px; margin-bottom: 3px"  fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                              </svg>
                            {{__('common.expert_data_ok')}}
                            </a>
                        @endif
                    </div>
                @endif
            @endforeach
        @endforeach
    </div>
@endsection
