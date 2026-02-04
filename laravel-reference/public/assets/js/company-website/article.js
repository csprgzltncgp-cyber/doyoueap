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
                url: '/ajax/company-website/delete-existing-article-section',
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

function saveLanguage() {
    $('#modal-language-select').modal('hide');
    $('input[name="language"]').val($('select[name="article_language"]').val());
    $('#language-select-button').text(
        $('select[name="article_language"] option:selected').text()
    );
    $('#language-select-button').removeClass('error');
}

function openModal(id) {
    $(`#${id}`).modal('show');
}

function deleteResource() {
    Swal.fire({
        title: 'Biztosan törölni szeretné?',
        text: 'A művelet nem visszavonható!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Igen, törlöm!',
    }).then((result) => {
        if (result.value) {
            $('#deleteForm').submit();
        }
    });
}
