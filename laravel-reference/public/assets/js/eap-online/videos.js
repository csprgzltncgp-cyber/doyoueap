$(function () {
    changeFileEvent();
});

function triggerFileUpload() {
    $(`input[name="attachment"]`).trigger('click');
}

function changeFileEvent() {
    $(`input[name="attachment"]`).on('change', function () {
        var filename = $(this).val().replace(/C:\\fakepath\\/i, '');
        $('#uploaded-file-name').html(filename);
        $('#file-upload-trigger').addClass('d-none').prop("src", `${window.location.origin}/assets/img/eap-online/file_icon.svg`);
        $('#file-delete-trigger').removeClass('d-none').prop("src", `${window.location.origin}/assets/img/eap-online/trash.svg`);
    })
}

function deleteUploadedFile() {
    Swal.fire({
        title: 'Biztosan törölni szeretné?',
        text: "A művelet nem visszavonható!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Igen, törlöm!'
    }).then((result) => {
        if (result.value) {
            $('input[name="attachment"]').val('');
            $('#uploaded-file-name').html('Dokumentum feltöltése (pdf max. 500 kbyte)');
            $('#file-upload-trigger').removeClass('d-none');
            $('#file-delete-trigger').addClass('d-none');
        }
    });
}
