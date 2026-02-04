@extends('layout.master')

@section('title')
    Admin Dashboard
@endsection

@section('extra_css')
    <link rel="stylesheet" href="/assets/css/filter.css?v={{time()}}">
    <link rel="stylesheet" href="/assets/css/cases/datetime.css?t={{time()}}">
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
            });
        }


    </script>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <h1>{{__('workshop.filter')}}</h1>
            <form method="post" class="row" action="{{route('admin.eap-online.filter.result', ['model' => $model])}}">
                @csrf
                <div class="filter-holder col-6">
                    <div class="filter">
                        <p>{{__('eap-online.articles.visibility')}}</p>
                        <button type="button" class="filter-button">
                            <svg xmlns="http://www.w3.org/2000/svg" style="height: 30px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                            </svg>
                        </button>
                        <div class="options d-none">
                            <select name="visibility">
                                <option value="">{{__('workshop.select')}}</option>
                                <option value="self_care">{{__('eap-online.articles.self_care')}}</option>
                                @if($model != 'quizzes')
                                    <option value="after_assessment">{{__('eap-online.articles.after_assessment')}}</option>
                                @endif
                                <option value="theme_of_the_month">{{__('eap-online.articles.theme_of_the_month')}}</option>
                                <option value="home_page">{{__('eap-online.articles.home_page')}}</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="filter-holder col-6">
                    <div class="filter">
                        <p>{{__('eap-online.articles.category')}}</p>
                        <button type="button" class="filter-button">
                            <svg xmlns="http://www.w3.org/2000/svg" style="height: 30px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                            </svg>
                        </button>
                        <div class="options d-none">
                            <select name="category">
                                <option value="">{{__('workshop.select')}}</option>
                                @foreach ($groups as $group_name => $categories)
                                    <optgroup label="{{$group_name}}">
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>

                                            @if (count($category->childs) > 0)
                                                @include('components.eap-online.subcategories_component', ['subcategories' => $category->childs, 'parent' => $category->name])
                                            @endif

                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="filter-holder col-6">
                    <div class="filter">
                        <p>{{__('workshop.date')}}</p>
                        <button type="button" class="filter-button">
                            <svg xmlns="http://www.w3.org/2000/svg" style="height: 30px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                            </svg>
                        </button>
                        <div class="options d-none">
                            <input type="text" name="date[]" class="datepicker w-25 mr-5"
                                   placeholder="{{__('common.from')}}">
                            <input type="text" name="date[]" class="datepicker w-25" placeholder="{{__('common.to')}}">
                        </div>
                    </div>
                </div>

                @if(isset($prefixes))
                    <div class="filter-holder col-6">
                        <div class="filter">
                            <p>{{__('eap-online.prefix.menu')}}</p>
                            <button type="button" class="filter-button">
                                <svg xmlns="http://www.w3.org/2000/svg" style="height: 30px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                                </svg>
                            </button>
                            <div class="options d-none">
                                <select name="prefix">
                                    <option value="">{{__('workshop.select')}}</option>
                                    @foreach($prefixes as $prefix)
                                        <option value="{{$prefix->id}}">{{$prefix->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                @endif

                @if($model == 'articles')
                    <div class="filter-holder col-6">
                        <div class="filter">
                            <p>{{__('eap-online.articles.appearance')}}</p>
                            <button type="button" class="filter-button">
                                <svg xmlns="http://www.w3.org/2000/svg" style="height: 30px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                                </svg>
                            </button>
                            <div class="options d-none">
                                <select name="type">
                                    <option value="">{{__('workshop.select')}}</option>
                                    <option value="editorial">{{__('eap-online.articles.editorial')}}</option>
                                    <option value="rovat">Rovat</option>
                                    <option value="sidebar_article">{{__('eap-online.articles.article_in_sidebar')}}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="col-12 mt-5 mb-5 d-flex justify-content-between align-items-end">
                    <a class="back-button"
                       href="{{ route('admin.eap-online.' . $model . '.list') }}">{{__('common.back-to-list')}}</a>
                    <button type="submit" class="button btn-radius">
                        <svg xmlns="http://www.w3.org/2000/svg" style="height: 20px; margin-bottom: 3px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        {{__('workshop.filter')}}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
