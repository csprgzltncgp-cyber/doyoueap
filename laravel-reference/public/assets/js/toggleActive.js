function toggleActive(id, element) {
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        },
        type: 'POST',
        url: '/ajax/toggle-user-active',
        data: {
            id: id,
        },
        success: function (data) {
            if (data.status == 0) {
                if (data.active) {
                    $(element).removeClass('deactivated');
                    $(element)
                        .html(` <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                                         style="height:20px; margin-bottom: 3px" fill="none" viewBox="0 0 24 24"
                                         stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg> Aktív`);
                } else {
                    $(element).addClass('deactivated');
                    $(element)
                        .html(` <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                                         style="height:20px; margin-bottom: 3px" fill="none" viewBox="0 0 24 24"
                                         stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg> Inaktív`);
                }
            }
        },
    });
}

function toggleLocked(id, element) {
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        },
        type: 'POST',
        url: '/ajax/toggle-user-locked',
        data: {
            id: id,
        },
        success: function (data) {
            if (data.status == 0) {
                if (!data.locked) {
                    $(element).removeClass('deactivated');
                    $(element)
                        .html(`<svg xmlns="http://www.w3.org/2000/svg" style="height:20px; margin-bottom: 3px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z" />
                               </svg> Feloldva`);
                } else {
                    $(element).addClass('deactivated');
                    $(element)
                        .html(`<svg xmlns="http://www.w3.org/2000/svg" style="height:20px; margin-bottom: 3px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg> Zárolva`);
                }
            }
        },
    });
}

function toggleList(countryId, element, event, baseSelector = 'list-element') {
    if (
        !$(event.target).is('a.mail') &&
        !$(event.target).is('button.batchmail')
    ) {
        if ($(element).hasClass('active')) {
            $(element).removeClass('active');
            $('.' + baseSelector + ' .caret-left').removeClass('rotated-icon');
            $('.' + baseSelector).each(function () {
                if (
                    $(this).data('country') &&
                    $(this).data('country') == countryId
                ) {
                    $(this).addClass('d-none');
                }
            });
        } else {
            $('.case-list-in').filter(':not(.date-list)').each(function () {
                $(this).removeClass('active');
            });

            $('.caret-left').filter(':not(.date-icon)').each(function () {
                $(this).removeClass('rotated-icon');
            });

            $(element).addClass('active');
            $(element).find('button.caret-left').addClass('rotated-icon');
            $('.' + baseSelector).each(function () {
                if (
                    $(this).data('country') &&
                    $(this).data('country') == countryId
                ) {
                    $(this).removeClass('d-none');
                } else if (!$(this).hasClass('group')) {
                    $(this).addClass('d-none');
                }
            });
        }
    }
}
