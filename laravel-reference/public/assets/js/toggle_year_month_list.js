var select = 0;

function selectClick() {
    select = !select;
    if (select) {
        $('#selectButton').addClass('active');
    } else {
        $('form[name="excel_export"] input[name!="_token"]').remove();
        $('#selectButton').removeClass('active');
        $('.case-list.selected').removeClass('selected');
    }
}

$(function () {
    clickOnCase();
});

function clickOnCase() {
    $('.case-list-holder').on('click', '.case-list', function () {
        if (!select) {
            window.location.href = $(this).data('href');
        }

        $(this).toggleClass('selected');
        const id = $(this).data('id');
        if ($(this).hasClass('selected')) {
            const input = '<input type="hidden" name="cases[]" value="' + id + '">';
            $('form[name="excel_export"]').append(input);
        } else {
            $('form[name="excel_export"] input[value="' + id + '"]').remove();
        }
    });
}

function monthOpen(id) {
    $("div#" + id).toggleClass("active");
    $("div#" + id).prev('.case-list-in').toggleClass("active");
    $("#m" + id).toggleClass("rotated-icon");
}

function yearOpen(id) {
    $("div#" + id).toggleClass("active");
    $("div#" + id).prev('.case-list-in').toggleClass("active");
    $("#y" + id).toggleClass("rotated-icon");
}