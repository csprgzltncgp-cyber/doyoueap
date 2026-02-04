const page = 10;

function openInvoicing(userId, element) {
    Swal.fire({
        title: 'Dátum kiválasztása',
        input: 'text',
        showLoaderOnConfirm: true,
        confirmButtonText: 'Kiválaszt',
        stopKeydownPropagation: false,
        inputValue: new Date().toISOString().slice(0, 16).replace('T', ' '),
        onOpen: function () {
            $(Swal.getInput()).datetimepicker({
                format: 'Y-m-d H:i',
                minDate: new Date(),
            });
        },
    }).then((result) => {
        if (result.value) {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                        'content'
                    ),
                },
                type: 'POST',
                url: '/ajax/open-invoicing/store',
                data: {
                    user_id: userId,
                    until: result.value,
                },
                success: function (data) {
                    $(element).css('background-color', '#eb7e30');
                },
                error: function (data) {
                    Swal.fire(
                        'Hiba',
                        'Hiba történt a művelet végrehajtása közben!',
                        'error'
                    );
                },
            });
        }
    });
}

function revertInvoicePaid(invoiceId, element) {
    Swal.fire({
        title: "Biztos, hogy visszavonja a szakértő 'Kiegyenlítve' jelzését?",
        text: 'A művelet nem visszavonható!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Igen, visszavonom!',
        cancelButtonText: 'Mégsem',
    }).then((result) => {
        if (result.value) {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                        'content'
                    ),
                },
                type: 'PUT',
                url: '/ajax/revert-invoice-paid-status/' + invoiceId,
                success: function (data) {
                    if (data.status == 0) {
                        $(element).remove();
                    } else {
                        Swal.fire(
                            'Hiba',
                            'Hiba történt a művelet végrehajtása közben (S3R)!',
                            'error'
                        );
                    }
                },
            });
        }
    });
}

function loadMore(monthId, element, all) {
    const holder = $('.invoice-list#' + monthId);
    const elements = holder.find('.invoice-admin-component');
    const p = Math.ceil(elements.length / page) + 1;

    $('.invoice-list-holder')
        .find('img.spinner#m_' + monthId)
        .removeClass('d-none');

    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        },
        type: 'GET',
        url:
            '/ajax/get-invoices?date=' + monthId + '&page=' + p + '&all=' + all,
        success: function (data) {
            if (data.html.length < page) {
                $(element).remove();
                $('.invoice-list-holder')
                    .find('button.load-all-cases#m_' + monthId)
                    .addClass('d-none');
            }

            if (all) {
                $('.invoice-list-holder')
                    .find('button.load-more-cases#m_' + monthId)
                    .addClass('d-none');
            }

            $('.invoice-list-holder')
                .find('img.spinner#m_' + monthId)
                .addClass('d-none');
            holder.append(data.html);
        },
    });
}

function monthOpen(monthId) {
    const element = $('div#' + monthId).prev('.case-list-in');
    const holder = $('div#' + monthId);
    const icon = $('#m' + monthId);

    if (element.hasClass('active')) {
        element.removeClass('active');
        icon.removeClass('rotated-icon');
        $('.invoice-list-holder')
            .find('button.load-more-cases#m_' + monthId)
            .addClass('d-none');
        holder.addClass('d-none');
    } else {
        element.addClass('active');
        icon.addClass('rotated-icon');
        holder.removeClass('d-none');
        $('.invoice-list-holder')
            .find('button.load-more-cases#m_' + monthId)
            .removeClass('d-none');
        if (holder.find('.invoice-admin-component').length == 0) {
            $('.invoice-list-holder')
                .find('img.spinner#m_' + monthId)
                .removeClass('d-none');
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                        'content'
                    ),
                },
                TYPE: 'GET',
                url: '/ajax/get-invoices?date=' + monthId + '&page=1',
                success: function (data) {
                    if (data.html.length < page) {
                        $('.invoice-list-holder')
                            .find('button.load-more-cases#m_' + monthId)
                            .remove();
                    }

                    $('.invoice-list-holder')
                        .find('img.spinner#m_' + monthId)
                        .addClass('d-none');
                    holder.append(data.html);
                },
            });
        }
    }
}

function yearOpen(id) {
    $('div#' + id).toggleClass('active');
    $('div#' + id)
        .prev('.case-list-in')
        .toggleClass('active');
    $('#y' + id).toggleClass('rotated-icon');
}
