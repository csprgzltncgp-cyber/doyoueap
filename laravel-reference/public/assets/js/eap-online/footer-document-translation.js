function triggerFileUpload(id) {
    changeFileEvent(id)
    $(`input[id="${id}"]`).trigger('click');
}

function deleteUploadedFile(id) {
    Swal.fire({
        title: 'Biztosan törölni szeretné?',
        text: "A művelet nem visszavonható!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Igen, törlöm!'
    }).then((result) => {
        if (result.value) {
            changeFileEvent(id);
            $(`input[id="${id}"]`).val('');
            $(`#${id}-uploaded-file-name`).html(file_upload_trans);
            $(`#${id}-file-upload-trigger`).removeClass('d-none');
            $(`#${id}-file-delete-trigger`).addClass('d-none');
            $(`#${id}-thumbnail-preview`).addClass('d-none');
        }
    });
}

function changeFileEvent(id) {
    $(`#${id}`).on('change', function () {
        const filename = $(this).val().replace(/C:\\fakepath\\/i, '');
        const id = $(this).attr('id');
        $(`#${id}-uploaded-file-name`).html(strLimit(filename, 25));
        $(`#${id}-file-upload-trigger`).addClass('d-none');
        $(`#${id}-file-delete-trigger`).removeClass('d-none');
    });
}

function strLimit(string, length) {
    return string.length > length ?
        string.substring(0, length) + '...' :
        string;
};
