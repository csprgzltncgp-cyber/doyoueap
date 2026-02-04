@extends('layout.master')

@section('title')
    Admin Dashboard
@endsection

@section('extra_css')
    <link rel="stylesheet" href="{{asset('assets/css/filter.css')}}?v={{time()}}">
    <link rel="stylesheet" href="{{asset('assets/css/cases/datetime.css')}}?t={{time()}}">
    <style>
        .back-button {
            text-transform: uppercase;
            font-weight: bold;
            color: rgb(0, 87, 93);
            margin-top: 30px;
            display: block;
        }
    </style>
@endsection

@section('extra_js')
    <script src="{{asset('assets/js/datetime.js')}}" charset="utf-8"></script>
    <script>
        $(function () {
            $('.datepicker').datepicker({
                'format': 'yyyy-mm-dd'
            });
            arrowClick();
        });

        function arrowClick() {
            $('.filter-button').click(function () {
                var options = $(this).closest('.filter').find('.options');
                options.toggleClass('d-none');
                if (options.hasClass('d-none')) {
                    $(this).find('i').removeClass('fa-arrow-up').addClass('fa-arrow-down');
                } else {
                    $(this).find('i').removeClass('fa-arrow-down').addClass('fa-arrow-up');
                }
            });
        }


    </script>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            {{ Breadcrumbs::render('other-activities.filter') }}
            <h1>{{__('workshop.filter')}}</h1>
            <form method="get" class="row" action="{{route('admin.other-activities.result')}}">
                <div class="filter-holder col-6">
                    <div class="filter">
                        <p>{{__('other-activity.activity_id')}}</p>
                        <button type="button" class="filter-button">
                            <svg xmlns="http://www.w3.org/2000/svg" style="height: 30px" fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                            </svg>
                        </button>
                        <div class="options d-none">
                            <input type="text" name="activity_id"
                                   placeholder="{{__('other-activity.activity_id')}}">
                        </div>
                    </div>
                </div>

                <div class="filter-holder col-6">
                    <div class="filter">
                        <p>{{__('workshop.expert')}}</p>
                        <button type="button" class="filter-button">
                            <svg xmlns="http://www.w3.org/2000/svg" style="height: 30px" fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                            </svg>
                        </button>
                        <div class="options d-none">
                            <select name="user_id">
                                <option value="">{{__('workshop.select')}}</option>
                                @foreach($experts as $expert)
                                    <option value="{{$expert->id}}">{{$expert->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="filter-holder col-6">
                    <div class="filter">
                        <p>{{__('workshop.company_name')}}</p>
                        <button type="button" class="filter-button">
                            <svg xmlns="http://www.w3.org/2000/svg" style="height: 30px" fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                            </svg>
                        </button>
                        <div class="options d-none">
                            <select name="company_id">
                                <option value="">{{__('workshop.select')}}</option>
                                @foreach($companies as $company)
                                    <option value="{{$company->id}}">{{$company->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="filter-holder col-6">
                    <div class="filter">
                        <p>{{__('workshop.status')}}</p>
                        <button type="button" class="filter-button">
                            <svg xmlns="http://www.w3.org/2000/svg" style="height: 30px" fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                            </svg>
                        </button>
                        <div class="options d-none">
                            <select name="status">
                                <option value="">{{__('workshop.select')}}</option>
                                <option value="1">{{__('workshop.under_agreement')}}</option>
                                <option value="2">{{__('workshop.active')}}</option>
                                <option value="3">{{__('workshop.closed')}}</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="filter-holder col-6">
                    <div class="filter">
                        <p>{{__('workshop.date')}}</p>
                        <button type="button" class="filter-button">
                            <svg xmlns="http://www.w3.org/2000/svg" style="height: 30px" fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                            </svg>
                        </button>
                        <div class="options d-none">
                            <input type="text" name="date[]" class="datepicker w-25 mr-5"
                                   placeholder="{{__('common.from')}}">
                            <input type="text" name="date[]" class="datepicker w-25"
                                   placeholder="{{__('common.to')}}">
                        </div>
                    </div>
                </div>


                <div class="col-12 pr-0 mt-5 mb-5 d-flex justify-content-between align-items-end">
                    <a class="back-button" href="{{ route('admin.other-activities.index') }}">{{__('common.back-to-list')}}</a>
                    <button type="submit" class="button btn-radius" style="--bnt-margin-right: 0px;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="height:20px; width:20px" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                        </svg> {{__('workshop.filter')}}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
