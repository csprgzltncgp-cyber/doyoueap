@extends('layout.master')

@section('title')
    Admin Dashboard
@endsection

@section('extra_css')
    <link rel="stylesheet" href="/assets/css/cases/view.css?t={{time()}}">
    <link rel="stylesheet" href="/assets/css/eap-online/translations.css?t={{time()}}">
@endsection
@section('extra_js')
    <script>
        const picture_upload_trans = "{{__('eap-online.articles.thumbnail_button')}}";
        const file_upload_trans = "{{__('eap-online.videos.attachment_placeholder')}}";
    </script>
    <script src="/assets/js/eap-online/translations.js?v={{time()}}" charset="utf-8"></script>
    <script src="/assets/js/eap-online/footer-document-translation.js?v={{time()}}" charset="utf-8"></script>
@endsection

@section('content')
    <div class="row">
        <div class="col-12 mb-5 p-0">
            {{Breadcrumbs::render('eap-online.translate-footer-document', $menu_point)}}
            <h1>EAP online - {{__('eap-online.footer.documents.translate')}}</h1>
        </div>
        <form method="post" class="row w-100" enctype="multipart/form-data">
            <div class="col-12">
                {{csrf_field()}}
                @foreach($documents as $document)
                    <div class="col-12 input">
                        <div class="row">
                            <div class="col-12 pl-3 d-flex justify-content-between align-items-center mb-3 line"
                                 onclick="toggleTranslationSection('document-{{$document->id}}-translations', this)"
                            >
                                <p class="m-0 mr-3">{{$document->name}}</p>
                                <div class="d-flex align-items-center">
                                    <div class='d-flex flex-wrap'>
                                        @foreach($languages as $language)
                                            @if($language->id != $document->language_id)
                                                @if(!empty($document->has_translation($language->id)))
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
                                            @endif
                                        @endforeach
                                    </div>
                                        <svg xmlns="http://www.w3.org/2000/svg" style="min-width: 25px; height: 25px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                        </svg>
                                </div>
                            </div>
                        </div>
                        <div class="d-none" id="document-{{$document->id}}-translations">
                            @foreach($languages as $language)
                                @if($language->id != $document->language_id)
                                    <div class="row translation mb-4 align-items-center" style="min-height: 48px">
                                        <div class="col-1 text-center" style="padding-top:15px;">
                                            {{$language->code}}
                                        </div>
                                        <div class="col-11 pl-0 d-flex align-items-center">
                                            <input type="text" class="mr-5 mb-0"
                                                   @if($document->has_translation($language->id)) value="{{$document->get_translation($language->id)->name}}"
                                                   @endif name="documents[{{$document->id}}][{{$language->id}}][name]">
                                            <textarea class="mb-0" name="documents[{{$document->id}}][{{$language->id}}][description]" id="" cols="60" rows="1">@if($document->has_description_translation($language->id)) {{ $document->get_description_translation($language->id)->description }} @endif</textarea>
                                            <div id="document-file-holder-{{$document->id}}-{{$language->id}}"
                                                 class="align-items-center justify-content-between pt-2 col-6 d-flex mb-2"
                                            >
                                                <div class="d-flex flex-column">
                                                    <div class="d-flex align-items-center"
                                                         style="cursor: pointer">

                                                        <svg class="ml-n1 mr-1 @if($document->has_translation($language->id)) d-none @endif"
                                                             onclick="triggerFileUpload('document-file-{{$document->id}}-{{$language->id}}-input')"
                                                             id="document-file-{{$document->id}}-{{$language->id}}-input-file-upload-trigger"
                                                             xmlns="http://www.w3.org/2000/svg"
                                                             style="color: rgb(89, 198, 198); height: 25px; width: 25px; cursor: pointer"
                                                             fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                  stroke-width="2"
                                                                  d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                                        </svg>

                                                        <svg class="ml-n1 mr-1 @if(!$document->has_translation($language->id)) d-none @endif"
                                                             onclick="deleteUploadedFile('document-file-{{$document->id}}-{{$language->id}}-input', {{$document->id}}, {{$language->id}})"
                                                             id="document-file-{{$document->id}}-{{$language->id}}-input-file-delete-trigger"
                                                             xmlns="http://www.w3.org/2000/svg"
                                                             style="color: rgb(89, 198, 198); height: 25px; width: 25px; cursor: pointer"
                                                             fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                  stroke-width="2"
                                                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                        </svg>

                                                        <span id="document-file-{{$document->id}}-{{$language->id}}-input-uploaded-file-name">
                                                            @if($document->has_translation($language->id))
                                                                {{substr($document->get_translation($language->id)->path, strrpos($document->get_translation($language->id)->path, '/') + 1)}}
                                                            @else
                                                                {{__('eap-online.videos.attachment_placeholder')}}
                                                            @endif
                                                        </span>
                                                    </div>
                                                </div>
                                                <input class="d-none"
                                                       id="document-file-{{$document->id}}-{{$language->id}}-input"
                                                       name="documents[{{$document->id}}][{{$language->id}}][file]"
                                                       type="file">
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="col-12 mt-1 mb-4 d-flex">
                <button class="w-auto btn-radius" type="submit">
                    <img class="mr-1" src="{{asset('assets/img/save.svg')}}" style="height: 20px; width: 20px" alt="">
                    <span class="mt-1">{{__('common.save')}}</span>
                </button>
            </div>
        </form>
        <div class="row col-4 col-lg-2 back-button mb-5">
            <a href="{{ route('admin.eap-online.footer.document.translate.list') }}">{{__('common.back-to-list')}}</a>
        </div>
    </div>
    </div>
@endsection
