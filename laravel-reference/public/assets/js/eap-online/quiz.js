$(function () {
    changeFileEvent();
    readImageUrl($('input[name="thumbnail"]'));

    $('input[name="thumbnail"]').on('change', function () {
        readImageUrl(this);
    });
});


function triggerFileUpload() {
    $('input[name="thumbnail"]').trigger('click');
}

function changeFileEvent() {
    $('input[name="thumbnail"]').on('change', function () {
        var filename = $(this).val().replace(/C:\\fakepath\\/i, '');
        $('#uploaded-file-name').html(filename);
        $('#file-upload-trigger').addClass('d-none');
        $('#file-delete-trigger').removeClass('d-none');
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
            $('input[name="thumbnail"]').val('');
            $('#uploaded-file-name').html('Kép, fotó, illusztráció feltöltése (jpg, png max. 500 kbyte)');
            $('#file-upload-trigger').removeClass('d-none');
            $('#file-delete-trigger').addClass('d-none');
            $('#thumbnail-preview').addClass('d-none');
        }
    });
}

function readImageUrl(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();

        reader.onload = function (e) {
            $('#thumbnail-preview').removeClass('d-none');
            $('#thumbnail-preview').attr('src', e.target.result);
        }

        reader.readAsDataURL(input.files[0]);
    }
}
