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
        $(function(){
            getArticles();

            $('#search').on('input', function (e) {
                if (e.target.value.length >= 2 || e.target.value.length == 0) {
                    getArticles(e.target.value)
                }
            });
        });

        function getArticles(needle = '', page) {
            const url = '/ajax/company-website/get-articles' + (page ? `?page=${page}` : '');

            $('#articles_holder').addClass('d-none');
            $('#spinner').removeClass('d-none');

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'GET',
                url: url,
                data: {
                    needle,
                    translation: {{Route::is('admin.company-website.articles.translation.index') ? 'true' : 'false'}}
                },
                success: function (data) {
                    $('#articles_holder').html(data);
                    $('#articles_holder').removeClass('d-none');
                    $('#spinner').addClass('d-none');
                },
                error: function (data) {
                    console.log(data);
                }
            });
        }
    </script>
@endsection

@section('content')
    <div class="row">
        <div class="col-12  mb-5 d-flex flex-column align-items-start">
            <div class="w-100 flex flex-col align-items-center col-12 p-0">
                @if(Route::is('admin.company-website.articles.index'))
                    <div>
                        {{ Breadcrumbs::render('company-website.articles') }}
                        <h1>{{__('company-website.menu')}} - {{ __('company-website.actions.articles.menu') }}</h1>
                        <a href="{{route('admin.company-website.articles.create')}}">{{__('eap-online.articles.new')}}</a>
                    </div>
                @endif

                @if(Route::is('admin.company-website.articles.translation.index'))
                    {{ Breadcrumbs::render('company-website.articles.translation') }}
                    <h1>{{__('company-website.menu')}} - {{__('company-website.actions.articles.translation')}}</h1>
                @endif

                <div class="d-flex mb-4 mt-3">
                    @if(Route::is('admin.eap-online.articles.list'))
                        <a href="{{route('admin.eap-online.filter.view', ['model' => 'articles'])}}" id="filter"
                           class="mr-3">
                            <svg xmlns="http://www.w3.org/2000/svg" style="height: 20px; margin-bottom: 3px" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                            </svg>
                            {{__('workshop.apply_filter')}}</a>
                    @endif
                    <input class="mb-0 flex-shrink-1 col-4" type="text" id="search"
                           placeholder="{{__('eap-online.system.search')}}">
                    <button class="ml-3 flex-grow-1 btn-radius" class="mr-1" style="--btn-height:auto; --btn-max-width: var(--btn-min-width); --btn-margin-bottom: 0px;" id="reset_search"
                            onClick="resetSearch()">
                        <img src="{{asset('assets/img/reset.svg')}}" class="mr-1" style="height: 20px; width: 20px" alt="">
                        <span>
                            {{__('eap-online.system.reset')}}
                        </span>
                    </button>
                </div>
            </div>
        </div>
        <div class="col-12" id="articles_holder"></div>
        <img id="spinner" class="d-none mx-auto" src="{{asset('assets/img/spinner.svg')}}" alt="spinner">
    </div>
    <div class="row col-4 col-lg-2 back-button mb-5">
        <a href="{{ route('admin.company-website.actions') }}">{{__('common.back-to-list')}}</a>
    </div>
@endsection
