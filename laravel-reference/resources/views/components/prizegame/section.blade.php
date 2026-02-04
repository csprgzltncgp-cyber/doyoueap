<div id="existing-secton-{{$section->id}}">
    <h1 class="sectionHeader">
        @switch($section->type)
            @case(\App\Models\PrizeGame\Section::TYPE_HEADLINE)
                {{__('prizegame.pages.headline')}}
                @break
            @case(\App\Models\PrizeGame\Section::TYPE_SUB_HEADLINE)
                {{__('prizegame.pages.sub_headline')}}
                @break
            @case(\App\Models\PrizeGame\Section::TYPE_BODY)
                {{__('prizegame.pages.body')}}
                @break
            @case(\App\Models\PrizeGame\Section::TYPE_LIST)
                {{__('prizegame.pages.list')}}
                @break
            @case(\App\Models\PrizeGame\Section::TYPE_CHECKBOX)
                {{__('prizegame.pages.checkbox')}}
                @break
        @endswitch
    </h1>
    <div class="row">
        <input type="hidden" name="sections[{{$section->id}}][block]"
               value="{{$block_id}}">
        <div class="col-8">
                 <textarea name="sections[{{$section->id}}][value]" cols="30" rows="5"
                           style="margin: 0 !important;">{{$section->get_translation($language)}}</textarea>
        </div>

        <div class="@if(($section->block == 3) || ($content_type_id == 5 && $section->block == 1)) d-none @else d-flex @endif col-2 flex-column @if($content_type_id == 5) justify-content-start @else justify-content-between @endif">
            <label class="container @if($content_type_id == 5) mb-3 @endif"
                   id="customer-satisfaction-not-possible">{{__('prizegame.pages.body')}}
                <input type="radio" name="sections[{{$section->id}}][type]"
                       @if($section->type == \App\Models\PrizeGame\Section::TYPE_BODY) checked="checked"
                       @endif value="{{\App\Models\PrizeGame\Section::TYPE_BODY}}">
                <span class="checkmark" onclick='changeSectionHeader(this, "{{$section->id}}")'></span>
            </label>
            <label class="container @if($content_type_id == 5) d-none @endif"
                   id="customer-satisfaction-not-possible">{{__('prizegame.pages.headline')}}
                <input type="radio" name="sections[{{$section->id}}][type]"
                       value="{{\App\Models\PrizeGame\Section::TYPE_HEADLINE}}"
                       @if($section->type == \App\Models\PrizeGame\Section::TYPE_HEADLINE) checked="checked" @endif>
                <span class="checkmark" onclick='changeSectionHeader(this, "{{$section->id}}")'></span>
            </label>
            <label class="container @if($content_type_id == 5) mb-3 @endif"
                   id="customer-satisfaction-not-possible">{{__('prizegame.pages.sub_headline')}}
                <input type="radio" name="sections[{{$section->id}}][type]"
                       value="{{\App\Models\PrizeGame\Section::TYPE_SUB_HEADLINE}}"
                       @if($section->type == \App\Models\PrizeGame\Section::TYPE_SUB_HEADLINE) checked="checked" @endif>
                <span class="checkmark" onclick='changeSectionHeader(this, "{{$section->id}}")'></span>
            </label>
            <label class="container @if($content_type_id == 5) d-none @endif"
                   id="customer-satisfaction-not-possible">{{__('prizegame.pages.list')}}
                <span class="d-none">{{__('eap-online.articles.separate_lines_by_enter')}}</span>
                <input type="radio" name="sections[{{$section->id}}][type]"
                       value="{{\App\Models\PrizeGame\Section::TYPE_LIST}}"
                       @if($section->type == \App\Models\PrizeGame\Section::TYPE_LIST) checked="checked" @endif>
                <span class="checkmark" onclick='changeSectionHeader(this, "{{$section->id}}")'></span>
            </label>
            <label class="container @if($content_type_id == 5) d-none @endif"
                   id="customer-satisfaction-not-possible">{{__('prizegame.pages.checkbox')}}
                <input type="radio" name="sections[{{$section->id}}][type]"
                       value="{{\App\Models\PrizeGame\Section::TYPE_CHECKBOX}}"
                       @if($section->type == \App\Models\PrizeGame\Section::TYPE_CHECKBOX) checked="checked" @endif>
                <span class="checkmark" onclick='changeSectionHeader(this, "{{$section->id}}")'></span>
            </label>
        </div>

        @if(!strstr(url()->current(), 'save-as') && ($section->block == 1 && $content_type_id != 5) || ($section->block != 1 && $content_type_id == 5))
            <div class="col-2 d-flex flex-column justify-content-center">
                <button type="button" class="btn-radius"
                        onclick="deleteExistingSection({{$section->id}}, 'existing-secton-{{$section->id}}')">
                    <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                         style="height: 20px; margin-bottom: 3px" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2"
                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    <span>{{__('common.delete')}}</span>
                </button>
            </div>
        @endif
    </div>
    @if($section->block != 1)
        <div
            class=" @if($section->type == \App\Models\PrizeGame\Section::TYPE_CHECKBOX) d-flex @else d-none @endif align-items-center justify-content-between"
            style=" padding-top: 30px"
            id="upload-container-{{$section->id}}">
            <div class="d-flex flex-column">
                <div class="d-flex align-items-center mb-3"
                     style="cursor: pointer">
                    <svg onclick="triggerFileUpload('section-file-{{$section->id}}')"
                         id="section-file-{{$section->id}}-input-file-upload-trigger"
                         class="@if($section->documents) d-none @endif  ml-n1 mr-1"
                         xmlns="http://www.w3.org/2000/svg"
                         style="color: rgb(89, 198, 198); height: 25px; width: 25px; cursor: pointer"
                         fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2"
                              d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    <svg
                        onclick="deleteUploadedFile('section-file-{{$section->id}}-input', '{{__('prizegame.pages.file')}}', '{{$section->id}}', 'document')"
                        class="@if(!$section->documents) d-none @endif mr-1 ml-n1"
                        id="section-file-{{$section->id}}-input-file-delete-trigger"
                        xmlns="http://www.w3.org/2000/svg"
                        style="color: rgb(89, 198, 198); height: 25px; width: 25px; cursor: pointer"
                        fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2"
                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    @if($section->documents)
                        <span id="section-file-{{$section->id}}-input-uploaded-file-name"
                        >{{$section->documents->filename}}</span>
                    @else
                        <span id="section-file-{{$section->id}}-input-uploaded-file-name"
                        >{{__('prizegame.pages.file')}}</span>
                    @endif
                </div>
            </div>
            <input class="d-none"
                   id="section-file-{{$section->id}}-input"
                   name="sections[{{$section->id}}][document][file]" type="file">

            @if($section->documents)
                <input type="text" name="sections[{{$section->id}}][document][name]" class="col-6"
                       placeholder="..." value="{{$section->documents->get_translation($language)}}">
            @else
                <input type="text" name="sections[{{$section->id}}][document][name]" class="col-6"
                       placeholder="...">
            @endif
        </div>
    @endif
    <input type="hidden" name="sections[{{$section->id}}][id]" value="{{$section->id}}">
</div>
