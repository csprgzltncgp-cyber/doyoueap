const idsToSave = [];
let sectionIndex = 0;

$(function () {
    changeFileEvent('image');
    readImageUrl($('#thumbnail-preview-input'));
    $('input[type="file"]').on('change', function () {
        readImageUrl(this);
    });
});

function triggerFileUpload(id) {
    $(`input[id="${id}-input"]`).trigger('click');
}

function changeFileEvent(type = '') {
    $('input[type="file"]').each(function () {
        $(this).on('change', function () {
            const filename = $(this)
                .val()
                .replace(/C:\\fakepath\\/i, '');
            const id = $(this).attr('id');
            $(`#${id}-uploaded-file-name`).html(filename);
            $(`#${id}-file-upload-trigger`).addClass('d-none');
            $(`#${id}-file-delete-trigger`).removeClass('d-none');
        });
    });
}

function deleteUploadedFile(id, type = 'image') {
    Swal.fire({
        title: 'Biztosan törölni szeretné?',
        text: 'A művelet nem visszavonható!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Igen, törlöm!',
    }).then((result) => {
        if (result.value) {
            $(`input[id="${id}"]`).val('');
            if (type == 'image') {
                $(`#${id}-uploaded-file-name`).html(picture_upload_trans);
            } else {
                $(`#${id}-uploaded-file-name`).html(file_upload_trans);
            }
            $(`#${id}-file-upload-trigger`).removeClass('d-none');
            $(`#${id}-file-delete-trigger`).addClass('d-none');
            $(`#${id}-thumbnail-preview`).addClass('d-none');
        }
    });
}

function deleteArticleSection(element) {
    Swal.fire({
        title: 'Biztos, hogy törölni szeretné?',
        text: 'A művelet nem visszavonható!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Igen, törlöm!',
        cancelButtonText: 'Mégsem',
    }).then(function (result) {
        if (result.value) {
            const holder = $(element).parent().parent().parent();
            holder.remove();
            sectionIndex--;
        }
    });
}

function deleteExistingArticleSection(id, element_id) {
    Swal.fire({
        title: 'Biztos, hogy törölni szeretné?',
        text: 'A művelet nem visszavonható!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Igen, törlöm!',
        cancelButtonText: 'Mégsem',
    }).then(function (result) {
        if (result.value) {
            $(`#${element_id}`).remove();

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                        'content'
                    ),
                },
                type: 'POST',
                url: '/ajax/delete-existing-article-section',
                data: {
                    id: id,
                },
            });
        }
    });
}

function newArticleSection(lastId) {
    const html = `
       <div>
         <h1 class="sectionHeader">${body_trans}</h1>
         <div class="row">
             <div class="col-8">
                 <textarea name="sections[${
                     sectionIndex + lastId
                 }][content]" cols="30" rows="5"
                           style="margin: 0 !important;"></textarea>
             </div>
             <div class="col-2 d-flex flex-column justify-content-between">
                 <label class="container"
                        id="customer-satisfaction-not-possible">${body_trans}
                     <input type="radio" name="sections[${
                         sectionIndex + lastId
                     }][type]" checked="checked" value="body">
                     <span class="checkmark" onclick="changeSectionHeader(this)"></span>
                 </label>
                 <label class="container"
                        id="customer-satisfaction-not-possible">${highlight_trans}
                     <input type="radio" name="sections[${
                         sectionIndex + lastId
                     }][type]" value="highlight">
                     <span class="checkmark" onclick="changeSectionHeader(this)"></span>
                 </label>
                  <label class="container"
                        id="customer-satisfaction-not-possible">${subtitle_trans}
                     <input type="radio" name="sections[${
                         sectionIndex + lastId
                     }][type]" value="subtitle">
                     <span class="checkmark" onclick="changeSectionHeader(this)"></span>
                 </label>
                 <label class="container"
                        id="customer-satisfaction-not-possible">${list_trans}
                      <span class="d-none">${list_alt_trans}</span>
                     <input type="radio" name="sections[${
                         sectionIndex + lastId
                     }][type]" value="list">
                     <span class="checkmark" onclick="changeSectionHeader(this)"></span>
                 </label>
             </div>
               <div class="col-2 d-flex flex-column justify-content-center">
                <button type="button" class="btn-radius" onclick="deleteArticleSection(this)">
                    <svg class="mr-1"  xmlns="http://www.w3.org/2000/svg" style="height: 20px; margin-bottom: 3px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg><span>${delete_trans}</span></button>
               </div>
         </div>
       </div>
    `;

    $('#articleSections').append(html);
    sectionIndex++;
}

function newImageSection(lastId) {
    const html = `
    <div id="section-image-holder-${
        Number(sectionIndex) + Number(lastId)
    }" class="d-flex align-items-center justify-content-between" style=" padding-top: 30px">
    <div class="d-flex flex-column">
     <h1>${picture_trans}</h1>
     <div class="d-flex align-items-center mb-3"
                                 style="cursor: pointer">

                                  <svg
                                                          onclick="triggerFileUpload('${
                                                              'section-image-' +
                                                              (Number(
                                                                  sectionIndex
                                                              ) +
                                                                  lastId)
                                                          }')"
                                                            id="${
                                                                'section-image-' +
                                                                Number(
                                                                    sectionIndex +
                                                                        Number(
                                                                            lastId
                                                                        )
                                                                )
                                                            }-input-file-upload-trigger"
                                                            style="color: rgb(89,198,198); height: 50px; width: 50px; cursor: pointer"
                                                            xmlns="http://www.w3.org/2000/svg"
                                                           class="ml-n1 mr-1"
                                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>


                              <svg onclick="deleteUploadedFile('${
                                  'section-image-' +
                                  (Number(sectionIndex) + Number(lastId))
                              }-input')"
                                                         class="d-none mr-1 ml-n1"
                                                        id="${
                                                            'section-image-' +
                                                            (Number(
                                                                sectionIndex
                                                            ) +
                                                                Number(lastId))
                                                        }-input-file-delete-trigger"
                                                         xmlns="http://www.w3.org/2000/svg"
                                                         style="color: rgb(89, 198, 198); height: 50px; width: 50px; cursor: pointer"
                                                         fill="none"
                                                         viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                              stroke-width="2"
                                                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>

                                <span id="${
                                    'section-image-' +
                                    (Number(sectionIndex) + Number(lastId))
                                }-input-uploaded-file-name"
                                >${picture_upload_trans}</span>
     </div>
                            <img class="d-none" src="#" alt="preview" id="${
                                'section-image-' +
                                (Number(sectionIndex) + Number(lastId))
                            }-input-thumbnail-preview"
                             width="200px" style="border:2px solid #4dc0b5">
             </div>
             <input type="hidden" name="sections[${
                 Number(sectionIndex) + Number(lastId)
             }][type]" value="image" />
                             <input class="d-none" id="${
                                 'section-image-' +
                                 (Number(sectionIndex) + Number(lastId))
                             }-input" name="sections[${
        Number(sectionIndex) + Number(lastId)
    }][content]" type="file">
                             <div class="col-2 d-flex flex-column justify-content-center pr-0" style="padding-left:25px">
                <button type="button" class="btn-radius" onclick="deleteFileSection('section-image-holder-${
                    Number(sectionIndex) + Number(lastId)
                }')">
                    <svg class="mr-1" xmlns="http://www.w3.org/2000/svg" style="height: 20px; margin-bottom: 3px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg><span>${delete_trans}</span></button>
               </div>
                             </div>
    `;

    $('#articleSections').append(html);
    changeFileEvent('image');
    readImageUrl(
        $(
            `#${
                'section-image-' + (Number(sectionIndex) + Number(lastId))
            }-input`
        )
    );
    $(
        `#${'section-image-' + (Number(sectionIndex) + Number(lastId))}-input`
    ).on('change', function () {
        readImageUrl(this);
    });
    sectionIndex++;
}

function newFileSection(lastId) {
    const html = `
         <div id="section-file-holder-${
             Number(sectionIndex) + Number(lastId)
         }" class="d-flex align-items-center justify-content-between" style=" padding-top: 30px">
    <div class="d-flex flex-column">
     <h1>${document_trans}</h1>
     <div class="d-flex align-items-center mb-3"
                                 style="cursor: pointer">

                                   <svg onclick="triggerFileUpload('${
                                       'section-file-' +
                                       (Number(sectionIndex) + lastId)
                                   }')"
                                                         id="${
                                                             'section-file-' +
                                                             Number(
                                                                 sectionIndex +
                                                                     Number(
                                                                         lastId
                                                                     )
                                                             )
                                                         }-input-file-upload-trigger"
                                                      class="ml-n1 mr-1"
                                                         xmlns="http://www.w3.org/2000/svg"
                                                         style="color: rgb(89, 198, 198); height: 50px; width: 50px; cursor: pointer"
                                                         fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                              stroke-width="2"
                                                              d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                                    </svg>

  <svg onclick="deleteUploadedFile('${
      'section-file-' + (Number(sectionIndex) + Number(lastId))
  }-input', 'file')"
                                                         class="d-none mr-1 ml-n1"
                                                        id="${
                                                            'section-file-' +
                                                            (Number(
                                                                sectionIndex
                                                            ) +
                                                                Number(lastId))
                                                        }-input-file-delete-trigger"
                                                         xmlns="http://www.w3.org/2000/svg"
                                                         style="color: rgb(89, 198, 198); height: 50px; width: 50px; cursor: pointer"
                                                         fill="none"
                                                         viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                              stroke-width="2"
                                                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>


                                <span id="${
                                    'section-file-' +
                                    (Number(sectionIndex) + Number(lastId))
                                }-input-uploaded-file-name"
                                >${file_upload_trans}</span>
     </div>
             </div>
             <input type="hidden" name="sections[${
                 Number(sectionIndex) + Number(lastId)
             }][type]" value="file" />
                             <input class="d-none" id="${
                                 'section-file-' +
                                 (Number(sectionIndex) + Number(lastId))
                             }-input" name="sections[${
        Number(sectionIndex) + Number(lastId)
    }][content]" type="file">
              <div class="col-2 d-flex flex-column justify-content-center pr-0" style="padding-left:25px">
                <button type="button" class="btn-radius" onclick="deleteFileSection('section-file-holder-${
                    Number(sectionIndex) + Number(lastId)
                }')">
                    <svg class="mr-1"  xmlns="http://www.w3.org/2000/svg" style="height: 20px; margin-bottom: 3px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg><span>${delete_trans}</span></button>
               </div>
                             </div>
    `;

    $('#articleSections').append(html);
    changeFileEvent('file');
    sectionIndex++;
}

function newLinkSection(lastId) {
    const html = `
        <div  id="section-link-holder-${
            Number(sectionIndex) + Number(lastId)
        }"  class="d-flex align-items-end justify-content-between">
         <div class="d-flex flex-column col-8 row pr-0">
             <h1>Link</h1>
                <input type="hidden" name="sections[${
                    Number(sectionIndex) + Number(lastId)
                }][type]" value="link" />
                <input oninput="removeError('error-text', 'link')" type="text" placeholder="link..." name="sections[${
                    Number(sectionIndex) + Number(lastId)
                }][content]">

         </div>
          <div class="col-2 d-flex flex-column justify-content-center pr-0" style="padding-left:25px">
                <button class="btn-radius" style=" margin-bottom: 20px !important;" type="button" onclick="deleteFileSection('section-link-holder-${
                    Number(sectionIndex) + Number(lastId)
                }')">
                    <svg class="mr-1" xmlns="http://www.w3.org/2000/svg" style="height: 20px; margin-bottom: 3px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg><span>${delete_trans}</span></button>
          </div>
        </div>
    `;

    $('#articleSections').append(html);
    sectionIndex++;
}

function deleteFileSection(id) {
    Swal.fire({
        title: 'Biztos, hogy törölni szeretné?',
        text: 'A művelet nem visszavonható!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Igen, törlöm!',
        cancelButtonText: 'Mégsem',
    }).then(function (result) {
        if (result.value) {
            $(`#${id}`).remove();
            sectionIndex--;
        }
    });
}

function changeSectionHeader(element) {
    const text = $(element).closest('label').text();
    $(element).parent().parent().parent().prev('.sectionHeader').html(text);
}

function readImageUrl(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        const id = input.getAttribute('id');
        reader.onload = function (e) {
            $(`#${id}-thumbnail-preview`)
                .attr('src', e.target.result)
                .removeClass('d-none');
        };

        reader.readAsDataURL(input.files[0]);
    }
}
