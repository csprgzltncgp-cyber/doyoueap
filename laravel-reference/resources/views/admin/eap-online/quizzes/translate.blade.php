@extends('layout.master')

@section('title')
    Admin Dashboard
@endsection

@section('extra_css')
    <link rel="stylesheet" href="/assets/css/cases/view.css?t={{time()}}">
    <link rel="stylesheet" href="/assets/css/eap-online/translations.css?t={{time()}}">
@endsection
@section('extra_js')
    <script src="/assets/js/eap-online/translations.js?v={{time()}}" charset="utf-8"></script>
@endsection

@section('content')
    <div class="row">
        <div class="col-12 mb-5 p-0">
            {{ Breadcrumbs::render('eap-online.translate-quizzes.view', $quiz) }}
            <h1>{{$quiz->title_translations()->where('language_id', $quiz->input_language)->first()->value}}
                - {{__('eap-online.quizzes.translate')}}</h1>
        </div>
        <div class="col-12">
            <div class="row d-flex flex-column col-12">
                <form method="post" class="w-100">
                    {{csrf_field()}}
                    <div class="d-flex justify-content-between align-items-center mb-3 line"
                         onclick="toggleTranslationSection('title-{{$quiz->id}}-translations', this)"
                    >
                        <p class="m-0 mr-3">{{__('eap-online.quizzes.title')}}</p>
                        <div class="d-flex align-items-center">
                            <div class="d-flex flex-wrap">
                                @foreach($languages as $language)
                                    @if(!empty($quiz->title_translations()->where('language_id', $language->id)->first()))
                                        <div style="background-color:rgb(145,183,82);" class="px-2 text-white mr-3 mb-2">
                                            {{$language->code}}
                                        </div>
                                    @else
                                        <div style="background-color:rgb(219, 11, 32);" class="px-2 text-white mr-3 mb-2">
                                            {{$language->code}}
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                                <svg xmlns="http://www.w3.org/2000/svg" style="min-width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                        </div>
                    </div>
                    <div class="d-none" id="title-{{$quiz->id}}-translations">
                        @foreach($languages as $language)
                            <div class="row translation">
                                <div class="col-1 text-center" style="padding-top:15px;">
                                    {{$language->code}}
                                </div>
                                <div class="col-8 pl-0">
                                        <textarea name="title[{{$quiz->id}}][{{$language->id}}]"
                                                  placeholder="{{__('eap-online.system.translation')}}">{{$quiz->title_translations()->where('language_id', $language->id)->first()->value ?? ''}}</textarea>
                                </div>
                            </div>
                        @endforeach
                        <div class="col-12  mt-1 mb-4">
                            <button type="submit" class="button btn-radius d-flex align-items-center">
                                <img src="{{asset('assets/img/save.svg')}}" style="height: 20px; width: 20px" alt="">
                                {{__('common.save')}}</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="row d-flex flex-column col-12">
                @foreach($quiz->eap_questions as $question)
                    <form method="post" class="w-100">
                        {{csrf_field()}}
                        <div class="my-4">
                            <div class="d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-center line mb-3"
                                     onclick="toggleTranslationSection('question-{{$question->id}}-translations', this)"
                                >
                                    <p class="m-0 mr-3">{{__('eap-online.quizzes.question')}} {{$loop->index + 1}}</p>
                                    <div class="d-flex align-items-center">
                                        <div class="d-flex flex-wrap">
                                            @foreach($languages as $language)
                                                @if(!empty($question->translations()->where('language_id', $language->id)->first()))
                                                    <div style="background-color:rgb(145,183,82);"
                                                        class="px-2 text-white mr-3 mb-2">
                                                        {{$language->code}}
                                                    </div>
                                                @else
                                                    <div style="background-color:rgb(219, 11, 32);"
                                                        class="px-2 text-white mr-3 mb-2">
                                                        {{$language->code}}
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                            <svg xmlns="http://www.w3.org/2000/svg" style="min-width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                            </svg>
                                    </div>
                                </div>
                                <div class="d-none" id="question-{{$question->id}}-translations">
                                    @foreach($languages as $language)
                                        <div class="row translation">
                                            <div class="col-1 text-center" style="padding-top:15px;">
                                                {{$language->code}}
                                            </div>
                                            <div class="col-8 pl-0">
                                                <textarea name="questions[{{$question->id}}][{{$language->id}}]"
                                                          placeholder="{{__('eap-online.system.translation')}}">{{$question->translations()->where('language_id', $language->id)->first()->value ?? ''}}</textarea>
                                            </div>
                                        </div>
                                    @endforeach
                                    <div class="col-12  mt-1 mb-4">
                                        <button type="submit" class="button btn-radius d-flex align-items-center">
                                            <img src="{{asset('assets/img/save.svg')}}" style="height: 20px; width: 20px" alt="">
                                            {{__('common.save')}}</button>
                                    </div>
                                </div>
                                <div class="d-flex flex-column">
                                    @foreach($question->eap_answers as $answer)
                                        <div class="d-flex justify-content-between align-items-center line mb-3"
                                             onclick="toggleTranslationSection('answer-{{$answer->id}}-translations', this)"
                                        >
                                            <p class="m-0 mr-3">{{__('eap-online.quizzes.answer')}} {{$loop->index + 1}}</p>
                                            <div class="d-flex align-items-center">
                                                <div class="d-flex flex-wrap">
                                                    @foreach($languages as $language)
                                                        @if(!empty($answer->translations()->where('language_id', $language->id)->first()))
                                                            <div style="background-color:rgb(145,183,82);"
                                                                class="px-2 text-white mr-3 mb-2">
                                                                {{$language->code}}
                                                            </div>
                                                        @else
                                                            <div style="background-color:rgb(219, 11, 32);"
                                                                class="px-2 text-white mr-3 mb-2">
                                                                {{$language->code}}
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                                    <svg xmlns="http://www.w3.org/2000/svg" style="min-width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                                    </svg>
                                            </div>
                                        </div>
                                        <div class="d-none" id="answer-{{$answer->id}}-translations">
                                            @foreach($languages as $language)
                                                <div class="row translation">
                                                    <div class="col-1 text-center" style="padding-top:15px;">
                                                        {{$language->code}}
                                                    </div>
                                                    <div class="col-8 pl-0">
                                                        <textarea
                                                                name="answers[{{$answer->id}}][{{$language->id}}]"
                                                                placeholder="{{__('eap-online.system.translation')}}">{{$answer->translations()->where('language_id', $language->id)->first()->value ?? ''}}</textarea>
                                                    </div>
                                                </div>
                                            @endforeach
                                            <div class="col-12  mt-1 mb-4">
                                                <button type="submit" class="button btn-radius d-flex align-items-center">
                                                    <img src="{{asset('assets/img/save.svg')}}" class="mr-1" style="height: 20px; width: 20px" alt="">
                                                    {{__('common.save')}}</button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </form>
                @endforeach
            </div>
            <div id="results_holder" class="row d-flex flex-column col-12">
                @foreach($quiz->eap_results as $result)
                    <form method="post" class="w-100">
                        {{csrf_field()}}
                        <div class="d-flex justify-content-between align-items-center line mb-3"
                             onclick="toggleTranslationSection('result-{{$result->id}}-translations', this)"
                        >
                            <p class="m-0 mr-3">{{__('eap-online.quizzes.result')}} {{$loop->index + 1}}</p>
                            <div class="d-flex align-items-center">
                                <div class="d-flex flex-wrap">
                                    @foreach($languages as $language)
                                        @if(!empty($result->translations()->where('language_id', $language->id)->first()))
                                            <div style="background-color:rgb(145,183,82);"
                                                class="px-2 text-white mr-3 mb-2">
                                                {{$language->code}}
                                            </div>
                                        @else
                                            <div style="background-color:rgb(219, 11, 32);"
                                                class="px-2 text-white mr-3 mb-2">
                                                {{$language->code}}
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                                    <svg xmlns="http://www.w3.org/2000/svg" style="min-width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                    </svg>
                            </div>
                        </div>
                        <div class="d-none" id="result-{{$result->id}}-translations">
                            @foreach($languages as $language)
                                <div class="row translation">
                                    <div class="col-1 text-center" style="padding-top:15px;">
                                        {{$language->code}}
                                    </div>
                                    <div class="col-8 pl-0">
                                        <textarea name="results[{{$result->id}}][{{$language->id}}]"
                                                  placeholder="{{__('eap-online.system.translation')}}">{{$result->translations()->where('language_id', $language->id)->first()->value ?? ''}}</textarea>
                                    </div>
                                </div>
                            @endforeach
                            <div class="col-12 mt-1 mb-4">
                                <button type="submit" class="button btn-radius d-flex align-items-center">
                                    <img src="{{asset('assets/img/save.svg')}}" class="mr-1" style="height: 20px; width: 20px" alt="">
                                    {{__('common.save')}}</button>
                            </div>
                        </div>
                    </form>
                @endforeach
            </div>
        </div>
        <div class="col-4 col-lg-2 back-button mb-5">
            <a href="{{ route('admin.eap-online.quizzes.translate.list') }}">{{__('common.back-to-list')}}</a>
        </div>
    </div>
@endsection
