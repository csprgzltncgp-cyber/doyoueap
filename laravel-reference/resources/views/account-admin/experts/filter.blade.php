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
    <script>
         $(function () {
            arrowClick();
        });

        function arrowClick() {
            $('.filter-button').click(function () {
                console.log('as');
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
    <div class="row mt-5">
        <div class="col-12">
            <h1>{{__("common.search")}}</h1>
            <form method="get" class="row" action="{{route('account_admin.experts.filter-result')}}">
                <div class="filter-holder col-6">
                    <div class="filter">
                        <p>{{__("eap-online.footer.menu_points.name")}}</p>
                        <button type="button" class="filter-button">
                            <svg xmlns="http://www.w3.org/2000/svg" style="height: 30px" fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                            </svg>
                        </button>
                        <div class="options d-none">
                            <input type="text" name="name" placeholder="{{__("eap-online.footer.menu_points.name")}}">
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
                                @foreach($cities as $city)
                                    <option value="{{$city->id}}">{{$city->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="filter-holder col-6">
                    <div class="filter">
                        <p>{{__('common.areas-of-experties')}}</p>
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
                                @foreach($permissions as $permission)
                                    <option value="{{$permission->id}}">{{$permission->translation->value}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-12 mt-5 mb-5 d-flex justify-content-between align-items-end">
                    <a class="back-button" href="{{route('account_admin.experts.index')}}">{{__('common.back-to-list')}}</a>
                    <button type="submit" class="button btn-radius" style="--btn-margin-right: 0px;">
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
