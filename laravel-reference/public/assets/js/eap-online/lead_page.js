$(function () {
    changeFileEvent('lead_image');
    changeFileEvent('theme_of_the_month_image');

    readImageUrl($('input[name="lead_image"]'));

    $('input[name="lead_image"]').on('change', function () {
        readImageUrl(this, 'lead_image');
    });

    readImageUrl($('input[name="theme_of_the_month_image"]'));

    $('input[name="theme_of_the_month_image"]').on('change', function () {
        readImageUrl(this, 'theme_of_the_month_image');
    });
});

function triggerFileUpload(name) {
    $(`input[name="${name}"]`).trigger('click');
}

function changeFileEvent(name) {
    $(`input[name="${name}"]`).on('change', function () {
        var filename = $(this).val().replace(/C:\\fakepath\\/i, '');
        $(`#${name}-uploaded-file-name`).html(filename);
        $(`#${name}-file-upload-trigger`).addClass('d-none');
        $(`#${name}-file-delete-trigger`).removeClass('d-none');
    })
}

function deleteUploadedFile(name) {
    $(`input[name="${name}"]`).val('');
    $(`#${name}-uploaded-file-name`).text(upload_alt);
    $(`#${name}-file-upload-trigger`).removeClass('d-none');
    $(`#${name}-file-delete-trigger`).addClass('d-none');
    $(`#${name}-preview`).addClass('d-none').attr('src', '');
}

function readImageUrl(input, name) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();

        reader.onload = function (e) {
            $(`#${name}-preview`).removeClass('d-none').attr('src', e.target.result);
        }

        reader.readAsDataURL(input.files[0]);
    }
}