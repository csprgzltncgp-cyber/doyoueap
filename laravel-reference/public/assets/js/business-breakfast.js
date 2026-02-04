function yearOpen(id) {
    $('div#' + id).toggleClass('active');
    $('div#' + id)
        .prev('.case-list-in')
        .toggleClass('active');
    $('#y' + id).toggleClass('rotated-icon');
}

function eventOpen(id) {
    $('div#event-detail-' + id).toggleClass('d-none');
    $('div#event-detail-' + id)
        .prev('.case-list-in')
        .toggleClass('active');
    $('#e' + id).toggleClass('rotated-icon');
}

function monthOpen(monthId) {
    const element = $('div#' + monthId).prev('.case-list-in');
    const holder = $('div#' + monthId);
    const icon = $('#m' + monthId);

    if (element.hasClass('active')) {
        element.removeClass('active');
        icon.removeClass('rotated-icon');
        $('.workshop-list-holder')
            .find('button.load-more-cases#m_' + monthId)
            .addClass('d-none');
        holder.addClass('d-none');
    } else {
        element.addClass('active');
        icon.addClass('rotated-icon');
        holder.removeClass('d-none');
        $('.workshop-list-holder')
            .find('button.load-more-cases#m_' + monthId)
            .removeClass('d-none');
    }
}
