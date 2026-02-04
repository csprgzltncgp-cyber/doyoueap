@extends('layout.master')

@section('title')
    Admin Dashboard
@endsection

@section('extra_css')
    <link rel="stylesheet" href="/assets/css/cases/view.css?t={{time()}}">
    <link rel="stylesheet" href="/assets/css/eap-online/translations.css?t={{time()}}">
    <link rel="stylesheet" href="/assets/css/form.css?v={{time()}}">
@endsection

@section('extra_js')
    <script src="/assets/js/eap-online/translations.js?v={{time()}}" charset="utf-8"></script>
@endsection

@section('content')
    <div class="row">
        <div class="col-12 mb-5 p-0">
            {{ Breadcrumbs::render('company-website.articles.translation.edit', $article) }}
            <h1>{{$article->getSectionByType(App\Models\CompanyWebsite\Section::TYPE_HEADLINE)}} - {{__('company-website.actions.articles.translation_edit')}}</h1>
        </div>

        <form action="{{route('admin.company-website.articles.translation.update', ['article' => $article])}}" method="post" class="col-12 row w-100">
            <div class="col-12 row">
                {{csrf_field()}}
                @php $iterator = 0; @endphp
                @foreach($article->sections->sortBy('id') as $section)
                    <div class="col-12 input">
                        <div class="row">
                            <div class="col-12 pl-3 d-flex justify-content-between align-items-center mb-3 line"
                                    onclick="toggleTranslationSection('title-{{$section->id}}-translations', this)"
                            >
                                <p class="m-0 mr-3">{{ucfirst($section->type)}}</p>
                                <div class="d-flex align-items-center">
                                    <div class="d-flex flex-wrap">
                                        @foreach($languages as $language)
                                            @if(!empty($section->has_translation($language->id)))
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
                                <input type="hidden" name="sections[{{$iterator}}][id]" value="{{$section->id}}">
                            </div>
                        </div>
                        <div class="d-none" id="title-{{$section->id}}-translations">
                            @foreach($languages as $language)
                                <div class="row translation">
                                    <div class="col-1 text-center" style="padding-top:15px;">
                                        {{$language->code}}
                                    </div>
                                    <div class="col-8 pl-0">
                                <textarea name="sections[{{$iterator}}][text][{{$language->id}}]"
                                            placeholder="{{__('eap-online.system.translation')}}">{{$section->get_translation($language->id)->value ?? ''}}</textarea>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @php $iterator++; @endphp
                @endforeach
            </div>

            <div class="row col-12 mt-1 mb-4 d-flex">
                <button class="w-auto btn-radius" type="submit">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="width: 20px; height: 20px" class="mr-1 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                    </svg>
                    <span class="mr-1">{{__('common.save')}}</span>
                </button>
            </div>
        </form>

        <div class="row col-4 col-lg-2 back-button mb-5">
            <a href="{{ route('admin.company-website.articles.translation.index') }}">{{__('common.back-to-list')}}</a>
        </div>
    </div>

@endsection
