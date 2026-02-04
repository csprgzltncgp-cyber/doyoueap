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
            {{Breadcrumbs::render('affiliate-search-workflow.filter')}}
            <h1>{{__('workshop.filter')}}</h1>
            <form method="get" class="row" action="{{route( auth()->user()->type . '.affiliate_searches.filter-result')}}">
                <div class="filter-holder col-6">
                    <div class="filter">
                        <p>Id</p>
                        <button type="button" class="filter-button">
                            <svg xmlns="http://www.w3.org/2000/svg" style="height: 30px" fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                            </svg>
                        </button>
                        <div class="options d-none">
                            <input type="number" name="id"placeholder="Id">
                        </div>
                    </div>
                </div>

                 <div class="filter-holder col-6">
                    <div class="filter">
                        <p>{{__('task.colleague')}}</p>
                        <button type="button" class="filter-button">
                            <svg xmlns="http://www.w3.org/2000/svg" style="height: 30px" fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                            </svg>
                        </button>
                        <div class="options d-none">
                            <select name="from_id">
                                <option value="">{{__('workshop.select')}}</option>
                                @foreach($admins as $admin)
                                    <option value="{{$admin->id}}">{{$admin->name}}</option>
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
                                <option value="{{\App\Models\AffiliateSearch::STATUS_SEARCH_STARTED}}">{{__('affiliate-search-workflow.status.search_started')}}</option>
                                <option value="{{\App\Models\AffiliateSearch::STATUS_AFFILIATE_FOUND}}">{{__('affiliate-search-workflow.status.affiliate_found')}}</option>
                                <option value="{{\App\Models\AffiliateSearch::STATUS_AFFILIATE_CONTACTED}}">{{__('affiliate-search-workflow.status.affiliate_contacted')}}</option>
                                <option value="{{\App\Models\AffiliateSearch::STATUS_CONTRACT_SENT}}">{{__('affiliate-search-workflow.status.contract_sent')}}</option>
                                <option value="{{\App\Models\AffiliateSearch::STATUS_CONTRACT_SIGNED}}">{{__('affiliate-search-workflow.status.contract_signed')}}</option>
                                <option value="{{\App\Models\AffiliateSearch::STATUS_ACTIVE_ON_DASBOARD}}">{{__('affiliate-search-workflow.status.active_on_dashboard')}}</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="filter-holder col-6">
                    <div class="filter">
                        <p>{{__('common.country')}}</p>
                        <button type="button" class="filter-button">
                            <svg xmlns="http://www.w3.org/2000/svg" style="height: 30px" fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                            </svg>
                        </button>
                        <div class="options d-none">
                            <select name="country_id">
                                <option value="">{{__('workshop.select')}}</option>
                                @foreach ($countries as $country)
                                    <option value="{{$country->id}}">{{$country->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="filter-holder col-6">
                    <div class="filter">
                        <p>{{__('crisis.city')}}</p>
                        <button type="button" class="filter-button">
                            <svg xmlns="http://www.w3.org/2000/svg" style="height: 30px" fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                            </svg>
                        </button>
                        <div class="options d-none">
                            <select name="city_id">
                                <option value="">{{__('workshop.select')}}</option>
                                @foreach ($cities as $city)
                                    <option value="{{$city->id}}">{{$city->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="filter-holder col-6">
                    <div class="filter">
                        <p>{{__('affiliate-search-workflow.affiliate_type')}}</p>
                        <button type="button" class="filter-button">
                            <svg xmlns="http://www.w3.org/2000/svg" style="height: 30px" fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                            </svg>
                        </button>
                        <div class="options d-none">
                            <select name="permission_id">
                                <option value="">{{__('workshop.select')}}</option>
                                @foreach ($permissions as $permission)
                                    <option value="{{$permission->id}}">{{$permission->translation->value}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

               <div class="filter-holder col-6">
                    <div class="filter">
                        <p>{{__('task.deadline')}}</p>
                        <button type="button" class="filter-button">
                            <svg xmlns="http://www.w3.org/2000/svg" style="height: 30px" fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                            </svg>
                        </button>
                        <div class="options d-none">
                            <input type="text" name="deadline" class="datepicker"
                                   placeholder="{{__('task.deadline')}}">
                        </div>
                    </div>
                </div>


                <div class="col-12 mt-5 mb-5 d-flex justify-content-between align-items-end">
                    <a class="back-button" href="{{ route(auth()->user()->type . '.affiliate_searches.index') }}">{{__('common.back-to-list')}}</a>
                    <button type="submit" class="button btn-radius" style="--btn-margin-right: 0px">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="height:20px; width:20px;" fill="none"
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
