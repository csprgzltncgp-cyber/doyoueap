@extends('layout.master')

@section('title')
    Admin Dashboard
@endsection

@section('extra_css')
    <link rel="stylesheet" href="/assets/css/list.css?v={{time()}}">
    <link rel="stylesheet" href="/assets/css/form.css?v={{time()}}">
    <link rel="stylesheet" href="/assets/css/cases/view.css?t=<?php echo e(time()); ?>">
    <link rel="stylesheet" href="/assets/css/eap-online/articles.css?v={{time()}}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.css"
          rel="stylesheet"/>
    <link rel="stylesheet" href="/assets/css/cases/datetime.css?t={{time()}}">
@endsection

@section('extra_js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.js" type="text/javascript"></script>
    <script
            src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>

    <script src="/assets/js/datetime.js" charset="utf-8"></script>
    <script type="text/javascript">
        const headline_trans = "{{__('eap-online.articles.headline')}}";
        const language_trans = "{{__('eap-online.actions.language')}}";
        const lead_trans = "{{__('eap-online.articles.lead')}}";
        const body_trans = "{{__('eap-online.articles.body')}}";
        const date_trans = "{{__('crisis.date')}}";
        const highlight_trans = "{{__('eap-online.articles.highlight')}}";
        const list_trans = "{{__('eap-online.articles.list')}}";
        const subtitle_trans = "{{__('eap-online.articles.subtitle')}}"
        const list_alt_trans = "{{__('eap-online.articles.separate_lines_by_enter')}}";
        const delete_trans = "{{__('common.delete')}}";
        const picture_trans = "{{__('eap-online.articles.picture')}}";
        const document_trans = "{{__('eap-online.articles.document')}}";
        const picture_upload_trans = "{{__('eap-online.articles.thumbnail_button')}}";
        const file_upload_trans = "{{__('eap-online.videos.attachment_placeholder')}}";
        const required_trans = "{{__('eap-online.required')}}";
        const category_trans = "{{__('eap-online.articles.all_articles')}}"
        const appearance_trans = "{{(__('eap-online.articles.appearance'))}}";
        let main_categories = @json(\App\Models\EapOnline\EapCategory::where('type', 'all-articles')->get()->pluck('id')->toArray())
    </script>
    <script src="/assets/js/eap-online/article_video.js?v={{time()}}" charset="utf-8"></script>
    <script src="/assets/js/eap-online/validator.js?v={{time()}}" charset="utf-8"></script>
    <script src="/assets/js/eap-online/article_validator.js?v={{time()}}" charset="utf-8"></script>
    <script src="/assets/js/eap-online/articles.js?v={{time()}}" charset="utf-8"></script>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            {{ Breadcrumbs::render('eap-online.articles.create') }}
            <h1>{{ __('eap-online.articles.new') }}</h1>
        </div>
        <div class="col-12">
            <form class="mb-5" action="{{route('admin.eap-online.articles.new')}}" method="post"
                  style="max-width: 100%" enctype="multipart/form-data">
                {{csrf_field()}}

                <div class="row d-flex flex-column col-12">
                    <h1 class="mb-3">{{__('eap-online.actions.language')}}</h1>
                    <div class="col-3 row d-flex">
                        <button id="language-select-button"
                                class="@if($errors->has('language')) error @endif float-left btn-radius" type="button"
                                onclick="openModal('modal-language-select')">
                            @if(!empty(old('language')))
                                {{\App\Models\EapOnline\EapLanguage::find(intval(old('language')))->name}}
                            @else
                            <img src="{{asset('assets/img/language.svg')}}" class="mr-1" style="height: 20px; width: 20px" alt="">
                            <span>
                                {{__('workshop.select_language')}}
                            </span>
                            @endif
                        </button>
                    </div>
                </div>

                <div class="row d-flex flex-column col-12">
                    <h1 class="mb-3">{{__('eap-online.articles.appearance')}} <span id="visibility_alt"
                                class="h5">{{__('eap-online.articles.visibility_alt')}}</span></h1>
                    <select name="visibility">
                        <option value="null">{{__('eap-online.articles.none')}}</option>
                        <option value="theme_of_the_month"
                                @if(old('visibility') == 'theme_of_the_month') selected @endif>{{__('eap-online.articles.theme_of_the_month')}}</option>
                        <option value="home_page"
                                @if(old('visibility') == 'home_page') selected @endif>{{__('eap-online.articles.home_page')}}</option>
                        <option value="burnout_page"
                                @if(old('visibility') == 'burnout_page') selected @endif>{{__('eap-online.articles.burnout_page')}}</option>
                        <option value="domestic_violence_page"
                                @if(old('visibility') == 'domestic_violence_page') selected @endif>{{__('eap-online.articles.domestic_violence_page')}}</option>
                    </select>
                    <div id="apperance-more"
                         class="@if(!(old('visibility') == 'theme_of_the_month' || old('visibility') == 'home_page')) d-none @endif">
                        <div id="date_picker"
                             class="@if(old('visibility') == 'theme_of_the_month')d-flex @else d-none @endif flex-column mb-4 ml-n2"
                             onclick="openModal('modal-date-picker')"
                             style="cursor: pointer; width: 300px">
                            <div class="d-flex align-items-center">

                                <svg id="calendar_image" xmlns="http://www.w3.org/2000/svg" class="mr-1 ml-1"
                                     style="@if($errors->has('start_date') || $errors->has('end_date')) color: #db0b20; @else color: rgb(89, 198, 198); @endif width: 20px; height: 20px"
                                     fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>

                                <span class="pt-1"
                                      id="visibility_date">
                                    @if(!empty(old('start_date')) && !empty(old('end_date')))
                                        {{old('start_date')}} - {{old('end_date')}}
                                    @else
                                        {{__('eap-online.articles.select_date')}}
                                    @endif
                                </span>
                            </div>
                        </div>
                        <div>
                            <label class="container  checkbox-container"
                                   id="customer-satisfaction-not-possible">{{__('eap-online.articles.editorial')}}
                                <input type="radio" name="type" value="editorial"
                                       @if(!empty(old('type')) && old('type') == 'editorial') checked @endif
                                >
                                <span class="checkmark"></span>
                            </label>
                        </div>
                        <div class="mb-4 d-flex align-items-center">
                            <div class="mt-4">
                                <label class="container checkbox-container"
                                       id="customer-satisfaction-not-possible">Rovat
                                    <input type="radio" name="type" value="rovat"
                                           @if(!empty(old('type')) && old('type') == 'rovat') checked @endif>
                                    <span class="checkmark"></span>
                                </label>
                            </div>
                            <div id="prefix-container" class="d-none">
                                @foreach(\App\Models\EapOnline\EapPrefix::all() as $prefix)
                                    <div class="mt-4">
                                        <label class="container checkbox-container"
                                               id="customer-satisfaction-not-possible">{{$prefix->name}}
                                            <input type="radio" name="prefix" value="{{$prefix->id}}"
                                                   @if(!empty(old('prefix')) && old('prefix') == $prefix->id) checked @endif>
                                            <span class="checkmark @if($errors->has('prefix')) error @endif"></span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="container checkbox-container"
                                   id="customer-satisfaction-not-possible">{{__('eap-online.articles.article_in_sidebar')}}
                                <input type="radio" name="type" value="sidebar_article"
                                       @if(!empty(old('type')) && old('type') == 'sidebar_article') checked @endif>
                                <span class="checkmark"></span>
                            </label>
                        </div>
                    </div>
                </div>
                <div id="categorize" class="row d-flex flex-column col-12">
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
                                                   @if(!empty(old('categories')) && in_array($category->id , old('categories'))) checked @endif>
                                            <span class="checkmark"></span>
                                        </label>
                                        @if(count($category->childs))
                                            @include('components.eap-online.category-line ',['childs' => $category->childs, 'level' => 1, 'type' => $loop->index])
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
                                                   @if(!empty(old('categories')) && in_array($category->id , old('categories'))) checked @endif>
                                            <span class="checkmark"></span>
                                        </label>
                                        @if(count($category->childs))
                                            @include('components.eap-online.category-line ',['childs' => $category->childs, 'level' => 1, 'type' => $loop->index])
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
                                                   @if(!empty(old('categories')) && in_array($category->id , old('categories'))) checked @endif>
                                            <span class="checkmark"></span>
                                        </label>
                                        @if(count($category->childs))
                                            @include('components.eap-online.category-line ',['childs' => $category->childs, 'level' => 1, 'type' => $loop->index])
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <div id="main_category"
                             class="list-element col-12 group @if($errors->has('categories')) error @endif"
                             onClick="toggleCategories(3, this)">
                            {{__('eap-online.articles.all_articles')}}
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
                                @foreach(\App\Models\EapOnline\EapCategory::whereNull('parent_id')->where('type', 'all-articles')->with('childs')->get() as $category)
                                    @if($loop->index % 4)
                                        <br/>
                                    @endif
                                    <li class="flex-grow-1">
                                        <label class="container pb-2"
                                               id="customer-satisfaction-not-possible">{{$category->name}}
                                            <input type="checkbox" name="categories[]{{$loop->index}}"
                                                   value="{{$category->id}}"
                                                   @if(!empty(old('categories')) && in_array($category->id , old('categories'))) checked @endif>
                                            <span onclick="removeError('error','main_category')"
                                                  class="checkmark"></span>
                                        </label>
                                        @if(count($category->childs))
                                            @include('components.eap-online.category-line ',['childs' => $category->childs, 'level' => 1, 'type' => $loop->index])
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>

                <div id="lesson" class="row d-none flex-column col-12">
                    <h1 class="mb-3">{{__('eap-online.articles.lesson')}}</h1>
                    <select name="lesson">
                        <option value="1"
                                @if(old('lesson') == '1') selected @endif>{{__('eap-online.articles.lesson_1')}}</option>
                        <option value="2"
                                @if(old('lesson') == '2') selected @endif>{{__('eap-online.articles.lesson_2')}}</option>
                        <option value="3"
                                @if(old('lesson') == '3') selected @endif>{{__('eap-online.articles.lesson_3')}}</option>
                        <option value="4"
                                @if(old('lesson') == '4') selected @endif>{{__('eap-online.articles.lesson_4')}}</option>
                        <option value="5"
                                @if(old('lesson') == '5') selected @endif>{{__('eap-online.articles.lesson_5')}}</option>
                        <option value="6"
                                @if(old('lesson') == '6') selected @endif>{{__('eap-online.articles.lesson_6')}}</option>
                        <option value="7"
                                @if(old('lesson') == '7') selected @endif>{{__('eap-online.articles.lesson_7')}}</option>
                    </select>
                </div>

                <div id="chapter" class="row d-none flex-column col-12">
                    <h1 class="mb-3">{{__('eap-online.articles.chapter')}}</h1>
                    <select name="chapter">
                        <option value="1"
                                @if(old('chapter') == '1') selected @endif>{{__('eap-online.articles.chapter_1')}}</option>
                        <option value="2"
                                @if(old('chapter') == '2') selected @endif>{{__('eap-online.articles.chapter_2')}}</option>
                        <option value="3"
                                @if(old('chapter') == '3') selected @endif>{{__('eap-online.articles.chapter_3')}}</option>
                        <option value="4"
                                @if(old('chapter') == '4') selected @endif>{{__('eap-online.articles.chapter_4')}}</option>
                        <option value="5"
                                @if(old('chapter') == '5') selected @endif>{{__('eap-online.articles.chapter_5')}}</option>
                        <option value="6"
                                @if(old('chapter') == '6') selected @endif>{{__('eap-online.articles.chapter_6')}}</option>
                    </select>
                </div>

                <div class="row col-12 d-flex">
                    <div class="d-flex flex-column justify-content-center">
                        <div>
                            <h1>{{__('eap-online.articles.thumbnail')}}</h1>
                            <div class="d-flex align-items-center mb-3"
                                 style="cursor: pointer">

                                @if($errors->has('thumbnail'))
                                    <svg class="ml-n1 mr-1"
                                         onclick="triggerFileUpload('thumbnail-preview')"
                                         id="thumbnail-preview-input-file-upload-trigger"
                                         style="color: #db0b20; height: 20px; width: 20px; cursor: pointer"
                                         xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                         stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                @else
                                    <svg class="ml-n1 mr-1"
                                         onclick="triggerFileUpload('thumbnail-preview')"
                                         id="thumbnail-preview-input-file-upload-trigger"
                                         style="color: rgb(89, 198, 198); height: 20px; width: 20px; cursor: pointer"
                                         xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                         stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                @endif

                                @if($errors->has('thumbnail'))
                                    <svg onclick="deleteUploadedFile('thumbnail-preview-input')"
                                         class="ml-n1 d-none mr-1"
                                         id="thumbnail-preview-input-file-delete-trigger"
                                         xmlns="http://www.w3.org/2000/svg"
                                         style="color: #db0b20; height: 20px; width: 20px; cursor: pointer"
                                         fill="none"
                                         viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                @else
                                    <svg onclick="deleteUploadedFile('thumbnail-preview-input')"
                                         class="ml-n1 mr-1 d-none"
                                         id="thumbnail-preview-input-file-delete-trigger"
                                         xmlns="http://www.w3.org/2000/svg"
                                         style="color: rgb(89, 198, 198); height: 20px; width: 20px; cursor: pointer"
                                         fill="none"
                                         viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                @endif

                                <span id="thumbnail-preview-input-uploaded-file-name"
                                >{{__('eap-online.articles.thumbnail_button')}}</span>
                            </div>
                        </div>
                        <img class="d-none" src="#" alt="preview" id="thumbnail-preview-input-thumbnail-preview"
                             width="200px" style="border:2px solid #4dc0b5">
                    </div>
                    <input class="d-none" id="thumbnail-preview-input" name="thumbnail-preview" type="file">
                </div>

                <div class="row">
                    <div id="articleSections" class="col-12 d-flex flex-column">
                        <div>
                            <h1 id="headline"
                                class="@if($errors->has('headline')) error-text @endif">{{__('eap-online.articles.headline')}}</h1>
                            <div class="row">
                                <div class="col-8">
                                    <textarea oninput="removeError('error-text', 'headline')" name="headline" cols="30"
                                              rows="5" placeholder="{{__('eap-online.articles.headline')}}..."
                                              style="margin: 0 !important;">{{old('headline')}}</textarea>
                                </div>
                            </div>
                        </div>
                        <div>
                            <h1 id="lead"
                                class="@if($errors->has('lead')) error-text @endif">{{__('eap-online.articles.lead')}}</h1>
                            <div class="row">
                                <div class="col-8">
                                    <textarea oninput="removeError('error-text', 'lead')" name="lead" cols="30" rows="5"
                                              placeholder="{{__('eap-online.articles.lead')}}..."
                                              style="margin: 0 !important;">{{old('lead')}}</textarea>
                                </div>
                            </div>
                        </div>
                        <div>
                            <h1 class="sectionHeader">{{__('eap-online.articles.body')}}</h1>
                            <div class="row">
                                <div class="col-8">
                                    <textarea name="sections[0][content]" cols="30" rows="5"
                                              style="margin: 0 !important;"></textarea>
                                </div>
                                <div class="col-2 d-flex flex-column justify-content-between">
                                    <label class="container checkbox-container"
                                           id="customer-satisfaction-not-possible">{{__('eap-online.articles.body')}}
                                        <input type="radio" name="sections[0][type]" value="body" checked="checked">
                                        <span class="checkmark" onclick="changeSectionHeader(this)"></span>
                                    </label>
                                    <label class="container checkbox-container"
                                           id="customer-satisfaction-not-possible">{{__('eap-online.articles.highlight')}}
                                        <input type="radio" name="sections[0][type]" value="highlight">
                                        <span class="checkmark" onclick="changeSectionHeader(this)"></span>
                                    </label>
                                    <label class="container checkbox-container"
                                           id="customer-satisfaction-not-possible">{{__('eap-online.articles.subtitle')}}
                                        <input type="radio" name="sections[0][type]" value="subtitle">
                                        <span class="checkmark" onclick="changeSectionHeader(this)"></span>
                                    </label>
                                    <label class="container checkbox-container"
                                           id="customer-satisfaction-not-possible">{{__('eap-online.articles.list')}}
                                        <span class="d-none">{{__('eap-online.articles.separate_lines_by_enter')}}</span>
                                        <input type="radio" name="sections[0][type]" value="list">
                                        <span class="checkmark" onclick="changeSectionHeader(this)"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-5" style="margin-bottom: 400px">
                    <div class="col-12 d-flex">
                        <div class="mr-3">
                            <button class="text-center btn-radius" style="--btn-margin-right: 0px;" type="button" onclick="newArticleSection(1)">
                                + {{__('eap-online.articles.body')}}</button>
                        </div>
                        <div class="mr-3">
                            <button class="text-center btn-radius" style="--btn-margin-right: 0px;" type="button" onclick="newImageSection(1)">
                                + {{__('eap-online.articles.picture')}}</button>
                        </div>
                        <div class="mr-3">
                            <button class="text-center btn-radius" style="--btn-margin-right: 0px;" type="button" onclick="newFileSection(1)">
                                + {{__('eap-online.articles.document')}}</button>
                        </div>
                        <div class="mr-3">
                            <button class="text-center btn-radius" style="--btn-margin-right: 0px;" type="button" onclick="newLinkSection(1)">+ Link</button>
                        </div>
                    </div>
                    <div class="col-12 d-flex">
                        <div>
                            <button class="text-center button btn-radius float-right d-flex align-items-center" style="--btn-margin-right: 0px;" type="submit">
                                <img src="{{asset('assets/img/save.svg')}}" class="mr-1" style="height: 20px; width: 20px" alt="">
                                {{__('common.save')}}</button>
                        </div>
                    </div>
                </div>

                <input type="hidden" value="{{old('start_date')}}" name="start_date">
                <input type="hidden" value="{{old('end_date')}}" name="end_date">
                <input type="hidden" value="{{old('language')}}" name="language">
            </form>
        </div>
    </div>
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
                           placeholder="{{__('common.from')}}">
                    -
                    <input type="text" name="to_date" class="datepicker w-25" placeholder="{{__('common.to')}}">
                    <button class="button mr-3 float-right" onclick="saveDate()">
                        {{__('common.select')}}
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal" tabindex="-1" id="modal-language-select" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{__('workshop.select_language')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <select name="article_language">
                        @foreach(\App\Models\EapOnline\EapLanguage::all() as $language)
                            <option value="{{$language->id}}">{{$language->name}}</option>
                        @endforeach
                    </select>
                    <button class="button btn-radius float-right m-0" style="--btn-margin-right: 0px" onclick="saveLanguage()">
                        <img src="{{asset('assets/img/select.svg')}}" style="height: 20px; width: 20px" alt="">
                        <span>{{__('common.select')}}</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
