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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    <script src="/assets/js/datetime.js" charset="utf-8"></script>
    <script src="/assets/js/eap-online/article_video.js?v={{time()}}" charset="utf-8"></script>
    <script src="/assets/js/eap-online/quiz.js?v={{time()}}" charset="utf-8"></script>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            {{ Breadcrumbs::render('eap-online.quizzes.edit', $quiz) }}
            <h1>{{ __('eap-online.quizzes.edit') }}</h1>
        </div>
        <div class="col-12">
            <form class="mb-5" action="{{route('admin.eap-online.quizzes.edit', ['id' => $quiz->id])}}" method="post"
                  style="max-width: 100%" enctype="multipart/form-data">
                {{csrf_field()}}
                <div class="row d-flex flex-column col-12">
                    <h1 class="mb-3">{{__('eap-online.articles.appearance')}} <span id="visibility_alt"
                                class="h5 {{ ($quiz->eap_visibility->burnout_page || $quiz->eap_visibility->domestic_violence_page) ? 'd-none' : '' }}">{{__('eap-online.quizzes.visibility_alt')}}</span></h1>
                    <select name="visibility">
                        <option value="null">{{__('eap-online.articles.none')}}</option>
                        <option value="theme_of_the_month"
                                @if($quiz->eap_visibility->theme_of_the_month) selected @endif>{{__('eap-online.articles.theme_of_the_month')}}</option>
                        <option value="home_page"
                                @if($quiz->eap_visibility->home_page) selected @endif>{{__('eap-online.articles.home_page')}}</option>
                        <option value="burnout_page"
                                @if($quiz->eap_visibility->burnout_page) selected @endif>{{__('eap-online.articles.burnout_page')}}</option>
                            <option value="domestic_violence_page"
                                @if($quiz->eap_visibility->domestic_violence_page) selected @endif>{{__('eap-online.articles.domestic_violence_page')}}</option>
                    </select>
                    <div id="apperance-more"
                         class="@if(!($quiz->eap_visibility->theme_of_the_month || $quiz->eap_visibility->home_page)) d-none @endif">
                        <div id="date_picker"
                             class="@if($quiz->eap_visibility->theme_of_the_month)d-flex @else d-none @endif flex-column mb-4 ml-n2"
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
                                    @if(empty($quiz->eap_visibility->to_date))
                                        <span class="pt-1"
                                              id="visibility_date">{{__('eap-online.articles.select_date')}}</span>
                                    @else
                                        <span class="pt-1"
                                              id="visibility_date">{{$quiz->eap_visibility->from_date}} - {{$quiz->eap_visibility->to_date}}</span>
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="categorize" class="row {{ ($quiz->eap_visibility->burnout_page || $quiz->eap_visibility->domestic_violence_page) ? 'd-none' : 'd-flex' }} flex-column col-12">
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
                                                   @if($quiz->hasCategory($category->id))
                                                   checked="checked"
                                                    @endif
                                            >
                                            <span class="checkmark"></span>
                                        </label>
                                        @if(count($category->childs))
                                            @include('components.eap-online.category-line ',['childs' => $category->childs, 'level' => 1, 'resource' => $quiz, 'type' => $loop->index])
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                <div id="lesson" class="row {{ $quiz->eap_visibility->burnout_page ? 'd-flex' : 'd-none' }} flex-column col-12">
                    <h1 class="mb-3">{{__('eap-online.articles.lesson')}}</h1>
                    <select name="lesson">
                        <option value="1"
                                @if(optional($quiz->lesson)->value == '1') selected @endif>{{__('eap-online.articles.lesson_1')}}</option>
                        <option value="2"
                                @if(optional($quiz->lesson)->value == '2') selected @endif>{{__('eap-online.articles.lesson_2')}}</option>
                        <option value="3"
                                @if(optional($quiz->lesson)->value == '3') selected @endif>{{__('eap-online.articles.lesson_3')}}</option>
                        <option value="4"
                                @if(optional($quiz->lesson)->value == '4') selected @endif>{{__('eap-online.articles.lesson_4')}}</option>
                        <option value="5"
                                @if(optional($quiz->lesson)->value == '5') selected @endif>{{__('eap-online.articles.lesson_5')}}</option>
                        <option value="6"
                                @if(optional($quiz->lesson)->value == '6') selected @endif>{{__('eap-online.articles.lesson_6')}}</option>
                        <option value="7"
                                @if(optional($quiz->lesson)->value == '7') selected @endif>{{__('eap-online.articles.lesson_7')}}</option>
                    </select>
                </div>
                <div id="chapter" class="row {{ $quiz->eap_visibility->domestic_violence_page ? 'd-flex' : 'd-none' }} flex-column col-12">
                    <h1 class="mb-3">{{__('eap-online.articles.chapter')}}</h1>
                    <select name="chapter">
                        <option value="1"
                                @if(optional($quiz->chapter)->value == '1') selected @endif>{{__('eap-online.articles.chapter_1')}}</option>
                        <option value="2"
                                @if(optional($quiz->chapter)->value == '2') selected @endif>{{__('eap-online.articles.chapter_2')}}</option>
                        <option value="3"
                                @if(optional($quiz->chapter)->value == '3') selected @endif>{{__('eap-online.articles.chapter_3')}}</option>
                        <option value="4"
                                @if(optional($quiz->chapter)->value == '4') selected @endif>{{__('eap-online.articles.chapter_4')}}</option>
                        <option value="5"
                                @if(optional($quiz->chapter)->value == '5') selected @endif>{{__('eap-online.articles.chapter_5')}}</option>
                        <option value="6"
                                @if(optional($quiz->chapter)->value == '6') selected @endif>{{__('eap-online.articles.chapter_6')}}</option>
                    </select>
                </div>
                <div class="row col-12 d-flex">
                    <div class="d-flex flex-column justify-content-center">
                        <div>
                            <h1>{{__('eap-online.articles.thumbnail')}}</h1>
                            <div class="d-flex align-items-center mb-3"
                                 style="cursor: pointer">
                                <svg class="@if($quiz->eap_thumbnail)d-none @endif ml-n1 mr-1"
                                     onclick="triggerFileUpload()" id="file-upload-trigger"
                                     style="color: rgb(89, 198, 198); height: 20px; width: 20px; cursor: pointer"
                                     xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                     stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>


                                <svg onclick="deleteUploadedFile()"
                                     class="@if(!$quiz->eap_thumbnail)d-none @endif mr-1 ml-n1" id="file-delete-trigger"
                                     xmlns="http://www.w3.org/2000/svg"
                                     style="color: rgb(89, 198, 198); height: 20px; width: 20px; cursor: pointer"
                                     fill="none"
                                     viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>

                                <span id="uploaded-file-name">
                                     @if($quiz->eap_thumbnail)
                                        {{$quiz->eap_thumbnail->filename}}
                                    @else
                                        {{__('eap-online.articles.thumbnail_button')}}
                                    @endif
                                </span>
                            </div>
                        </div>
                        <img class="@if(!$quiz->eap_thumbnail) d-none @endif"
                             src="@if($quiz->eap_thumbnail)/assets/eap-online/thumbnails/quiz/{{$quiz->eap_thumbnail->filename}}@endif"
                             alt="preview" id="thumbnail-preview"
                             width="200px" style="border:2px solid #4dc0b5">
                    </div>
                    <input class="d-none" name="thumbnail" type="file">
                </div>

                <div class="row d-flex col-7">
                    <h1 id="title"
                        class="@if($errors->has('title')) error-text @endif">{{__('eap-online.quizzes.title')}}</h1>
                    <input onclick="removeError('error-text','title')" type="text"
                           placeholder="{{__('eap-online.videos.title_placeholder')}}"
                           name="title"
                           value="{{$quiz->title_translations()->where('language_id', $quiz->input_language)->first()->value}}">
                </div>

                <div class="row d-flex flex-column col-12 mt-5">
                    @foreach($quiz->eap_questions as $question)
                        <div class="mb-3">
                            <div class="d-flex flex-column">
                                <div class="d-flex">
                                    <textarea class="col-7 mr-3" type="text"
                                              placeholder="{{__('eap-online.videos.title_placeholder')}}"
                                              name="questions[{{$question->id}}][title]"
                                    >{{$question->translations()->where('language_id', $quiz->input_language)->first()->value}}</textarea>
                                </div>
                                <div class="d-flex flex-column">
                                    @foreach($question->eap_answers as $answer)
                                        <div class="d-flex">
                                            <textarea class="col-7 mr-3" type="text"
                                                      placeholder="{{__('eap-online.quizzes.answer_placeholder')}}"
                                                      name="questions[{{$question->id}}][answers][{{$answer->id}}][title]"
                                            >{{$answer->translations()->where('language_id', $quiz->input_language)->first()->value}}
                                            </textarea>
                                            <input class="col-1 mr-3 mb-auto" type="number"
                                                   placeholder="{{__('eap-online.quizzes.point_placeholder')}}"
                                                   name="questions[{{$question->id}}][answers][{{$answer->id}}][point]"
                                                   value="{{$answer->point}}"
                                            >
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div id="results_holder" class="row d-flex flex-column col-12">
                    @foreach($quiz->eap_results as $result)
                        <div class="d-flex flex-column justify-content-between">
                            <div class="d-flex">
                            <textarea name="results[{{$result->id}}][content]" cols="30" rows="10" class="col-7 mr-3"
                                      placeholder="{{__('eap-online.quizzes.result_placeholder')}}">{{$result->translations()->where('language_id', $quiz->input_language)->first()->value}}
                            </textarea>
                                <div style="padding: 0 !important;"
                                     class="d-flex col-3 justify-content-between align-items-start">
                                    <input type="number" name="results[{{$result->id}}][from]"
                                           placeholder="{{__('eap-online.quizzes.from_placeholder')}}"
                                           class="m-0"
                                           value="{{$result->from}}"
                                    >
                                    <span class="col-1 mt-2">-</span>
                                    <input type="number" name="results[{{$result->id}}][to]"
                                           placeholder="{{__('eap-online.quizzes.to_placeholder')}}"
                                           class="m-0"
                                           value="{{$result->to}}"
                                    >
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="row mt-5">
                    <div class="col-12 d-flex">
                        <div>
                            <button class="text-center btn-radius" type="submit">
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
                                <svg xmlns="http://www.w3.org/2000/svg" style="height: 20px; margin-bottom: 3px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg> </button>
                        </div>
                    </div>
                </div>

                <input type="hidden" name="start_date"
                       value="{{!empty($quiz->eap_visibility->from_date) ?  $quiz->eap_visibility->from_date : ''}}">
                <input type="hidden" name="end_date"
                       value="{{!empty($quiz->eap_visibility->to_date) ?  $quiz->eap_visibility->to_date : ''}}">
            </form>
        </div>
    </div>

    <form method="post" id="deleteForm"
          action="{{route('admin.eap-online.quizzes.delete', ['id' => $quiz->id])}}">{{csrf_field()}}</form>
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
                           value="{{!empty($quiz->eap_visibility->from_date) ?  $quiz->eap_visibility->from_date : ''}}">
                    -
                    <input type="text" name="to_date" class="datepicker w-25" placeholder="{{__('common.to')}}"
                           value="{{!empty($quiz->eap_visibility->to_date) ?  $quiz->eap_visibility->to_date : ''}}">
                    <button class="button mr-3 float-right" onclick="saveDate()">
                        {{__('common.select')}}
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
