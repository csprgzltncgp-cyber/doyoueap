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

    <style>
        .new-quiz-section {
            height: 25px;
            width: 25px;
            background-color: rgb(89, 198, 198);
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
        }

        .delete-quiz-section {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            color: rgb(89, 198, 198);
            cursor: pointer;
        }

    </style>
@endsection

@section('extra_js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.js" type="text/javascript"></script>
    <script
            src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>

    <script type="text/javascript">
        const language_trans = "{{__('eap-online.actions.language')}}";
        const date_trans = "{{__('crisis.date')}}";
        const title_trans = "{{__('eap-online.quizzes.title')}}";
        const picture_trans = "{{__('eap-online.articles.picture')}}";
        const required_trans = "{{__('eap-online.required')}}";
    </script>

    <script src="/assets/js/datetime.js" charset="utf-8"></script>
    <script src="/assets/js/eap-online/article_video.js?v={{time()}}" charset="utf-8"></script>
    <script src="/assets/js/eap-online/quiz.js?v={{time()}}" charset="utf-8"></script>
    <script src="/assets/js/eap-online/validator.js?v={{time()}}" charset="utf-8"></script>
    <script src="/assets/js/eap-online/quiz_validator.js?v={{time()}}" charset="utf-8"></script>

    <script>
        let questionIndex = 0;
        let answerIndex = 0;
        let resultIndex = 0;

        function addQuestion() {
            const $questionHolder = $('#questions_holder');
            const questionHtml = `
             <div id="question-${questionIndex}-holder" class="mb-3">
                        <div class="d-flex flex-column">
                            <div class="d-flex">
                                <input class="col-7 mr-3" type="text"
                                       placeholder="{{__('eap-online.videos.title_placeholder')}}"
                                       name="questions[${questionIndex}][title]">
                                <span class="delete-quiz-section" onclick="deleteQuestion(${questionIndex})">
                                                <i class="fas fa-trash-alt"></i></span>
                            </div>
                            <div id="question-${questionIndex}-answers" class="d-flex flex-column">

                            </div>
                        </div>
                        <div class="d-flex mb-3" style="cursor: pointer" onclick="addAnswer(${questionIndex})">
                            <span class="new-quiz-section mr-3">+</span>
                            <span>{{__('eap-online.quizzes.new_answer')}}</span>
                        </div>
                    </div>
            `;

            $questionHolder.append(questionHtml);
            questionIndex++;
        }

        function deleteQuestion(question_id) {
            $(`#question-${question_id}-holder`).remove();
        }

        function addAnswer(question_id) {
            const $answerHolder = $(`#question-${question_id}-answers`);
            const answerHtml = `
            <div class="d-flex" id="answer-${answerIndex}-holder">
                <input class="col-7 mr-3" type="text"
                       placeholder="{{__('eap-online.quizzes.answer_placeholder')}}"
                       name="questions[${question_id}][answers][${answerIndex}][title]">
                <input class="col-1 mr-3" type="number"
                       placeholder="{{__('eap-online.quizzes.point_placeholder')}}"
                       name="questions[${question_id}][answers][${answerIndex}][point]">
                <span class="delete-quiz-section" onclick="deleteAnswer(${answerIndex})">
                    <i class="fas fa-trash-alt"></i>
                </span>
            </div>
            `;

            $answerHolder.append(answerHtml);
            answerIndex++;
        }

        function deleteAnswer(answer_id) {
            $(`#answer-${answer_id}-holder`).remove();
        }

        function addResult() {
            const $resultsHolder = $('#results_holder');
            const resultHtml = `
            <div id="result-${resultIndex}-holder" class="d-flex flex-column justify-content-between">
                        <div class="d-flex">
                            <textarea name="results[${resultIndex}][content]" cols="30" rows="10" class="col-7 mr-3"
                                      placeholder="{{__('eap-online.quizzes.result_placeholder')}}"></textarea>
                            <div class="d-flex col-3 justify-content-between align-items-start">
                                <input type="number" name="results[${resultIndex}][from]"
                                       placeholder="{{__('eap-online.quizzes.from_placeholder')}}"
                                       class="m-0">
                                <span class="col-1 mt-2">-</span>
                                <input type="number" name="results[${resultIndex}][to]" placeholder="{{__('eap-online.quizzes.to_placeholder')}}"
                                       class="m-0">
                                <span class="delete-quiz-section" onclick="deleteResult(${resultIndex})">
                                     <i class="fas fa-trash-alt ml-3 mt-3"></i>
                                </span>
                            </div>
                        </div>
                    </div>
            `;

            $resultsHolder.append(resultHtml).addClass('mt-3');
            resultIndex++;
        }

        function deleteResult(result_id) {
            $(`#result-${result_id}-holder`).remove();
        }
    </script>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            {{ Breadcrumbs::render('eap-online.quizzes.create') }}
            <h1>{{ __('eap-online.quizzes.new') }}</h1>
        </div>
        <div class="col-12">
            <form class="mb-5" action="{{route('admin.eap-online.quizzes.new')}}" method="post" style="max-width: 100%" enctype="multipart/form-data">
                {{csrf_field()}}

                @if($errors->has('questions'))
                    <p class="error w-100 p-2"
                       style="margin-right: 10px">{{__('eap-online.quizzes.question_error')}}</p>
                @endif

                @if($errors->has('results'))
                    <p class="error w-100 p-2"
                       style="margin-right: 10px">{{__('eap-online.quizzes.results_error')}}</p>
                @endif

                <div class="row d-flex flex-column col-12">
                    <h1 class="mb-3">{{__('eap-online.actions.language')}}</h1>
                    <div class="col-3 row d-flex">
                        <button id="language-select-button"
                                class="float-left @if($errors->has('language')) error @endif btn-radius" type="button"
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
                                class="h5 {{ in_array(old('visibility'), ['burnout_page', 'domestic_violence_page']) ? 'd-none' : '' }}">{{__('eap-online.quizzes.visibility_alt')}}</span></h1>
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
                    </div>
                </div>

                <div id="categorize" class="row {{ in_array(old('visibility'), ['burnout_page', 'domestic_violence_page']) ? 'd-none' : 'd-flex' }} flex-column col-12">
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
                    </div>
                </div>

                <div id="lesson" class="row {{ old('visibility') == 'burnout_page' ? 'd-flex' : 'd-none' }} flex-column col-12">
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

                <div id="chapter" class="row {{ old('visibility') == 'domestic_violence_page' ? 'd-flex' : 'd-none' }} flex-column col-12">
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
                                <svg class="ml-n1 mr-1"
                                     onclick="triggerFileUpload()"
                                     id="file-upload-trigger"
                                     style="color: rgb(89, 198, 198); height: 20px; width: 20px; cursor: pointer"
                                     xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                     stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>

                                <svg onclick="deleteUploadedFile()"
                                     class="d-none mr-1"
                                     id="file-delete-trigger"
                                     xmlns="http://www.w3.org/2000/svg"
                                     style="color: rgb(89, 198, 198); height: 20px; width: 20px; cursor: pointer"
                                     fill="none"
                                     viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>

                                <span id="uploaded-file-name">{{__('eap-online.articles.thumbnail_button')}}</span>
                            </div>
                        </div>
                        <img class="d-none" src="#" alt="preview" id="thumbnail-preview"
                             width="200px" style="border:2px solid #4dc0b5">
                    </div>
                    <input class="d-none" name="thumbnail" type="file">
                </div>

                <div class="row d-flex col-7">
                    <h1 id="title"
                        class="@if($errors->has('title')) error-text @endif">{{__('eap-online.quizzes.title')}}</h1>
                    <input onclick="removeError('error-text','title')" type="text"
                           placeholder="{{__('eap-online.videos.title_placeholder')}}"
                           name="title" value="{{old('title')}}">
                </div>

                <div id="questions_holder" class="row d-flex flex-column col-12 mt-5"></div>

                <div class="row">
                    <div class="col-12 d-flex flex-column">
                        <div class="d-flex mb-3" style="cursor: pointer" onclick="addQuestion()">
                            <span class="new-quiz-section mr-3">+</span>
                            <span>{{__('eap-online.quizzes.new_question')}}</span>
                        </div>
                    </div>
                </div>

                <div id="results_holder" class="row d-flex flex-column col-12"></div>

                <div class="row">
                    <div class="col-12 d-flex flex-column">
                        <div class="d-flex" style="cursor: pointer" onclick="addResult()">
                            <span class="new-quiz-section mr-3">+</span>
                            <span>{{__('eap-online.quizzes.new_result')}}</span>
                        </div>
                    </div>
                </div>

                <div class="row mt-5">
                    <div class="col-12 d-flex">
                        <div>
                            <button class="text-center btn-radius" type="submit">
                                <img class="mr-1" src="{{asset('assets/img/save.svg')}}" style="height: 20px; width: 20px" alt="">
                                <span class="mt-1">{{__('common.save')}}</span>
                            </button>
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
                        <span>
                            {{__('common.select')}}
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
