@extends('layout.master')

@section('title')
    Admin Dashboard
@endsection

@section('extra_css')
    <link rel="stylesheet" href="/assets/css/list.css?v={{time()}}">
    <link rel="stylesheet" href="/assets/css/form.css?v={{time()}}">
    <link rel="stylesheet" href="/assets/css/cases/view.css?t=<?php echo e(time()); ?>">
    <link rel="stylesheet" href="/assets/css/eap-online/articles.css?v={{time()}}">

    <style>
        .number {
            background: rgb(89, 198, 198);
            padding: 15px;
            color: white;
            font-size: 25px;
            cursor: pointer;
            margin-right: 10px;
            margin-bottom: 10px;
        }

        .number.selected {
            background: rgb(138, 74, 122);
        }

        .number.placeholder {
            background: rgb(217, 193, 210);
        }

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
    <script src="/assets/js/prizegame/contents.js?v={{time()}}" charset="utf-8"></script>
    <script type="text/javascript">
        const headline_trans = "{{__('prizegame.pages.headline')}}";
        const sub_headline_trans = "{{__('prizegame.pages.sub_headline')}}";
        const list_trans = "{{__('prizegame.pages.list')}}";
        const body_trans = "{{__('prizegame.pages.body')}}";
        const checkbox_trans = "{{__('prizegame.pages.checkbox')}}";
        const delete_trans = "{{__('common.delete')}}";
        const list_alt_trans = "{{__('eap-online.articles.separate_lines_by_enter')}}";
        const required_trans = "{{__('eap-online.required')}}";
        const language_trans = "{{__('eap-online.actions.language')}}";
        const file_trans = "{{__('prizegame.pages.file')}}";
        const download_button_trans = "{{__('eap-online.videos.attachment_button_title')}}";
        const correct_trans = "{{__('prizegame.pages.correct')}}";
        const answer_trans = "{{__('eap-online.quizzes.answer_placeholder')}}";
        const question_trans = "{{__('eap-online.quizzes.question')}}";
        const new_answer_trans = "{{__('eap-online.quizzes.new_answer')}}";
        const image_trans = "{{__('prizegame.pages.bg_title')}}";
        const phone_trans = "{{__('prizegame.pages.phone')}}";
        const min_questions_trans = "{{__('prizegame.pages.min_questions_number')}}";
        const min_answers_trans = "{{__('prizegame.pages.min_answers_number')}}";
    </script>
    <script src="/assets/js/eap-online/validator.js?v={{time()}}" charset="utf-8"></script>
    <script src="/assets/js/prizegame/contents_validator.js?v={{time()}}" charset="utf-8"></script>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            {{ Breadcrumbs::render('prizegame.pages.edit', $content) }}
            <h1>{{__('myeap.pages.edit')}}</h1>
        </div>
        <div class="col-12">
            <form class="mb-5" method="post" style="max-width: 100%" enctype="multipart/form-data">
                {{csrf_field()}}
                <input type="hidden" name="prizegame-type" value="{{$content->type_id}}" />

                <div class="row d-flex flex-column col-12">

                    <div class="row d-flex flex-column col-12 mt-3">
                        <div class="col-12 row d-flex mb-3">
                            <p class="w-100 m-0 p-0 px-2 py-2 text-white"
                               style="background-color: rgb(0,87,95);">{{__('myeap.pages.block')}}
                                1
                            </p>
                        </div>

                        <div id="sections-block-1" class="col-12 d-flex flex-column row">
                            @foreach($content->sections->where('block', 1)->sortBy('id') as $section)
                                @component('components.prizegame.section', ['language' => $content->language, 'section' => $section, 'block_id' => 1, 'content_type_id' => $content->type_id])@endcomponent
                            @endforeach
                        </div>

                        @if ($content->type_id != 5)
                        <div class="col-12 row d-flex mt-5">
                            <div class="mr-3">
                                <button class="text-center btn-radius" type="button"
                                        onclick="newSection({{optional($content->sections->last())->id ?? 1}}, 1, ['body', 'sub_headline', 'list'])">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="width:20px;" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-6 h-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                    </svg>
                                    <span>
                                        {{__('myeap.pages.body')}}
                                    </span>
                                </button>
                            </div>
                        </div>
                        @endif
                    </div>

                    <div class="row d-flex flex-column col-12 mt-5">
                        <div class="col-12 row d-flex mb-3">
                            <p class="w-100 m-0 p-0 px-2 py-2 text-white"
                               style="background-color: rgb(0,87,95);">{{__('myeap.pages.block')}}
                                2</p>
                        </div>

                        <div id="sections-block-2" class="col-12 d-flex flex-column row">
                            @foreach($content->sections->where('block', 2)->sortBy('id') as $section)
                                @component('components.prizegame.section', ['language' => $content->language, 'section' => $section, 'block_id' => 2, 'content_type_id' => $content->type_id])@endcomponent
                            @endforeach
                        </div>

                        <div class="col-12 row d-flex mt-5">
                            <div class="mr-3">
                                @if ($content->type_id != 5)
                                <button class="text-center btn-radius" type="button"
                                        onclick="newSection({{optional($content->sections->last())->id ?? 1}}, 2, ['body', 'sub_headline', 'list', 'checkbox'])">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="width:20px;" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-6 h-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                    </svg>
                                    <span>
                                        {{__('myeap.pages.body')}}
                                    </span>
                                </button>
                                @else
                                <button class="text-center btn-radius" type="button"
                                        onclick="newSection({{optional($content->sections->last())->id ?? 1}}, 2, ['body', 'sub_headline'])">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="width:20px;" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-6 h-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                    </svg>
                                    <span>
                                        {{__('myeap.pages.body')}}
                                    </span>
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row d-flex flex-column col-12 mt-5">
                        <div class="col-12 row d-flex mb-3">
                            <p class="d-flex justify-content-between align-items-center w-100 m-0 p-0 px-2 py-2 text-white"
                               style="background-color: rgb(0,87,95);">
                                <span>{{__('myeap.pages.block')}} 3</span>
                            </p>
                        </div>

                        <div id="sections-block-3" class="col-12 d-flex flex-column row">
                            @foreach($content->sections->where('block', 3)->sortBy('id') as $section)
                                @component('components.prizegame.section', ['language' => $content->language, 'section' => $section, 'block_id' => 3, 'content_type_id' => $content->type_id])@endcomponent
                            @endforeach

                            @if ($content->type_id != 5)
                            <div>
                                <h1 class="sectionHeader">Phone number</h1>
                                <div class="row">
                                    <div class="col-8 d-flex">
                                        <input type="number" id="phone_number" name="phone-number"
                                               style="margin: 0 !important;"
                                               value="{{$content->digits->sortBy('order')->implode('value','')}}"/>
                                        <input type="hidden" name="phone-number-changed"
                                               value="0"/>
                                    </div>
                                </div>
                            </div>
                            @endif

                            @if ($content->type_id != 5)
                            <div id="numbers_holder" class="col-12 row mt-5">
                                @foreach($content->digits->sortBy('order') as $digit)
                                    <div>
                                        <span class="number @if($digit->question) selected @endif"
                                              onclick="selectNumber(this)"
                                              digit_id="{{$digit->id}}"
                                              @if($digit->question) question_id="{{$digit->question->id}}" @endif>{{$digit->value}}</span>
                                        <input type="hidden" name="digits[{{$digit->id}}][value]"
                                               value="{{$digit->value}}"/>
                                        <input type="hidden" name="digits[{{$digit->id}}][id]"
                                               value="{{$digit->id}}"/>
                                        <input type="hidden" name="digits[{{$digit->id}}][question_id]"
                                               @if($digit->question) value="{{$digit->question->id}}"
                                               @else value @endif/>
                                        <input type="hidden" name="digits[{{$digit->id}}][sort]"
                                               value="{{$digit->order}}"/>
                                    </div>
                                @endforeach
                            </div>
                            @endif

                            @if ($content->type_id != 5)
                            <div id="questions_holder" class="row d-flex flex-column col-12 mt-5">
                                @foreach($content->questions as $question)
                                    @component('components.prizegame.question', ['question' => $question, 'language' => $content->language])@endcomponent
                                @endforeach
                            </div>
                            @endif
                        </div>

                        @if ($content->type_id == 5)
                        <div class="col-12 row d-flex mt-5">
                            <div class="mr-3">
                                <button class="text-center btn-radius" type="button"
                                        onclick="newSection({{optional($content->sections->last())->id ?? 1}}, 3, ['checkbox'])">
                                    + {{__('prizegame.pages.checkbox')}}</button>
                            </div>
                        </div>
                        @endif
                    </div>

                    <div class="row mt-5" style="margin-bottom: 400px">
                        <div class="col-12 d-flex mt-3">
                            <div>
                                <button class="text-center button btn-radius float-right d-flex align-items-center" type="submit">
                                    <img src="{{asset('assets/img/save.svg')}}" class="mr-1" style="height: 20px; width: 20px" alt="">
                                    {{__('common.save')}}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

    </div>
    </div>
@endsection
