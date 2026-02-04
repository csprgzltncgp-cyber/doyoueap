function triggerSame(id, element, section_id, language_id) {
    if (element.checked) {
        $('#' + id).addClass('d-none').removeClass('d-flex');
    } else {
        $('#' + id).addClass('d-flex').removeClass('d-none');
        delete_section_attachment_translation(section_id, language_id);
    }
}

function delete_section_attachment_translation(section_id, language_id) {
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'POST',
        url: '/ajax/delete_video_attachment_translation',
        data: {
            section_id: section_id,
            language_id: language_id
        }
    });
}

function triggerFileUpload(id, type = 'image') {
    changeFileEvent(id, type)
    $(`input[id="${id}"]`).trigger('click');
}

function deleteUploadedFile(id, section_id, language_id, type = 'image') {
    Swal.fire({
        title: 'Biztosan törölni szeretné?',
        text: "A művelet nem visszavonható!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Igen, törlöm!'
    }).then((result) => {
        if (result.value) {
            changeFileEvent(id, type);
            $(`input[id="${id}"]`).val('');
            if (type == 'image') {
                $(`#${id}-uploaded-file-name`).html(picture_upload_trans);
            } else {
                $(`#${id}-uploaded-file-name`).html(file_upload_trans);
            }
            $(`#${id}-file-upload-trigger`).removeClass('d-none');
            $(`#${id}-file-delete-trigger`).addClass('d-none');
            $(`#${id}-thumbnail-preview`).addClass('d-none');
            $(`#${id}-same`).removeClass('d-none').addClass('d-flex');
            delete_section_attachment_translation(section_id, language_id);
        }
    });
}

function changeFileEvent(id, type = '') {
    $(`#${id}`).on('change', function () {
        readImageUrl(this);
        const filename = $(this).val().replace(/C:\\fakepath\\/i, '');
        const id = $(this).attr('id');
        $(`#${id}-uploaded-file-name`).html(filename);
        if (type == 'image') {
            $(`#${id}-file-upload-trigger`).addClass('d-none').prop("src", `${window.location.origin}/assets/img/eap-online/image.svg`);
        } else {
            $(`#${id}-file-upload-trigger`).addClass('d-none').prop("src", `${window.location.origin}/assets/img/eap-online/file_icon.svg`);
        }
        $(`#${id}-file-delete-trigger`).removeClass('d-none').prop("src", `${window.location.origin}/assets/img/eap-online/trash.svg`);

        $(`#${id}-same`).removeClass('d-flex').addClass('d-none');
    });
}

function readImageUrl(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        const id = input.getAttribute('id');
        reader.onload = function (e) {
            $(`#${id}-thumbnail-preview`).attr('src', e.target.result).removeClass('d-none');
        }
        reader.readAsDataURL(input.files[0]);
    }
}
