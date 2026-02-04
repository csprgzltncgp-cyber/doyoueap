@extends('layout.master')

@section('title')
    Admin Dashboard
@endsection

@section('extra_css')
    <link rel="stylesheet" href="/assets/css/list.css?v={{time()}}">
    <link rel="stylesheet" href="/assets/css/cases/view.css?t=<?php echo e(time()); ?>">
    <link rel="stylesheet" href="/assets/css/form.css?v={{time()}}">
    <link rel="stylesheet" href="/assets/css/eap-online/articles.css?v={{time()}}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.css"
          rel="stylesheet"/>
    <link rel="stylesheet" href="/assets/css/cases/datetime.css?t={{time()}}">
@endsection

@section('extra_js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.js" type="text/javascript"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    <script type="text/javascript">
        const body_trans = "{{__('eap-online.articles.body')}}";
        const highlight_trans = "{{__('eap-online.articles.highlight')}}";
        const list_trans = "{{__('eap-online.articles.list')}}";
        const subtitle_trans = "{{__('eap-online.articles.subtitle')}}"
        const list_alt_trans = "{{__('eap-online.articles.separate_lines_by_enter')}}";
        const delete_trans = "{{__('common.delete')}}";
        const picture_trans = "{{__('eap-online.articles.picture')}}";
        const document_trans = "{{__('eap-online.articles.document')}}";
        const picture_upload_trans = "{{__('eap-online.articles.thumbnail_button')}}";
        const file_upload_trans = "{{__('eap-online.videos.attachment_placeholder')}}";
    </script>
    <script src="/assets/js/company-website/article.js?v={{time()}}" charset="utf-8"></script>
@endsection


@section('content')
<div class="row">
    <div class="col-12">
        {{ Breadcrumbs::render('company-website.articles.edit', $article) }}
        <h1>{{ __('eap-online.articles.edit') }}</h1>
    </div>

    <div class="col-12">
        <form id="saveForm" class="mb-5"
            action="{{route('admin.company-website.articles.update', ['article' => $article])}}"
            method="post"
            style="max-width: 100%" enctype="multipart/form-data">
            {{csrf_field()}}

            <div class="row d-flex flex-column col-12">
                <h1 class="mb-3">SEO</h1>
                <input class="col-8" type="text" name="seo_title" maxlength="250" value="{{$article->seo_title}}"
                    placeholder="{{__('company-website.actions.articles.title')}}">

                <input class="col-8" type="text" name="seo_description" maxlength="250" value="{{$article->seo_description}}"
                    placeholder="{{__('company-website.actions.articles.description')}}">

                    <input class="col-8" type="text" name="seo_keywords" maxlength="250" value="{{$article->seo_keywords}}"
                    placeholder="{{__('company-website.actions.articles.keywords')}}">
            </div>

            <div class="row col-12 d-flex">
                <div class="d-flex flex-column justify-content-center">
                    <div>
                        <h1>{{__('eap-online.articles.thumbnail')}}</h1>
                        <div class="d-flex align-items-center mb-3">
                            @if($errors->has('thumbnail'))
                                <svg onclick="deleteUploadedFile('thumbnail-preview-input')"
                                     class="ml-n1 mr-1 @if(! $article->thumbnail) d-none @endif mr-1"
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
                                     class="ml-n1 mr-1 @if(! $article->thumbnail) d-none @endif"
                                     id="thumbnail-preview-input-file-delete-trigger"
                                     xmlns="http://www.w3.org/2000/svg"
                                     style="color: rgb(89, 198, 198); height: 20px; width: 20px; cursor: pointer"
                                     fill="none"
                                     viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            @endif

                            @if($errors->has('thumbnail'))
                                <svg class="ml-n1 mr-1 @if(!$article->thumbnail)d-none @endif"
                                     onclick="triggerFileUpload('thumbnail-preview')"
                                     id="thumbnail-preview-input-file-upload-trigger"
                                     style="color: #db0b20; height: 20px; width: 20px; cursor: pointer"
                                     xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                     stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            @else
                                <svg class="ml-n1 mr-1 @if($article->thumbnail)d-none @endif"
                                     onclick="triggerFileUpload('thumbnail-preview')"
                                     id="thumbnail-preview-input-file-upload-trigger"
                                     style="color: rgb(89, 198, 198); height: 20px; width: 20px; cursor: pointer"
                                     xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                     stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            @endif


                            <span id="thumbnail-preview-input-uploaded-file-name">
                                 @if($article->thumbnail)
                                    {{$article->thumbnail}}
                                @else
                                    {{__('eap-online.articles.thumbnail_button')}}
                                @endif
                            </span>
                        </div>
                    </div>
                    <img class="@if(!$article->thumbnail) d-none @endif"
                         src="@if($article->thumbnail)/assets/company-website/thumbnails/{{$article->thumbnail}}@endif"
                         alt="preview" id="thumbnail-preview-input-thumbnail-preview"
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
                                          rows="5"
                                          placeholder="{{__('eap-online.articles.headline')}}..."
                                          style="margin: 0 !important;">{{$article->getSectionByType('headline')}}</textarea>
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
                                          style="margin: 0 !important;">{{$article->getSectionByType('lead')}}</textarea>
                            </div>
                        </div>
                    </div>
                    <div>
                        @foreach($article->sections->sortBy('id') as $section)
                            @if($section->type != 'headline' && $section->type != 'lead')
                                    <div id="article-content-section-{{$loop->index}}">
                                        <h1 class="sectionHeader">{{__('eap-online.articles.' . $section->type)}}</h1>
                                        <div class="row">
                                            <div class="col-8">
                                    <textarea name="sections[{{$loop->index}}][content]" cols="30" rows="5"
                                              style="margin: 0 !important;">{{$section->translations()->where('language_id', $article->input_language)->first()->value}}</textarea>
                                            </div>
                                            <div class="col-2 d-flex flex-column justify-content-between">
                                                <label class="container checkbox-container"
                                                       id="customer-satisfaction-not-possible">{{__('eap-online.articles.body')}}
                                                    <input type="radio" name="sections[{{$loop->index}}][type]"
                                                           value="body"
                                                           @if($section->type == 'body') checked="checked" @endif>
                                                    <span class="checkmark"
                                                          onclick="changeSectionHeader(this)"></span>
                                                </label>
                                                <label class="container checkbox-container"
                                                       id="customer-satisfaction-not-possible">{{__('eap-online.articles.highlight')}}
                                                    <input type="radio" name="sections[{{$loop->index}}][type]"
                                                           value="highlight"
                                                           @if($section->type == 'highlight') checked="checked" @endif>
                                                    <span class="checkmark"
                                                          onclick="changeSectionHeader(this)"></span>
                                                </label>
                                                <label class="container checkbox-container"
                                                       id="customer-satisfaction-not-possible">{{__('eap-online.articles.subtitle')}}
                                                    <input type="radio" name="sections[{{$loop->index}}][type]"
                                                           value="subtitle"
                                                           @if($section->type == 'subtitle') checked="checked" @endif>
                                                    <span class="checkmark"
                                                          onclick="changeSectionHeader(this)"></span>
                                                </label>
                                                <label class="container checkbox-container"
                                                       id="customer-satisfaction-not-possible">{{__('eap-online.articles.list')}}
                                                    <span class="d-none">{{__('eap-online.articles.separate_lines_by_enter')}}</span>
                                                    <input type="radio" name="sections[{{$loop->index}}][type]"
                                                           value="list"
                                                           @if($section->type == 'list') checked="checked" @endif>
                                                    <span class="checkmark"
                                                          onclick="changeSectionHeader(this)"></span>
                                                </label>
                                            </div>
                                            <div class="col-2 d-flex flex-column justify-content-center">
                                                <button class="btn-radius" type="button"
                                                        onclick="deleteExistingArticleSection({{$section->id}}, 'article-content-section-{{$loop->index}}')">
                                                    <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                                                         style="height: 20px; margin-bottom: 3px" fill="none"
                                                         viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                              stroke-width="2"
                                                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                    <span>{{__('common.delete')}}</span>
                                                </button>
                                            </div>
                                        </div>
                                        <input type="hidden" name="sections[{{$loop->index}}][id]"
                                               value="{{$section->id}}">
                                    </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="row mt-5" style="margin-bottom: 400px">
                <div class="col-12 d-flex">
                    <div class="mr-3">
                        <button class="btn-radius" type="button"
                                onclick="newArticleSection({{count($article->sections)}})">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="width:20px;" fill="none" viewBox="0 0 24 24" stroke-width="2.0" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            <span>{{__('eap-online.articles.body')}}</span>
                        </button>
                    </div>
                </div>
                <div class="col-12 d-flex">
                    <div>
                        <button class="btn-radius" type="submit">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="width: 20px; height: 20px" class="mr-1 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                            </svg>
                            <span class="mr-1">{{__('common.save')}}</span>
                        </button>
                    </div>
                    <div>
                        <button class="btn-radius"
                                type="button"
                                onclick="deleteResource()"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="height: 20px; margin-bottom: 3px"
                                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            <span>
                                {{__('common.delete')}}
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </form>

        <form method="post" id="deleteForm"
          action="{{route('admin.company-website.articles.delete', ['article' => $article])}}">{{csrf_field()}}</form>
    </div>
</div>
@endsection
