$(function () {
    show_for();
    show_permission();
});

function show_for() {
    $('select[name="show_for"]').change(function () {
        const selected = $(this).val();
        const select_element = $('.show-options.' + selected + ' select');
        $('.show-options').addClass("d-none");
        $('.permissions').addClass("d-none");
        select_element.val('').trigger('chosen:updated');
        $('.permissions select').val('').trigger('chosen:updated');
        if (selected == '') {
            return false;
        }
        $('.show-options.' + selected).removeClass("d-none");
        select_element.chosen();
    });
}

function show_permission() {
    $('#selected_target_groups').change(function () {
        const selected = $(this).val();

        if (selected.includes('expert')) {
            $('.permissions').removeClass("d-none");
            $('.permissions select').chosen();
        } else {
            $('.permissions').addClass("d-none");
            $('.permissions select').val('').trigger('chosen:updated');
        }
    });
}
