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
        const type_trans = "{{__('prizegame.pages.type')}}";
    </script>
    <script src="/assets/js/eap-online/validator.js?v={{time()}}" charset="utf-8"></script>
    <script src="/assets/js/prizegame/contents_validator.js?v={{time()}}" charset="utf-8"></script>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            {{ Breadcrumbs::render('prizegame.pages.create') }}
            <h1>{{__('myeap.pages.new')}}</h1>
        </div>
        <div class="col-12">
            <form class="mb-5" method="post" style="max-width: 100%" enctype="multipart/form-data">
                {{csrf_field()}}

                <div class="row d-flex flex-column col-12">
                    <h1 class="mb-3">{{__('eap-online.actions.language')}}</h1>
                    <div class="col-3 row d-flex">
                        <button id="language-select-button"
                                class="@if($errors->has('language')) error @endif float-left btn-radius" type="button"
                                onclick="openModal('modal-language-select')">
                            @if(!empty(old('language')))
                                {{\App\Models\PrizeGame\Language::find(intval(old('language')))->name}}
                            @else
                                <img src="{{asset('assets/img/language.svg')}}" class="mr-1" style="height: 20px; width: 20px" alt="">
                                <span>
                                    {{__('workshop.select_language')}}
                                </span>
                            @endif
                        </button>
                    </div>

                    <div class="row d-flex flex-column col-12">
                        <h1 class="mb-3">{{__('prizegame.pages.type')}}</h1>
                        <div class="col-3 row d-flex">
                            <select name="type">
                                @foreach($types as $type)
                                    @if (session()->has('prizegame_type') && session()->get('prizegame_type') == $type->id)
                                        <option value="{{$type->id}}" selected="selected">{{$type->name}}</option>
                                    @else
                                        <option value="{{$type->id}}">{{$type->name}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row d-flex flex-column col-12 mt-3">
                        <div class="col-12 row d-flex mb-3">
                            <p class="w-100 m-0 p-0 px-2 py-2 text-white"
                               style="background-color: rgb(0,87,95);">{{__('myeap.pages.block')}}
                                1</p>
                        </div>

                        <div id="sections-block-1" class="col-12 d-flex flex-column row">
                            <div id="block-1-headline">
                                <h1 class="sectionHeader">{{__('myeap.pages.headline')}}</h1>
                                <div class="row">
                                    <input type="hidden" name="sections[1][block]" value="1">
                                    <div class="col-8">
                                        <textarea name="sections[1][value]" cols="30" rows="5" style="margin: 0 !important;"></textarea>
                                    </div>
                                    <div class="col-2 d-flex flex-column justify-content-between">
                                        <input type="hidden" name="sections[1][type]" readonly checked="checked" value="1">
                                    </div>
                                </div>
                            </div>

                            @if (session()->has('prizegame_type') && session()->get('prizegame_type') == 5)
                            <div id="block-1-sub-headline">
                                <h1 class="sectionHeader">{{__('myeap.pages.sub_headline')}}</h1>
                                <div class="row">
                                    <input type="hidden" name="sections[2][block]" value="1">
                                    <div class="col-8">
                                        <textarea name="sections[2][value]" cols="30" rows="5" style="margin: 0 !important;"></textarea>
                                    </div>
                                    <div class="col-2 d-flex flex-column justify-content-between">
                                        <input type="hidden" name="sections[2][type]" readonly checked="checked" value="2">
                                    </div>
                                </div>
                            </div>
                            @endif

                            @if (session()->has('prizegame_type') && session()->get('prizegame_type') == 5)
                            <div id="block-1-body">
                                <h1 class="sectionHeader">{{__('myeap.pages.body')}}</h1>
                                <div class="row">
                                    <input type="hidden" name="sections[3][block]" value="1">
                                    <div class="col-8">
                                        <textarea name="sections[3][value]" cols="30" rows="5" style="margin: 0 !important;"></textarea>
                                    </div>
                                    <div class="col-2 d-flex flex-column justify-content-between">
                                        <input type="hidden" name="sections[3][type]" readonly checked="checked" value="4">
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>

                        <div id="block-1-new-section" class="col-12 row {{ (session()->has('prizegame_type') && session()->get('prizegame_type') == 5) ? 'd-none' : 'd-flex'}} mt-5">
                            <div class="mr-3">
                                <button class="text-center btn-radius" type="button" onclick="newSection(4, 1, ['body', 'sub_headline', 'list'])">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="width:20px;" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-6 h-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                    </svg>
                                    <span>
                                        {{__('myeap.pages.body')}}
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="row d-flex flex-column col-12 mt-5">
                        <div class="col-12 row d-flex mb-3">
                            <p class="w-100 m-0 p-0 px-2 py-2 text-white"
                               style="background-color: rgb(0,87,95);">{{__('myeap.pages.block')}}
                                2</p>
                        </div>

                        <div id="sections-block-2" class="col-12 d-flex flex-column row">
                            @if (session()->has('prizegame_type') && session()->get('prizegame_type') != 5)
                            <div id="block-2-headline">
                                <h1 class="sectionHeader">{{__('myeap.pages.headline')}}</h1>
                                <div class="row">
                                    <input type="hidden" name="sections[2][block]" value="2">
                                    <div class="col-8">
                                        <textarea name="sections[2][value]" cols="30" rows="5" style="margin: 0 !important;"></textarea>
                                    </div>
                                    <div class="col-2 d-flex flex-column justify-content-between">
                                        <input type="hidden" name="sections[2][type]" readonly checked="checked" value="1">
                                    </div>
                                </div>
                            </div>
                            @endif

                            @if (session()->has('prizegame_type') && session()->get('prizegame_type') == 5)
                            <div id="block-2-sub-headline">
                                <h1 class="sectionHeader">{{__('myeap.pages.sub_headline')}}</h1>
                                <div class="row">
                                    <input type="hidden" name="sections[4][block]" value="2">
                                    <div class="col-8">
                                        <textarea name="sections[4][value]" cols="30" rows="5" style="margin: 0 !important;"></textarea>
                                    </div>
                                    <div class="col-2 d-flex flex-column justify-content-between">
                                        <input type="hidden" name="sections[4][type]" readonly checked="checked" value="2">
                                    </div>
                                </div>
                            </div>
                            @endif

                            @if (session()->has('prizegame_type') && session()->get('prizegame_type') == 5)
                            <div id="block-2-body">
                                <h1 class="sectionHeader">{{__('myeap.pages.body')}}</h1>
                                <div class="row">
                                    <input type="hidden" name="sections[5][block]" value="2">
                                    <div class="col-8">
                                        <textarea name="sections[5][value]" cols="30" rows="5" style="margin: 0 !important;"></textarea>
                                    </div>
                                    <div class="col-2 d-flex flex-column justify-content-between">
                                        <input type="hidden" name="sections[5][type]" readonly checked="checked" value="4">
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>

                        <div id="block-2-new-section" class="col-12 row d-flex mt-5">
                            <div class="mr-3">
                                <button class="text-center btn-radius" type="button" onclick="{{ (session()->has('prizegame_type') && session()->get('prizegame_type') == 5) ? "newSection(5, 2, ['body', 'sub_headline'])" : "newSection(4, 2, ['body', 'sub_headline', 'list', 'checkbox'])" }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="width:20px;" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-6 h-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                    </svg>
                                    <span>
                                        {{__('myeap.pages.body')}}
                                    </span>
                                </button>
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
                            @if (session()->has('prizegame_type') && session()->get('prizegame_type') != 5)
                            <div id="block-3-headline">
                                <h1 class="sectionHeader">{{__('myeap.pages.headline')}}</h1>
                                <div class="row">
                                    <input type="hidden" name="sections[3][block]" value="3">
                                    <div class="col-8">
                                        <textarea name="sections[3][value]" cols="30" rows="5" style="margin: 0 !important;"></textarea>
                                    </div>
                                    <div class="col-2 d-flex flex-column justify-content-between">
                                        <input type="hidden" name="sections[3][type]" readonly checked="checked"
                                               value="1">
                                    </div>
                                </div>
                            </div>
                            @endif

                            @if (session()->has('prizegame_type') && session()->get('prizegame_type') != 5)
                            <div id="block-3-body">
                                <h1 class="sectionHeader">{{__('myeap.pages.body')}}</h1>
                                <div class="row">
                                    <input type="hidden" name="sections[3][block]" value="3">
                                    <div class="col-8">
                                        <textarea name="sections[3][value]" cols="30" rows="5" style="margin: 0 !important;"></textarea>
                                    </div>
                                    <div class="col-2 d-flex flex-column justify-content-between">
                                        <input type="hidden" name="sections[3][type]" readonly checked="checked"
                                               value="4">
                                    </div>
                                </div>
                            </div>
                            @endif

                            @if (session()->has('prizegame_type') && session()->get('prizegame_type') != 5)
                            <div id="block-3-phone">
                                <h1 class="sectionHeader">{{__('prizegame.pages.phone')}}</h1>
                                <div class="row">
                                    <div class="col-8 d-flex">
                                        <input type="number" id="phone_number" name="phone-number"
                                               style="margin: 0 !important;"/>
                                    </div>
                                </div>
                            </div>
                            @endif

                            @if (session()->has('prizegame_type') && session()->get('prizegame_type') != 5)
                            <div id="numbers_holder" class="col-12 row mt-5">
                            </div>
                            @endif

                            @if (session()->has('prizegame_type') && session()->get('prizegame_type') != 5)
                            <div id="questions_holder" class="row d-flex flex-column col-12 mt-5">
                            </div>
                            @endif
                        </div>

                        <div id="block-3-new-section" class="col-12 row {{ (session()->has('prizegame_type') && session()->get('prizegame_type') == 5) ? 'd-flex' : 'd-none' }} mt-5">
                            <div class="mr-3">
                                <button class="text-center btn-radius" type="button" onclick="{{ (session()->has('prizegame_type') && session()->get('prizegame_type') == 5) ? "newSection(5, 3, ['checkbox'])" : "" }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="width:20px;" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-6 h-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                    </svg>
                                    {{__('prizegame.pages.checkbox')}}
                                </button>
                            </div>
                        </div>
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

                    <input type="hidden" value="{{old('language')}}" name="language">
                </div>
            </form>
        </div>

    </div>
    </div>
@endsection

@section('modal')
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
                    <select name="content-language">
                        @foreach($languages as $language)
                            <option value="{{$language->id}}">{{$language->name}}</option>
                        @endforeach
                    </select>
                    <button class="button btn-radius float-right m-0" style="--btn-margin-right: 0px;" onclick="saveLanguage()">
                        <img src="{{asset('assets/img/select.svg')}}" class="mr-1" style="height: 20px; width: 20px" alt="">
                        <span>
                            {{__('common.select')}}
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
