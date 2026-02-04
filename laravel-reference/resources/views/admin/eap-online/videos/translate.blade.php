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
        const file_upload_trans = "{{__('eap-online.videos.attachment_placeholder')}}";
    </script>
    <script src="/assets/js/eap-online/translations.js?v={{time()}}" charset="utf-8"></script>
    <script src="/assets/js/eap-online/translations-video.js?v={{time()}}" charset="utf-8"></script>
@endsection

@section('content')
    <div class="row">
        <div class="col-12 mb-5 p-0">
            {{ Breadcrumbs::render('eap-online.translate-videos.view', $video) }}
            <h1>{{__('eap-online.videos.translate')}}</h1>
        </div>
        <form method="post" class="row col-12 w-100" enctype="multipart/form-data">
            <div class="col-12 row">
                {{csrf_field()}}
                <div class="col-12 input">
                    <div class="row">
                        <div class="col-12 pl-3 d-flex justify-content-between align-items-center mb-3 line"
                             onclick="toggleTranslationSection('short-title-{{$video->id}}-translations', this)"
                        >
                            <p class="m-0 mr-3">{{$video->short_title}}</p>
                            <div class="d-flex align-items-center">
                                <div class="d-flex flex-wrap">
                                    @foreach($languages as $language)
                                        @if(!empty($video->get_translation('ShortTitle', $language->id)->value))
                                            <div style="background-color:rgb(145,183,82);" class="px-2 text-white mr-3 mb-2">
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
                    </div>
                    <div class="d-none" id="short-title-{{$video->id}}-translations">
                        @foreach($languages as $language)
                            <div class="row translation">
                                <div class="col-1 text-center" style="padding-top:15px;">
                                    {{$language->code}}
                                </div>
                                <div class="col-8 pl-0">
                                    <textarea name="short_title[{{$language->id}}]"
                                              placeholder="{{__('eap-online.system.translation')}}">{{$video->get_translation('ShortTitle', $language->id)->value ?? ''}}</textarea>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="col-12 input">
                    <div class="row">
                        <div class="col-12 pl-3 d-flex justify-content-between align-items-center mb-3 line"
                             onclick="toggleTranslationSection('long-title-{{$video->id}}-translations', this)"
                        >
                            <p class="m-0 mr-3">{{$video->long_title}}</p>
                            <div class="d-flex align-items-center">
                                <div class="d-flex flex-wrap">
                                    @foreach($languages as $language)
                                        @if(!empty($video->get_translation('LongTitle', $language->id)->value))
                                            <div style="background-color:rgb(145,183,82);" class="px-2 text-white mr-3 mb-2">
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
                    </div>
                    <div class="d-none" id="long-title-{{$video->id}}-translations">
                        @foreach($languages as $language)
                            <div class="row translation">
                                <div class="col-1 text-center" style="padding-top:15px;">
                                    {{$language->code}}
                                </div>
                                <div class="col-8 pl-0">
                                    <textarea name="long_title[{{$language->id}}]"
                                              placeholder="{{__('eap-online.system.translation')}}">{{$video->get_translation('LongTitle', $language->id)->value ?? ''}}</textarea>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="col-12 input">
                    <div class="row">
                        <div class="col-12 pl-3 d-flex justify-content-between align-items-center mb-3 line"
                             onclick="toggleTranslationSection('description-first-line-{{$video->id}}-translations', this)"
                        >
                            <p class="m-0 mr-3">{{$video->description_first_line}}</p>
                            <div class="d-flex align-items-center">
                                <div class="d-flex flex-wrap">
                                    @foreach($languages as $language)
                                        @if(!empty($video->get_translation('DescriptionFirstLine', $language->id)->value))
                                            <div style="background-color:rgb(145,183,82);" class="px-2 text-white mr-3 mb-2">
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
                    </div>
                    <div class="d-none" id="description-first-line-{{$video->id}}-translations">
                        @foreach($languages as $language)
                            <div class="row translation">
                                <div class="col-1 text-center" style="padding-top:15px;">
                                    {{$language->code}}
                                </div>
                                <div class="col-8 pl-0">
                                    <textarea name="description_first_line[{{$language->id}}]"
                                              placeholder="{{__('eap-online.system.translation')}}">{{$video->get_translation('DescriptionFirstLine', $language->id)->value ?? ''}}</textarea>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="col-12 input">
                    <div class="row">
                        <div class="col-12 pl-3 d-flex justify-content-between align-items-center mb-3 line"
                             onclick="toggleTranslationSection('description-second-line-{{$video->id}}-translations', this)"
                        >
                            <p class="m-0 mr-3">{{$video->description_second_line}}</p>
                            <div class="d-flex align-items-center">
                                <div class="d-flex flex-wrap">
                                    @foreach($languages as $language)
                                        @if(!empty($video->get_translation('DescriptionSecondLine', $language->id)->value))
                                            <div style="background-color:rgb(145,183,82);" class="px-2 text-white mr-3 mb-2">
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
                    </div>
                    <div class="d-none" id="description-second-line-{{$video->id}}-translations">
                        @foreach($languages as $language)
                            <div class="row translation">
                                <div class="col-1 text-center" style="padding-top:15px;">
                                    {{$language->code}}
                                </div>
                                <div class="col-8 pl-0">
                                    <textarea name="description_second_line[{{$language->id}}]"
                                              placeholder="{{__('eap-online.system.translation')}}">{{$video->get_translation('DescriptionSecondLine', $language->id)->value ?? ''}}</textarea>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @if($video->eap_video_attachment()->first())
                    <div class="col-12 input">
                        <div class="row">
                            <div class="col-12 pl-3 d-flex justify-content-between align-items-center mb-3 line"
                                 onclick="toggleTranslationSection('button-text-{{$video->id}}-translations', this)"
                            >
                                <p class="m-0 mr-3">{{__('eap-online.videos.attachment_button_title')}}</p>
                                <div class="d-flex align-items-center">
                                    <div class="d-flex flex-wrap">
                                        @foreach($languages as $language)
                                            @if(!empty($video->eap_video_attachment()->where('language_id', $language->id)->first()->button_text))
                                                <div style="background-color:rgb(145,183,82);" class="px-2 text-white mr-3 mb-2">
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
                        </div>
                        <div class="d-none" id="button-text-{{$video->id}}-translations">
                            @foreach($languages as $language)
                                @if($language->id != $video->language)
                                    <div class="row translation">
                                        <div class="col-1 text-center" style="padding-top:15px;">
                                            {{$language->code}}
                                        </div>
                                        <div class="col-8 pl-0">
                                    <textarea name="button_text[{{$language->id}}]"
                                              placeholder="{{__('eap-online.system.translation')}}">{{$video->eap_video_attachment()->where('language_id', $language->id)->first()->button_text ?? ''}}</textarea>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    <div class="col-12 input">
                        <div class="row">
                            <div class="col-12 pl-3 d-flex justify-content-between align-items-center mb-3 line"
                                 onclick="toggleTranslationSection('file-{{$video->id}}-translations', this)"
                            >
                                <p class="m-0 mr-3">Attachment</p>
                                <div class="d-flex align-items-center">
                                    <div class="d-flex flex-wrap">
                                        @foreach($languages as $language)
                                            @if(!empty($video->has_attachment_translation($language->id)))
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
                        </div>
                        <div class="d-none" id="file-{{$video->id}}-translations">
                            @foreach($languages as $language)
                                @if($language->id != $video->language)
                                    <div class="row translation mb-3" style="min-height: 48px">
                                        <div class="col-1 text-center" style="padding-top:15px;">
                                            {{$language->code}}
                                        </div>
                                        <div class="col-8 pl-0 d-flex align-items-center">
                                            <div id="video-file-holder-{{$video->id}}-{{$language->id}}"
                                                 class="align-items-center justify-content-between pt-2 col-8 d-flex"
                                            >
                                                <div class="d-flex flex-column">
                                                    <div class="d-flex align-items-center"
                                                         style="cursor: pointer">

                                                        <img class="ml-n1 mr-1 @if($video->has_attachment_translation($language->id)) d-none @endif"
                                                             style="width: 20px;"
                                                             onclick="triggerFileUpload('video-file-{{$video->id}}-{{$language->id}}-input', 'file')"
                                                             id="video-file-{{$video->id}}-{{$language->id}}-input-file-upload-trigger"
                                                             src="{{asset('assets/img/eap-online/file_icon.svg')}}"
                                                        >


                                                        <img onclick="deleteUploadedFile('video-file-{{$video->id}}-{{$language->id}}-input', {{$video->id}}, {{$language->id}}, 'file')"
                                                             class="mr-1 @if(!$video->has_attachment_translation($language->id)) d-none @endif"
                                                             style="width: 20px;"
                                                             id="video-file-{{$video->id}}-{{$language->id}}-input-file-delete-trigger"
                                                             src="{{asset('assets/img/eap-online/trash.svg')}}"
                                                        >

                                                        <span id="video-file-{{$video->id}}-{{$language->id}}-input-uploaded-file-name">
                                                            @if($video->has_attachment_translation($language->id))
                                                                {{$video->eap_video_attachment()->where('language_id', $language->id)->first()->filename}}
                                                            @else
                                                                {{__('eap-online.videos.attachment_placeholder')}}
                                                            @endif
                                                        </span>
                                                    </div>
                                                </div>
                                                <input class="d-none"
                                                       id="video-file-{{$video->id}}-{{$language->id}}-input"
                                                       name="attachments[{{$language->id}}]"
                                                       type="file">
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif
                <input type="hidden" name="video_id" value="{{$video->id}}">
            </div>
            <div></div>
            <div class="row col-12 mt-1 mb-4 d-flex">
                <button class="w-auto btn-radius" type="submit">
                    <img class="mr-1" src="{{asset('assets/img/save.svg')}}" style="height: 20px; width: 20px" alt="">
                    <span class="mt-1">{{__('common.save')}}</span>
                </button>
            </div>
        </form>
        <div class="row col-4 col-lg-2 back-button mb-5">
            <a href="{{ route('admin.eap-online.videos.translate.list') }}">{{__('common.back-to-list')}}</a>
        </div>
    </div>
@endsection
