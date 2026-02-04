@extends('layout.master')

@section('title')
    Operator Dashboard
@endsection

@section('extra_css')
    <link rel="stylesheet" href="/assets/css/filter.css?v={{time()}}">
    <link rel="stylesheet" href="/assets/css/cases/datetime.css?t={{time()}}">
@endsection

@section('extra_js')
    <script src="/assets/js/datetime.js" charset="utf-8"></script>
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
    <div class="row mt-5">
        <div class="col-12">
            <h1>{{__('workshop.filter')}}</h1>
            <form method="post" class="row" action="{{route('operator.eap-online.mails.filter.result')}}">
                @csrf
                <div class="filter-holder col-6">
                    <div class="filter">
                        <p>{{__('eap-online.users.username')}}</p>
                        <button type="button" class="filter-button"><svg xmlns="http://www.w3.org/2000/svg" style="height: 30px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                </svg></button>
                        <div class="options d-none">
                            <select name="username">
                                <option value="">{{__('workshop.select')}}</option>
                                @foreach($users as $user)
                                    <option value="{{$user->id}}">{{$user->username}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="filter-holder col-6">
                    <div class="filter">
                        <p>{{__('workshop.company_name')}}</p>
                        <button type="button" class="filter-button"><svg xmlns="http://www.w3.org/2000/svg" style="height: 30px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                </svg></button>
                        <div class="options d-none">
                            <select name="company">
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
                        <p>{{__('workshop.date')}}</p>
                        <button type="button" class="filter-button"><svg xmlns="http://www.w3.org/2000/svg" style="height: 30px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                </svg></button>
                        <div class="options d-none">
                            <input type="text" name="date[]" class="datepicker w-25 mr-5"
                                   placeholder="{{__('common.from')}}">
                            <input type="text" name="date[]" class="datepicker w-25" placeholder="{{__('common.to')}}">
                        </div>
                    </div>
                </div>

                <div class="col-12 mt-5 mb-5 d-flex justify-content-between align-items-end">
                    <a class="back-button"
                       href="{{ route('operator.eap-online.mails.list') }}">{{__('common.back-to-list')}}</a>
                    <button type="submit" class="button btn-radius">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="height:20px; width:20px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg> {{__('workshop.filter')}}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
