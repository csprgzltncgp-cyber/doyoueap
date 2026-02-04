$(document).ready(function () {
    $(window).on('beforeunload', function () {
        if ($("input[name^='new']").length > 0) {
            return "you have unsaved changes. Are you sure you want to navigate away?";
        }
    });

    $(document).on("submit", "form", function (event) {
        $(window).off('beforeunload');
    });
});

function toggleTranslationSection(section_id, element) {
    $(`#${section_id}`).toggleClass('d-none');
    $(element).children('div').children('i').toggleClass('fa-caret-left').toggleClass('fa-caret-down');

    if ($(element).hasClass('active')) {
        $(element).removeClass('active');
        $(element).find('svg').removeClass('rotated-icon');
    } else {
        $(element).addClass('active');
        $(element).find('svg').addClass('rotated-icon');
    }
}
