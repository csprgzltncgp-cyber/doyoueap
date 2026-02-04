@extends('layout.master')

@section('title')
    Admin Dashboard
@endsection

@section('extra_css')
    <link rel="stylesheet" href="/assets/css/cases/view.css?t={{time()}}">
    <link rel="stylesheet" href="/assets/css/workshops.css?v={{time()}}">
    <link rel="stylesheet" href="/assets/css/form.css?v={{time()}}">
    <style>
        .new-quiz-section {
            height: 25px;
            width: 25px;
            background-color: rgb(89, 198, 198);
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
        }

        .menu-point-inputs {
            border-color: rgb(0, 87, 95) !important;
            color: rgb(0, 87, 95) !important;
        }

        .menu-point-inputs::-webkit-input-placeholder {
            color: rgb(0, 87, 95) !important;
        }

        .menu-point-inputs::-moz-placeholder {
            color: rgb(0, 87, 95) !important;
        }

        .menu-point-inputs::-ms-input-placeholder {
            color: rgb(0, 87, 95) !important;
        }
    </style>
@endsection

@section('extra_js')
    <script>
        const picture_upload_trans = "{{__('eap-online.articles.thumbnail_button')}}";
        const file_upload_trans = "{{__('eap-online.videos.attachment_placeholder')}}";
    </script>
    <script src="/assets/js/eap-online/footer-document-translation.js?v={{time()}}" charset="utf-8"></script>
    <script>
        let indexForNewMenuItems = 0;
        let indexForNewDocuments = 0;

        function addDocument(id, existingMenuPoint = false) {
            if (existingMenuPoint) {
                $(`#existingDocumentHolder-${id}`).append(`
          <div class="d-flex align-items-center">
            <input required class="w-25 mb-0 mr-5" type="text" name="existing_menu_points[${id}][documents][${indexForNewDocuments}][name]"
                placeholder="{{__('eap-online.footer.menu_points.document_placeholder')}}">
            <textarea class="w-25 mb-0" style="resize: vertical;" name="existing_menu_points[${id}][documents][${indexForNewDocuments}][description]" id="" cols="30" rows="1" placeholder="{{ __('eap-online.footer.documents.file_description')}}"></textarea>
            <input type="hidden" value="true" name="existing_menu_points[${id}][documents][${indexForNewDocuments}][is_new]">
        <div class="d-flex">
        <div>
            <svg class="ml-n1 mr-1"
            onclick="triggerFileUpload('document-file-${id}-${indexForNewDocuments}-input')" id="document-file-${id}-${indexForNewDocuments}-input-file-upload-trigger"
                 xmlns="http://www.w3.org/2000/svg"
                 style="color: rgb(89, 198, 198); height: 50px; width: 50px; cursor: pointer; flex: 1 0 auto;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
            </svg>


         <svg class="ml-n1 mr-1 d-none"
                     onclick="deleteUploadedFile('document-file-${id}-${indexForNewDocuments}-input', ${id}, ${indexForNewDocuments})"
                     id="document-file-${id}-${indexForNewDocuments}-input-file-delete-trigger"
                     xmlns="http://www.w3.org/2000/svg"
                     style="color: rgb(89, 198, 198); height: 50px; width: 50px; cursor: pointer; flex: 1 0 auto;"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          stroke-width="2"
                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>

            <span class="w-25" id="document-file-${id}-${indexForNewDocuments}-input-uploaded-file-name" class="mt-3">{{__('eap-online.videos.attachment_placeholder')}}</span>
            </div>
        <input type="file" class="d-none" id="document-file-${id}-${indexForNewDocuments}-input" name="existing_menu_points[${id}][documents][${indexForNewDocuments}][file]">
    </div>
        `)

            } else {
                $(`#newDocumentHolder-${id}`).append(`
          <div class="d-flex">
        <input required class="w-25 mr-5" type="text" name="new_menu_points[${id}][documents][${indexForNewDocuments}][name]"
               placeholder="{{__('eap-online.footer.menu_points.document_placeholder')}}">

        <div class="d-flex">

        <div>
        <svg class="ml-n1 mr-1 d-none"
                     onclick="deleteUploadedFile('document-file-${id}-${indexForNewDocuments}-input', ${id}, ${indexForNewDocuments})"
                     id="document-file-${id}-${indexForNewDocuments}-input-file-delete-trigger"
                     xmlns="http://www.w3.org/2000/svg"
                     style="color: rgb(89, 198, 198); height: 50px; width: 50px; cursor: pointer; flex: 1 0 auto;"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          stroke-width="2"
                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>


                    <svg class="ml-n1 mr-1"
            onclick="triggerFileUpload('document-file-${id}-${indexForNewDocuments}-input')" id="document-file-${id}-${indexForNewDocuments}-input-file-upload-trigger"
                 xmlns="http://www.w3.org/2000/svg"
                 style="color: rgb(89, 198, 198); height: 50px; width: 50px; cursor: pointer; flex: 1 0 auto;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
            </svg>
                </svg>
           <span class="w-25" id="document-file-${id}-${indexForNewDocuments}-input-uploaded-file-name">{{__('eap-online.videos.attachment_placeholder')}}</span>
        </div>
        <input type="file" class="d-none"  id="document-file-${id}-${indexForNewDocuments}-input" name="new_menu_points[${id}][documents][${indexForNewDocuments}][file]">
    </div>
        `)

            }
            indexForNewDocuments++;
            changeFileEvent();
        }

        function deleteMenuPoint(id) {
            Swal.fire({
                title: '{{__('common.are-you-sure-to-delete')}}',
                text: "{{__('common.operation-cannot-undone')}}",
                icon: 'warning',
                showCancelButton: true,
                cancelButtonText: '{{__('common.cancel')}}',
                confirmButtonText: '{{__('common.yes-delete-it')}}'
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        async: false,
                        type: 'GET',
                        url: '/ajax/delete-menu-point/' + id,
                        success: function () {
                            location.reload();
                        }
                    });
                }
            });
        }

        function deleteDocument(id) {
            Swal.fire({
                title: '{{__('common.are-you-sure-to-delete')}}',
                text: "{{__('common.operation-cannot-undone')}}",
                icon: 'warning',
                showCancelButton: true,
                cancelButtonText: '{{__('common.cancel')}}',
                confirmButtonText: '{{__('common.yes-delete-it')}}'
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        async: false,
                        type: 'GET',
                        url: '/ajax/delete-menu-point-document/' + id,
                        success: function () {
                            location.reload();
                        }
                    });
                }
            });
        }


        function newMenuPoint() {
            const html = `
<div class="row col-12 d-flex flex-column mb-5">
<div class="d-flex">
                            <select name="new_menu_points[${indexForNewMenuItems}][language_id]" class="w-25 mr-5 menu-point-inputs">
                                @foreach($languages as $language)
            <option value="{{$language->id}}">{{$language->name}}</option>
                                @endforeach
            </select>
            <input  class="w-25 menu-point-inputs" type="text"
                   placeholder="{{__('eap-online.footer.menu_points.name_placeholder')}}"
                                   name="new_menu_points[${indexForNewMenuItems}][name]" required>
</div>
                                    <div id="newDocumentHolder-${indexForNewMenuItems}"></div>
                                    <div class="row">
        <div class="col-12 d-flex flex-column">
            <div class="d-flex mb-3" style="cursor: pointer" onclick="addDocument(${indexForNewMenuItems})">
                <span class="new-quiz-section mr-3">+</span>
                <span>{{__('eap-online.footer.menu_points.add_document')}}</span>
            </div>
        </div>
    </div>
                        </div>
            `;

            $('#menuPointsHolder').append(html);
            indexForNewMenuItems++;
        }
    </script>
@endsection

@section('content')
    <div class="row">
        <div class="col-12 mb-5">
            {{ Breadcrumbs::render('eap-online.footer') }}
            <h1>EAP online - {{__('eap-online.footer.menu_points.menu')}}</h1>
        </div>

        <div class="row col-12">
            <div class="col-12">
                <form enctype="multipart/form-data" action="{{route('admin.eap-online.footer.menu.store')}}"
                      class="mw-100" method="post">
                    {{csrf_field()}}

                    <div id="menuPointsHolder">
                        @foreach($menu_points as $menu_point)
                            @component('components.eap-online.footer_menu_line_component',['menu_point' => $menu_point])@endcomponent
                        @endforeach
                    </div>

                    <div class="row col-12 d-flex">
                        <div>
                            <button style="background-color: rgb(0,87,95) !important;" class="text-center btn-radius" type="button"
                                    onclick="newMenuPoint()">
                                <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="height: 20px; width: 20px" fill="none"
                                     viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 4v16m8-8H4"/>
                                </svg>
                                {{__('eap-online.footer.menu_points.add')}}
                            </button>
                        </div>
                        <div>
                            <button style="background-color: rgb(0,87,95) !important;" class="text-center btn-radius"
                            type="submit">
                                <img class="mr-1" src="{{asset('assets/img/save.svg')}}" style="height: 20px; width: 20px" alt="">
                                <span class="mt-1">{{__('common.save')}}</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>


        <div class="col-4 col-lg-2 back-button mb-5">
            <a href="{{ route('admin.eap-online.actions') }}">{{__('common.back-to-list')}}</a>
        </div>
    </div>
@endsection
