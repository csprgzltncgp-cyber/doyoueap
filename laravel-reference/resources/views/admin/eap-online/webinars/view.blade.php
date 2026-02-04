@extends('layout.master')

@section('title')
    Admin Dashboard
@endsection

@section('extra_css')
    <link rel="stylesheet" href="/assets/css/list.css?v={{time()}}">
    <link rel="stylesheet" href="/assets/css/form.css?v={{time()}}">
    <link rel="stylesheet" href="/assets/css/cases/view.css?t=<?php echo e(time()); ?>">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.css"
          rel="stylesheet"/>
    <link rel="stylesheet" href="/assets/css/cases/datetime.css?t={{time()}}">
    <link rel="stylesheet" href="/assets/css/eap-online/articles.css?v={{time()}}">
    </style>
@endsection

@section('extra_js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.js" type="text/javascript"></script>
    <script
            src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>

    <script src="/assets/js/datetime.js" charset="utf-8"></script>
    <script src="/assets/js/eap-online/webinars.js?v={{time()}}" charset="utf-8"></script>
    <script src="/assets/js/eap-online/article_video.js?v={{time()}}" charset="utf-8"></script>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            {{ Breadcrumbs::render('eap-online.webinars.edit', $webinar) }}
            <h1>{{__('eap-online.webinars.edit')}}</h1>
        </div>
        <div class="col-12">
            <form id="saveForm" action="{{route('admin.eap-online.webinars.edit', ['id' => $webinar->id])}}" method="post"
                  class="mb-5"
                  style="max-width: 100%"
                  enctype="multipart/form-data">
                {{csrf_field()}}
                <div class="row d-flex flex-column col-12">
                    <h1 class="mb-3">{{__('eap-online.articles.appearance')}} <span id="visibility_alt"
                                class="h5 {{ ($webinar->eap_visibility->burnout_page || $webinar->eap_visibility->domestic_violence_page) ? 'd-none' : '' }}">{{__('eap-online.videos.visibility_alt')}}</span></h1>
                    <select name="visibility">
                        <option value="null">{{__('eap-online.articles.none')}}</option>
                        <option value="theme_of_the_month"
                                @if($webinar->eap_visibility->theme_of_the_month) selected @endif>{{__('eap-online.articles.theme_of_the_month')}}</option>
                        <option value="home_page"
                                @if($webinar->eap_visibility->home_page) selected @endif>{{__('eap-online.articles.home_page')}}</option>
                        <option value="burnout_page"
                                @if($webinar->eap_visibility->burnout_page) selected @endif>{{__('eap-online.articles.burnout_page')}}</option>
                        <option value="domestic_violence_page"
                                @if($webinar->eap_visibility->domestic_violence_page) selected @endif>{{__('eap-online.articles.domestic_violence_page')}}</option>
                    </select>
                    <div id="apperance-more"
                         class="@if(!($webinar->eap_visibility->theme_of_the_month || $webinar->eap_visibility->home_page)) d-none @endif">
                        <div id="date_picker"
                             class="@if($webinar->eap_visibility->theme_of_the_month)d-flex @else d-none @endif flex-column mb-4 ml-n2"
                             onclick="openModal('modal-date-picker')"
                             style="cursor: pointer; width: 300px">
                            <div class="d-flex align-items-center">
                                <svg id="calendar_image"
                                     xmlns="http://www.w3.org/2000/svg" class="mr-1 ml-1"
                                     style="@if($errors->has('start_date') || $errors->has('end_date')) color: #db0b20; @else color: rgb(89, 198, 198); @endif width: 20px; height: 20px"
                                     fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>

                                <span class="pt-1"
                                      id="visibility_date">
                                    @if(empty($webinar->eap_visibility->to_date))
                                        <span class="pt-1"
                                              id="visibility_date">{{__('eap-online.articles.select_date')}}</span>
                                    @else
                                        <span class="pt-1"
                                              id="visibility_date">{{$webinar->eap_visibility->from_date}} - {{$webinar->eap_visibility->to_date}}</span>
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="categorize" class="row {{ ($webinar->eap_visibility->burnout_page || $webinar->eap_visibility->domestic_violence_page) ? 'd-none' : 'd-flex' }} flex-column col-12">
                    <h1 class="mb-3">{{__('eap-online.articles.categorize')}}</h1>
                    <div>
                        <div class="list-element col-12 group" onClick="toggleCategories(1, this)">
                            {{__('eap-online.articles.categorize_self-help')}}
                            <div class="d-flex align-items-center">
                                <span class="caret-left float-right">
                                    <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </span>
                            </div>
                        </div>
                        <div class="col-12 list-el d-none pl-0" data-category="1">
                            <ul class="border-0 d-inline-flex w-100 mb-2 pb-0 pt-0 mt-4 pl-0"
                                style="color: black !important; list-style: none;">
                                @foreach(\App\Models\EapOnline\EapCategory::whereNull('parent_id')->where('type', 'self-help')->with('childs')->get() as $category)
                                    <li class="flex-grow-1">
                                        <label class="container pb-2"
                                               id="customer-satisfaction-not-possible">{{$category->name}}
                                            <input type="radio" name="categories[]{{$loop->index}}"
                                                   value="{{$category->id}}"
                                                   @if($webinar->hasCategory($category->id))
                                                   checked="checked"
                                                    @endif>
                                            <span class="checkmark"></span>
                                        </label>
                                        @if(count($category->childs))
                                            @include('components.eap-online.category-line ',['childs' => $category->childs, 'level' => 1, 'resource' => $webinar, 'type' => $loop->index])
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="list-element col-12 group" onClick="toggleCategories(2, this)">
                            {{__('eap-online.articles.after_assessment')}}
                            <div class="d-flex align-items-center">
                                <span class="caret-left float-right">
                                    <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </span>
                            </div>
                        </div>
                        <div class="col-12 d-none list-el pl-0" data-category="2">
                            <ul class="border-0 d-inline-flex w-100 mb-2 pb-0 pt-0 mt-4 pl-0"
                                style="color: black !important; list-style: none;">
                                @foreach(\App\Models\EapOnline\EapCategory::whereNull('parent_id')->where('type', 'eap-assessment')->with('childs')->get() as $category)
                                    <li class="flex-grow-1">
                                        <label class="container pb-2"
                                               id="customer-satisfaction-not-possible">{{$category->name}}
                                            <input type="checkbox" name="categories[]{{$loop->index}}"
                                                   value="{{$category->id}}"
                                                   @if($webinar->hasCategory($category->id))
                                                   checked="checked"
                                                    @endif>
                                            <span class="checkmark"></span>
                                        </label>
                                        @if(count($category->childs))
                                            @include('components.eap-online.category-line ',['childs' => $category->childs, 'level' => 1, 'resource' => $webinar, 'type' => $loop->index])
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="list-element col-12 group" onClick="toggleCategories(4, this)">
                            {{__('eap-online.articles.well-being')}}
                            <div class="d-flex align-items-center">
                                <span class="caret-left float-right">
                                    <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
</svg>
                                </span>
                            </div>
                        </div>
                        <div class="col-12 d-none list-el pl-0" data-category="4">
                            <ul class="border-0 d-inline-flex w-100 mb-2 pb-0 pt-0 mt-4 pl-0"
                                style="color: black !important; list-style: none;">
                                @foreach(\App\Models\EapOnline\EapCategory::whereNull('parent_id')->where('type', 'well-being')->with('childs')->get() as $category)
                                    <li class="flex-grow-1">
                                        <label class="container pb-2"
                                               id="customer-satisfaction-not-possible">{{$category->name}}
                                            <input type="checkbox" name="categories[]{{$loop->index}}"
                                                   value="{{$category->id}}"
                                                   @if($webinar->hasCategory($category->id))
                                                   checked="checked"
                                                    @endif>
                                            <span class="checkmark"></span>
                                        </label>
                                        @if(count($category->childs))
                                            @include('components.eap-online.category-line ',['childs' => $category->childs, 'level' => 1, 'resource' => $webinar, 'type' => $loop->index])
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <div id="main_category"
                             class="list-element col-12 group @if($errors->has('categories')) error @endif"
                             onClick="toggleCategories(3, this)">
                            {{__('eap-online.webinars.category_title')}}
                            <div class="d-flex align-items-center">
                                <span class="caret-left float-right">
                                    <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
</svg>
                                </span>
                            </div>
                        </div>

                        <div class="col-12 d-none list-el pl-0" data-category="3">
                            <ul class="border-0 d-inline-flex w-100 mb-2 pb-0 pt-0 mt-4 pl-0"
                                style="color: black !important; list-style: none;">
                                @foreach(\App\Models\EapOnline\EapCategory::whereNull('parent_id')->where('type', 'all-webinars')->with('childs')->get() as $category)
                                    <li class="flex-grow-1">
                                        <label class="container pb-2"
                                               id="customer-satisfaction-not-possible">{{$category->name}}
                                            <input type="checkbox" name="categories[]{{$loop->index}}"
                                                   value="{{$category->id}}"
                                                   @if($webinar->hasCategory($category->id))
                                                   checked="checked"
                                                    @endif>
                                            <span onclick="removeError('error','main_category')"
                                                  class="checkmark"></span>
                                        </label>
                                        @if(count($category->childs))
                                            @include('components.eap-online.category-line ',['childs' => $category->childs, 'level' => 1, 'resource' => $webinar, 'type' => $loop->index])
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>

                <div id="lesson" class="row {{ $webinar->eap_visibility->burnout_page ? 'd-flex' : 'd-none' }} flex-column col-12">
                    <h1 class="mb-3">{{__('eap-online.articles.lesson')}}</h1>
                    <select name="lesson">
                        <option value="1"
                                @if(optional($webinar->lesson)->value == '1') selected @endif>{{__('eap-online.articles.lesson_1')}}</option>
                        <option value="2"
                                @if(optional($webinar->lesson)->value == '2') selected @endif>{{__('eap-online.articles.lesson_2')}}</option>
                        <option value="3"
                                @if(optional($webinar->lesson)->value == '3') selected @endif>{{__('eap-online.articles.lesson_3')}}</option>
                        <option value="4"
                                @if(optional($webinar->lesson)->value == '4') selected @endif>{{__('eap-online.articles.lesson_4')}}</option>
                        <option value="5"
                                @if(optional($webinar->lesson)->value == '5') selected @endif>{{__('eap-online.articles.lesson_5')}}</option>
                        <option value="6"
                                @if(optional($webinar->lesson)->value == '6') selected @endif>{{__('eap-online.articles.lesson_6')}}</option>
                        <option value="7"
                                @if(optional($webinar->lesson)->value == '7') selected @endif>{{__('eap-online.articles.lesson_7')}}</option>
                    </select>
                </div>

                <div id="chapter" class="row {{ $webinar->eap_visibility->domestic_violence_page ? 'd-flex' : 'd-none' }} flex-column col-12">
                    <h1 class="mb-3">{{__('eap-online.articles.chapter')}}</h1>
                    <select name="chapter">
                        <option value="1"
                                @if(optional($webinar->chapter)->value == '1') selected @endif>{{__('eap-online.articles.chapter_1')}}</option>
                        <option value="2"
                                @if(optional($webinar->chapter)->value == '2') selected @endif>{{__('eap-online.articles.chapter_2')}}</option>
                        <option value="3"
                                @if(optional($webinar->chapter)->value == '3') selected @endif>{{__('eap-online.articles.chapter_3')}}</option>
                        <option value="4"
                                @if(optional($webinar->chapter)->value == '4') selected @endif>{{__('eap-online.articles.chapter_4')}}</option>
                        <option value="5"
                                @if(optional($webinar->chapter)->value == '5') selected @endif>{{__('eap-online.articles.chapter_5')}}</option>
                        <option value="6"
                                @if(optional($webinar->chapter)->value == '6') selected @endif>{{__('eap-online.articles.chapter_6')}}</option>
                    </select>
                </div>

                <div class="row d-flex col-7">
                    <h1 id="link"
                        class="@if($errors->has('link')) error-text @endif">{{__('eap-online.webinars.link')}}</h1>
                    <input oninput="removeError('error-text', 'link')" type="text" placeholder="link..." name="link"
                           value="@if($webinar->link) {{$webinar->link}} @endif">
                </div>
                <div class="row d-flex col-7">
                    <h1 id="short_title"
                        class="@if($errors->has('short_title')) error-text @endif">{{__('eap-online.webinars.short_title')}}</h1>
                    <input oninput="removeError('error-text', 'short_title')" type="text"
                           placeholder="{{__('eap-online.webinars.title_placeholder')}}" name="short_title"
                           value="@if($webinar->short_title) {{$webinar->short_title}}@endif">
                </div>
                <div class="row d-flex col-7">
                    <h1 id="long_title"
                        class="@if($errors->has('long_title')) error-text @endif">{{__('eap-online.webinars.long_title')}}</h1>
                    <input oninput="removeError('error-text', 'long_title')" type="text"
                           placeholder="{{__('eap-online.webinars.title_placeholder')}}" name="long_title"
                           value="@if($webinar->long_title) {{$webinar->long_title}}@endif">
                </div>
                <div class="row d-flex col-7">
                    <h1 id="description_first_line"
                        class="@if($errors->has('description_first_line')) error-text @endif">{{__('eap-online.webinars.description_first_line')}}</h1>
                    <input oninput="removeError('error-text', 'description_first_line')" type="text"
                           placeholder="{{__('eap-online.webinars.description_placeholder')}}"
                           name="description_first_line"
                           value="@if($webinar->description_first_line) {{$webinar->description_first_line}}@endif">
                </div>
                <div class="row d-flex col-7 ">
                    <h1 id="description_second_line"
                        class="@if($errors->has('description_second_line')) error-text @endif">{{__('eap-online.webinars.description_second_line')}}</h1>
                    <input oninput="removeError('error-text', 'description_second_line')" type="text"
                           placeholder="{{__('eap-online.webinars.description_placeholder')}}"
                           name="description_second_line"
                           value="@if($webinar->description_second_line) {{$webinar->description_second_line}}@endif">
                </div>

                <div class="row mt-5">
                    <div class="col-12 d-flex">
                        <div>
                            <button class="text-center btn-radius" type="submit" form="saveForm">
                                <img class="mr-1" src="{{asset('assets/img/save.svg')}}" style="height: 20px; width: 20px" alt="">
                                <span class="mt-1">{{__('common.save')}}</span>
                            </button>
                        </div>
                        <div>
                            <button class="text-center btn-radius"
                                    style="--btn-min-width: auto;"
                                    type="button"
                                    onclick="deleteResource()"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" style="height: 20px; margin-bottom: 3px"
                                     fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <input type="hidden" name="start_date"
                       value="{{!empty($webinar->eap_visibility->from_date) ? $webinar->eap_visibility->from_date : ''}}">
                <input type="hidden" name="end_date"
                       value="{{!empty($webinar->eap_visibility->to_date) ? $webinar->eap_visibility->to_date : ''}}">
            </form>
        </div>
    </div>

    <form method="post" id="deleteForm"
          action="{{route('admin.eap-online.webinars.delete', ['id' => $webinar->id])}}">{{csrf_field()}}</form>
@endsection

@section('modal')
    <div class="modal" tabindex="-1" id="modal-date-picker" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{__('eap-online.articles.select_date')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="text" name="from_date" class="datepicker w-25"
                           placeholder="{{__('common.from')}}"
                           value="{{!empty($webinar->eap_visibility->from_date) ?  $webinar->eap_visibility->from_date : ''}}">
                    -
                    <input type="text" name="to_date" class="datepicker w-25" placeholder="{{__('common.to')}}"
                           value="{{!empty($webinar->eap_visibility->from_date) ?  $webinar->eap_visibility->from_date : ''}}">
                    <button class="button mr-3 float-right" onclick="saveDate()">
                        {{__('common.select')}}
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
