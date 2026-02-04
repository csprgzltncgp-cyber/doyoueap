@extends('layout.master')

@section('title')
    Admin Dashboard
@endsection

@section('extra_css')
    <link rel="stylesheet" href="{{asset('assets/css/cases/list.css')}}?v={{time()}}">
    <link rel="stylesheet" href="{{asset('assets/css/workshops.css')}}?v={{time()}}">
    <link rel="stylesheet" href="/assets/css/form.css?v={{time()}}">
    <link rel="stylesheet" href="/assets/css/perfix-input.css?v={{time()}}">
    <link rel="stylesheet" href="{{ asset('/assets/css/invoice-helper/cgp-card.css') }}">
@endsection

@section('extra_js')
    <script src="{{asset('assets/js/business-breakfast.js')}}?v={{time()}}"></script>
@endsection

@section('content')
    <div>
        <div class="row">
            <div class="col-12">
                {{ Breadcrumbs::render('business-breakfast') }}
                <h1>Business Breakfast</h1>
            </div>

            <div class="col-12 case-list-holder">
                @foreach ($events as $year => $months)
                    <div class="case-list-in col-12 group" onclick="yearOpen({{$year}})">
                        {{$year}}
                        <button class="caret-left float-right">
                            <svg id="y{{$year}}" xmlns="http://www.w3.org/2000/svg"
                                 style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                 stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                    </div>
                    <div class="lis-element-div" id="{{$year}}">
                        @foreach ($months as $month => $events)
                            <div class="workshop-list-holder">
                                <div class="case-list-in col-12 group" onclick="monthOpen('{{$month}}')">
                                    {{$month}}
                                    <button class="caret-left float-right">
                                        <svg id="m{{$month}}" xmlns="http://www.w3.org/2000/svg"
                                             style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24"
                                             stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </button>
                                </div>

                                <div class="workshop-list d-none" id="{{$month}}">
                                        @foreach ($events as $event)
                                            <div class="case-list-in col-12 group" onclick="eventOpen('{{$event->id}}')">
                                                {{$event->date->format('Y-m-d')}} - {{$event->name}}
                                                <button class="caret-left float-right">
                                                    <svg id="e{{$event->id}}" xmlns="http://www.w3.org/2000/svg"
                                                         style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24"
                                                         stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                                    </svg>
                                                </button>
                                            </div>

                                            <div class="event-detail d-none" id="event-detail-{{$event->id}}">
                                                <form>
                                                    <div class="event-detail" id="event-detail-" style="width: 1000px;">
                                                        <h1 style="font-size: 18px; color:black; padding-top:0;">Interakciók:</h1>
                                                        <div class="form-row">
                                                            <div class="form-group col-md-3 mb-0">
                                                                <div class="input-group col-12 p-0 mb-0">
                                                                    <div class="input-group-prepend">
                                                                        <div class="input-group-text">
                                                                            A later date would be suitable:
                                                                        </div>
                                                                    </div>
                                                                    <input type="text" disabled="" readonly="">
                                                                </div>
                                                            </div>

                                                            <div class="form-group col-md-3 mb-0">
                                                                <div class="input-group col-12 p-0 ">
                                                                    <div class="input-group-prepend">
                                                                        <div class="input-group-text">
                                                                        </div>
                                                                    </div>
                                                                    <input type="text" readonly value="{{$event->later_date_count}}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-row">
                                                            <div class="form-group col-md-3 mb-0">
                                                                <div class="input-group col-12 p-0 mb-0">
                                                                    <div class="input-group-prepend">
                                                                        <div class="input-group-text">
                                                                            I'm not interested in this topic:
                                                                        </div>
                                                                    </div>
                                                                    <input type="text" disabled="" readonly="">
                                                                </div>
                                                            </div>

                                                            <div class="form-group col-md-3 mb-0">
                                                                <div class="input-group col-12 p-0 ">
                                                                    <div class="input-group-prepend">
                                                                        <div class="input-group-text">
                                                                        </div>
                                                                    </div>
                                                                    <input type="text" readonly value="{{$event->not_interested_count}}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-row">
                                                            <div class="form-group col-md-3 mb-0">
                                                                <div class="input-group col-12 p-0 mb-0">
                                                                    <div class="input-group-prepend">
                                                                        <div class="input-group-text">
                                                                            In the next 2-4 months:
                                                                        </div>
                                                                    </div>
                                                                    <input type="text" disabled="" readonly="">
                                                                </div>
                                                            </div>

                                                            <div class="form-group col-md-3 mb-0">
                                                                <div class="input-group col-12 p-0 ">
                                                                    <div class="input-group-prepend">
                                                                        <div class="input-group-text">
                                                                        </div>
                                                                    </div>
                                                                    <input type="text" readonly value="{{$event->next_2_4_months_count}}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-row">
                                                            <div class="form-group col-md-3 mb-0">
                                                                <div class="input-group col-12 p-0 mb-0">
                                                                    <div class="input-group-prepend">
                                                                        <div class="input-group-text">
                                                                            In the next 5-6 months:
                                                                        </div>
                                                                    </div>
                                                                    <input type="text" disabled="" readonly="">
                                                                </div>
                                                            </div>

                                                            <div class="form-group col-md-3 mb-0">
                                                                <div class="input-group col-12 p-0 ">
                                                                    <div class="input-group-prepend">
                                                                        <div class="input-group-text">
                                                                        </div>
                                                                    </div>
                                                                    <input type="text" readonly value="{{$event->next_5_6_months_count}}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-row">
                                                            <div class="form-group col-md-3 mb-0">
                                                                <div class="input-group col-12 p-0 mb-0">
                                                                    <div class="input-group-prepend">
                                                                        <div class="input-group-text">
                                                                            In the next 7-8 months:
                                                                        </div>
                                                                    </div>
                                                                    <input type="text" disabled="" readonly="">
                                                                </div>
                                                            </div>

                                                            <div class="form-group col-md-3 mb-0">
                                                                <div class="input-group col-12 p-0 ">
                                                                    <div class="input-group-prepend">
                                                                        <div class="input-group-text">
                                                                        </div>
                                                                    </div>
                                                                    <input type="text" readonly value="{{$event->next_7_8_months_count}}">
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <h1 style="font-size: 18px; color:black"></h1>
                                                        <div class="form-row mb-3 pb-3">
                                                            <div class="form-group col-md-3 mb-0">
                                                                <div class="green-box dark col-12">
                                                                    Foglalások:
                                                                </div>
                                                            </div>
                                                            <div class="form-group col-md-3 mb-0">
                                                                <div class="input-group col-12 p-0 mb-0 ">
                                                                    <div class="input-group-prepend">
                                                                        <div class="input-group-text">
                                                                        </div>
                                                                    </div>
                                                                    <input type="text" readonly value="{{$event->bookings_count}}">
                                                                </div>
                                                            </div>
                                                            <div class="form-group col-md-3 mb-0">
                                                                <a class="btn-radius" style="color:white; background:#59c6c6; width: 48px; --btn-height: 48px; --btn-min-width: auto; --btn-padding-x: 0px;" href="{{route('admin.business-breakfast.export-bookings', $event)}}" class="h-100 col-3 d-flex justify-content-center align-items-center">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                                      </svg>
                                                                </a>
                                                            </div>
                                                        </div>

                                                        <h1 style="font-size: 18px; color:black"></h1>
                                                        <div class="form-row mb-3 pb-3">
                                                            <div class="form-group col-md-3 mb-0">
                                                                <div class="green-box dark col-12">
                                                                    Értesítési kérelmek:
                                                                </div>
                                                            </div>
                                                            <div class="form-group col-md-3 mb-0">
                                                                <div class="input-group col-12 p-0 mb-0 ">
                                                                    <div class="input-group-prepend">
                                                                        <div class="input-group-text">
                                                                        </div>
                                                                    </div>
                                                                    <input type="text" readonly value="{{$event->notification_requests_count}}">
                                                                </div>
                                                            </div>

                                                            <div class="form-group col-md-3 mb-0">
                                                                <a style="color:white; background:#59c6c6; width: 48px; --btn-height: 48px; --btn-min-width: auto; --btn-padding-x: 0px;" href="{{route('admin.business-breakfast.export-notification-requests', $event)}}" class="btn-radius h-100 col-3 d-flex justify-content-center align-items-center">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                                      </svg>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
