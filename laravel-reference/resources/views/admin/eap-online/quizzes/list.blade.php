@extends('layout.master')

@section('title')
    Admin Dashboard
@endsection

@section('extra_css')
    <link rel="stylesheet" href="/assets/css/cases/view.css?t={{time()}}">
    <link rel="stylesheet" href="/assets/css/list.css?v={{time()}}">
    <link rel="stylesheet" href="/assets/css/workshops.css?v={{time()}}">
    <link rel="stylesheet" href="/assets/css/eap-online/master.css?v={{time()}}">
@endsection

@section('extra_js')
    <script>
        $(function () {
            getQuizzes();

            $('#search').on('input', function (e) {
                if (e.target.value.length >= 2 || e.target.value.length == 0) {
                    getQuizzes(e.target.value)
                }
            });

            $(document).ajaxStop(function () {
                $('#quizzes_holder').removeClass('d-none');
                $('#spinner').addClass('d-none');
            });

            $(document).on('click', '.pagination a', function (e) {
                getQuizzes($('#search').val(), $(this).attr('href').split('page=')[1])
                e.preventDefault();
            });
        });

        function resetSearch() {
            $("#search").val("");
            getQuizzes();
        }

        function getQuizzes(needle = '', page) {
            let url;
            if (page) {
                url = `/ajax/get-quizzes?page=${page}`
            } else {
                url = `/ajax/get-quizzes`
            }

            $('#quizzes_holder').addClass('d-none');
            $('#spinner').removeClass('d-none');
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'GET',
                url: url,
                data: {
                    needle,
                    translation: '{{Route::is('admin.eap-online.quizzes.translate.list')}}'
                },
                success: function (data) {
                    $('#quizzes_holder').html(data);
                },
            });
        }
    </script>
@endsection

@section('content')
    <div class="row">
        <div class="col-12  mb-5 d-flex flex-column align-items-start">
            <div class="w-100 flex flex-col align-items-center col-12 p-0">
                @if(Route::is('admin.eap-online.quizzes.list'))
                    <div>
                        {{ Breadcrumbs::render('eap-online.quizzes') }}
                        <h1>EAP online - {{__('eap-online.quizzes.menu')}}</h1>
                        <a href="{{route('admin.eap-online.quizzes.new_view')}}">{{__('eap-online.quizzes.new')}}</a>
                    </div>
                @endif
                @if(Route::is('admin.eap-online.quizzes.translate.list'))
                    {{ Breadcrumbs::render('eap-online.translate-quizzes') }}
                    <h1>EAP online - {{__('eap-online.quizzes.translate')}}</h1>
                @endif
                <div class="d-flex mb-4 mt-3">
                    @if(Route::is('admin.eap-online.quizzes.list'))
                        <a href="{{route('admin.eap-online.filter.view', ['model' => 'quizzes'])}}" id="filter"
                           class="btn-radius d-flex flex-row justify-content-center align-items-center"
                           style="--btn-height: auto; --btn-margin-bottom: 0px;">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="height: 20px; margin-bottom: 3px" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                            </svg>
                            {{__('workshop.apply_filter')}}</a>
                    @endif
                    <input class="mb-0 flex-shrink-1 col-4" type="text" id="search"
                           placeholder="{{__('eap-online.system.search')}}">
                    <button class="ml-3 flex-grow-1 btn-radius d-flex flex-row justify-content-center align-items-center btn-max-width" id="reset_search"
                        style="--btn-height: auto; --btn-margin-left: var(--btn-margin-x); --btn-margin-bottom: 0px;"
                        onClick="resetSearch()">
                        <div class="d-flex flex-row">
                            <img src="{{asset('assets/img/reset.svg')}}" class="mr-1" style="height: 20px; width: 20px" alt="">
                            {{__('eap-online.system.reset')}}
                        </div>
                    </button>
                </div>
            </div>
        </div>
        <div class="col-12" id="quizzes_holder"></div>
        <img id="spinner" class="d-none mx-auto" src="{{asset('assets/img/spinner.svg')}}" alt="spinner">
    </div>
    <div class="row col-4 col-lg-2 back-button mb-5">
        <a href="{{ route('admin.eap-online.actions') }}">{{__('common.back-to-list')}}</a>
    </div>
@endsection
