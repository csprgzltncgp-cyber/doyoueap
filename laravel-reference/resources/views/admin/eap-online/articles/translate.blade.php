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
    <script>
        const picture_upload_trans = "{{__('eap-online.articles.thumbnail_button')}}";
        const file_upload_trans = "{{__('eap-online.videos.attachment_placeholder')}}";
    </script>
    <script src="/assets/js/eap-online/translations.js?v={{time()}}" charset="utf-8"></script>
    <script src="/assets/js/eap-online/translations-article.js?v={{time()}}" charset="utf-8"></script>
@endsection
@section('content')
    <div class="row">
        <div class="col-12 mb-5 p-0">
            {{ Breadcrumbs::render('eap-online.translate-articles.view', $article) }}
            <h1>{{$article->getSectionByType('headline')}} - {{__('eap-online.articles.translate')}}</h1>
        </div>
        <form method="post" class="col-12 row w-100" enctype="multipart/form-data">
            <div class="col-12 row">
                {{csrf_field()}}
                @php $iterator = 0; @endphp
                @foreach($article->eap_sections->sortBy('id') as $section)
                    @if($section->type == 'file')
                        <div class="col-12 input">
                            <div class="row">
                                <div class="col-12 pl-3 d-flex justify-content-between align-items-center mb-3 line"
                                     onclick="toggleTranslationSection('file-{{$section->id}}-translations', this)"
                                >
                                    <p class="m-0 mr-3">{{ucfirst($section->type)}}</p>
                                    <div class="d-flex align-items-center">
                                        <div class="d-flex flex-wrap">
                                            @foreach($languages as $language)
                                                @if(!empty($section->has_attachment_translation($language->id)))
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
                            <div class="d-none" id="file-{{$section->id}}-translations">
                                @foreach($languages as $language)
                                    @if($language->id != $article->input_language)
                                        <div class="row translation mb-3" style="min-height: 48px">
                                            <div class="col-1 text-center" style="padding-top:15px;">
                                                {{$language->code}}
                                            </div>
                                            <div class="col-8 pl-0 d-flex align-items-center">
                                                <div id="section-file-holder-{{$section->id}}-{{$language->id}}"
                                                     class="align-items-center justify-content-between pt-2 col-8 @if(isset($section->eap_section_attachment()->where('language_id', $language->id)->first()->same)) d-none @else d-flex @endif"
                                                >
                                                    <div class="d-flex flex-column">
                                                        <div class="d-flex align-items-center"
                                                             style="cursor: pointer">

                                                            <img class="ml-n1 mr-1 @if($section->has_attachment_translation($language->id) && !isset($section->eap_section_attachment()->where('language_id', $language->id)->first()->same)) d-none @endif"
                                                                 onclick="triggerFileUpload('section-file-{{$section->id}}-{{$language->id}}-input', 'file')"
                                                                 id="section-file-{{$section->id}}-{{$language->id}}-input-file-upload-trigger"
                                                                 src="{{asset('assets/img/eap-online/file_icon.svg')}}"
                                                            >


                                                            <img onclick="deleteUploadedFile('section-file-{{$section->id}}-{{$language->id}}-input', {{$section->id}}, {{$language->id}}, 'file')"
                                                                 class="mr-1 @if(!$section->has_attachment_translation($language->id) || isset($section->eap_section_attachment()->where('language_id', $language->id)->first()->same)) d-none @endif"
                                                                 id="section-file-{{$section->id}}-{{$language->id}}-input-file-delete-trigger"
                                                                 src="{{asset('assets/img/eap-online/trash.svg')}}"
                                                            >

                                                            <span id="section-file-{{$section->id}}-{{$language->id}}-input-uploaded-file-name">
                                                            @if($section->has_attachment_translation($language->id) && !isset($section->eap_section_attachment()->where('language_id', $language->id)->first()->same))
                                                                    {{$section->eap_section_attachment()->where('language_id', $language->id)->first()->filename}}
                                                                @else
                                                                    {{__('eap-online.videos.attachment_placeholder')}}
                                                                @endif
                                                        </span>
                                                        </div>
                                                    </div>
                                                    <input class="d-none"
                                                           id="section-file-{{$section->id}}-{{$language->id}}-input"
                                                           name="sections[{{$iterator}}][file][{{$language->id}}]"
                                                           type="file">
                                                </div>
                                                <label class="container checkbox-container ml-3 mb-0 checkbox-same @if($section->has_attachment_translation($language->id) && !isset($section->eap_section_attachment()->where('language_id', $language->id)->first()->same)) d-none @endif"
                                                       id="section-file-{{$section->id}}-{{$language->id}}-input-same">{{__('eap-online.same')}}
                                                    <input type="checkbox"
                                                           name="sections[{{$iterator}}][file][{{$language->id}}]"
                                                           @if(isset($section->eap_section_attachment()->where('language_id', $language->id)->first()->same)) checked
                                                           @endif
                                                           onclick="triggerSame('section-file-holder-{{$section->id}}-{{$language->id}}', this, {{$section->id}}, {{$language->id}})"
                                                    >
                                                    <span class="checkmark"></span>
                                                </label>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @elseif($section->type =='image')
                        <div class="col-12 input">
                            <div class="row">
                                <div class="col-12 pl-3 d-flex justify-content-between align-items-center mb-3 line"
                                     onclick="toggleTranslationSection('image-{{$section->id}}-translations', this)"
                                >
                                    <p class="m-0 mr-3">{{ucfirst($section->type)}}</p>
                                    <div class="d-flex align-items-center">
                                        <div class="d-flex flex-wrap">
                                            @foreach($languages as $language)
                                                @if(!empty($section->has_attachment_translation($language->id)))
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
                            <div class="d-none" id="image-{{$section->id}}-translations">
                                @foreach($languages as $language)
                                    @if($language->id != $article->input_language)
                                        <div class="row translation mb-3" style="min-height: 48px">
                                            <div class="col-1 text-center" style="padding-top:15px;">
                                                {{$language->code}}
                                            </div>
                                            <div class="col-8 pl-0 d-flex align-items-center">
                                                <div id="section-image-holder-{{$section->id}}-{{$language->id}}"
                                                     class="align-items-center justify-content-between pt-2 col-8 @if(isset($section->eap_section_attachment()->where('language_id', $language->id)->first()->same)) d-none @else d-flex @endif"
                                                >
                                                    <div class="d-flex flex-column">
                                                        <div class="d-flex align-items-center mb-3"
                                                             style="cursor: pointer">

                                                            <img class="ml-n1 mr-1 @if($section->has_attachment_translation($language->id) && !isset($section->eap_section_attachment()->where('language_id', $language->id)->first()->same)) d-none @endif"
                                                                 onclick="triggerFileUpload('section-image-{{$section->id}}-{{$language->id}}-input')"
                                                                 id="section-image-{{$section->id}}-{{$language->id}}-input-file-upload-trigger"
                                                                 src="{{asset('assets/img/eap-online/image.svg')}}"
                                                            >


                                                            <img onclick="deleteUploadedFile('section-image-{{$section->id}}-{{$language->id}}-input', {{$section->id}}, {{$language->id}})"
                                                                 class="mr-1 @if(!$section->has_attachment_translation($language->id) || isset($section->eap_section_attachment()->where('language_id', $language->id)->first()->same)) d-none @endif"
                                                                 id="section-image-{{$section->id}}-{{$language->id}}-input-file-delete-trigger"
                                                                 src="{{asset('assets/img/eap-online/trash.svg')}}"
                                                            >

                                                            <span id="section-image-{{$section->id}}-{{$language->id}}-input-uploaded-file-name">
                                                            @if($section->has_attachment_translation($language->id) && !isset($section->eap_section_attachment()->where('language_id', $language->id)->first()->same))
                                                                    {{$section->eap_section_attachment()->where('language_id', $language->id)->first()->filename}}
                                                                @else
                                                                    {{__('eap-online.lead_page.image_upload_text')}}
                                                                @endif
                                                        </span>
                                                        </div>
                                                        @if($section->has_attachment_translation($language->id) && !isset($section->eap_section_attachment()->where('language_id', $language->id)->first()->same))
                                                            <img src="/assets/eap-online/section-attachments/{{$section->eap_section_attachment()->where('language_id', $language->id)->first()->filename}}"
                                                                 alt="preview"
                                                                 id="section-image-{{$section->id}}-{{$language->id}}-input-thumbnail-preview"
                                                                 width="200px" style="border:2px solid #4dc0b5">
                                                        @else
                                                            <img src=""
                                                                 class="d-none"
                                                                 alt="preview"
                                                                 id="section-image-{{$section->id}}-{{$language->id}}-input-thumbnail-preview"
                                                                 width="200px" style="border:2px solid #4dc0b5">
                                                        @endif
                                                    </div>
                                                    <input class="d-none"
                                                           id="section-image-{{$section->id}}-{{$language->id}}-input"
                                                           name="sections[{{$iterator}}][image][{{$language->id}}]"
                                                           type="file">
                                                </div>
                                                <label class="container checkbox-container ml-3 mb-0 checkbox-same @if($section->has_attachment_translation($language->id) && !isset($section->eap_section_attachment()->where('language_id', $language->id)->first()->same)) d-none @endif"
                                                       id="section-image-{{$section->id}}-{{$language->id}}-input-same">{{__('eap-online.same')}}
                                                    <input type="checkbox"
                                                           name="sections[{{$iterator}}][image][{{$language->id}}]"
                                                           @if(isset($section->eap_section_attachment()->where('language_id', $language->id)->first()->same)) checked
                                                           @endif
                                                           onclick="triggerSame('section-image-holder-{{$section->id}}-{{$language->id}}', this,  {{$section->id}}, {{$language->id}})"
                                                    >
                                                    <span class="checkmark"></span>
                                                </label>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="col-12 input">
                            <div class="row">
                                <div class="col-12 pl-3 d-flex justify-content-between align-items-center mb-3 line"
                                     onclick="toggleTranslationSection('title-{{$section->id}}-translations', this)"
                                >
                                    <p class="m-0 mr-3">{{ucfirst($section->type)}}</p>
                                    <div class="d-flex align-items-center">
                                        <div class="d-flex flex-wrap">
                                            @foreach($languages as $language)
                                                @if(!empty($section->hasTranslation($language->id)))
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
                    @endif
                    @php $iterator++; @endphp
                @endforeach
            </div>
            <div class="row col-12 mt-1 mb-4 d-flex">
                <button class="w-auto btn-radius" type="submit">
                    <img class="mr-1" src="{{asset('assets/img/save.svg')}}" style="height: 20px; width: 20px" alt="">
                    <span class="mt-1">{{__('common.save')}}</span>
                </button>
            </div>
        </form>
        <div class="row col-4 col-lg-2 back-button mb-5">
            <a href="{{ route('admin.eap-online.articles.translate.list') }}">{{__('common.back-to-list')}}</a>
        </div>
    </div>
@endsection
