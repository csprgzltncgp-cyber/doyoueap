function openList(element, adminId) {
    if ($(element).hasClass('active')) {
        $(element).removeClass('active');
        $(element).find('svg.arrow').removeClass(`rotated-icon`);
        $('div.task-list#admin_' + adminId).addClass('d-none');

    } else {
        $(element).addClass('active');
        $(element).find('svg.arrow').addClass(`rotated-icon`);
        $('div.task-list#admin_' + adminId).removeClass('d-none');
    }
}
