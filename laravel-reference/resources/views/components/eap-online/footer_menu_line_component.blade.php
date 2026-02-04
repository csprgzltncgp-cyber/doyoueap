<div class="row col-12 d-flex flex-column mb-5">
    <div class="d-flex">
        <input type="text" class="w-25 mr-5 menu-point-inputs" value="{{$menu_point->firstTranslation->language->name}}" readonly>
        <input  required class="w-25 menu-point-inputs" type="text" value="{{$menu_point->firstTranslation->value}}"
               name="existing_menu_points[{{$menu_point->id}}][name]">
        <button style="background-color: rgb(0,87,95) !important; --btn-height: 48px; --btn-min-width: auto; --btn-padding-x:15px;" onclick="deleteMenuPoint({{$menu_point->id}})" class="text-center w-auto h-100 ml-5 btn-radius" type="button">
            <svg xmlns="http://www.w3.org/2000/svg" style="height:20px; width:20px" fill="none" viewBox="0 0 24 24"
                 stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
        </button>
    </div>


    @foreach($menu_point->eap_footer_menu_documents as $document)
        <div class="d-flex align-items-center mb-4">
            <input class="w-25 mb-0" type="text" value="{{$document->name}}" required
                   name="existing_menu_points[{{$menu_point->id}}][documents][{{$document->id}}][name]">
            <textarea class="ml-5 w-25 mb-0" style="resize: vertical;" name="existing_menu_points[{{$menu_point->id}}][documents][{{$document->id}}][description]" id="" cols="30" rows="1" placeholder="{{ __('eap-online.footer.documents.file_description')}}">{{$document->description}}</textarea>
            <div class="w-25 ml-5 d-flex align-items-center justify-content-start">
                <svg class="ml-n1 mr-1"
                     onclick="deleteUploadedFile('document-file-{{$document->id}}-{{$menu_point->firstTranslation->language->id}}-input', {{$document->id}}, {{$menu_point->firstTranslation->language->id}})"
                     id="document-file-{{$document->id}}-{{$menu_point->firstTranslation->language->id}}-input-file-delete-trigger"
                     xmlns="http://www.w3.org/2000/svg"
                     style="color: rgb(89, 198, 198); height: 20px; width: 20px; cursor: pointer;"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          stroke-width="2"
                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>

                <svg class="ml-n1 mr-1 d-none"
                     onclick="triggerFileUpload('document-file-{{$document->id}}-{{$menu_point->firstTranslation->language->id}}-input')"
                     id="document-file-{{$document->id}}-{{$menu_point->firstTranslation->language->id}}-input-file-upload-trigger"
                     xmlns="http://www.w3.org/2000/svg"
                     style="color: rgb(89, 198, 198); height: 20px; width: 20px; cursor: pointer; flex: 1 0 auto;"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          stroke-width="2"
                          d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>


                <span id="document-file-{{$document->id}}-{{$menu_point->firstTranslation->language->id}}-input-uploaded-file-name">
                {{Str::limit(substr($document->path, strrpos($document->path, '/') + 1), 25)}}
            </span>

                <input class="d-none"
                       id="document-file-{{$document->id}}-{{$menu_point->firstTranslation->language->id}}-input"
                       type="file"
                       name="existing_menu_points[{{$menu_point->id}}][documents][{{$document->id}}][file]">
            </div>
            <button class="text-center w-auto h-100 ml-5 btn-radius" style="--btn-height: 48px; --btn-min-width: auto; --btn-padding-x:15px; --btn-margin-bottom: 0px;" type="button">
                <svg
                        onclick="deleteDocument({{$document->id}})"
                        xmlns="http://www.w3.org/2000/svg"
                        style="height: 20px; width: 20px; color: white; cursor: pointer;" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </button>
        </div>
    @endforeach

    <div id="existingDocumentHolder-{{$menu_point->id}}"></div>

    <div class="row">
        <div class="col-12 d-flex flex-column">
            <div class="d-flex mb-3">
                <span style="cursor: pointer" class="new-quiz-section mr-3"  onclick="addDocument({{$menu_point->id}}, true)">+</span>
                <span style="cursor: pointer" onclick="addDocument({{$menu_point->id}}, true)">{{__('eap-online.footer.menu_points.add_document')}}</span>
            </div>
        </div>
    </div>
</div>
