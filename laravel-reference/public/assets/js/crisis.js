const page = 10;

function yearOpen(id) {
    $("div#" + id).toggleClass("active");
    $("div#" + id).prev('.case-list-in').toggleClass("active");
    $("#y" + id).toggleClass("rotated-icon");
}

function monthOpen(monthId) {
    const element = $("div#" + monthId).prev('.case-list-in');
    const holder = $("div#" + monthId);
    const icon = $("#m" + monthId);

    if (element.hasClass('active')) {
        element.removeClass('active');
        icon.removeClass('rotated-icon');
        $('.crisis-list-holder').find('button.load-more-cases#m_' + monthId).addClass('d-none');
        holder.addClass('d-none')
    } else {
        element.addClass('active');
        icon.addClass('rotated-icon');
        holder.removeClass('d-none')
        $('.crisis-list-holder').find('button.load-more-cases#m_' + monthId).removeClass('d-none');
        if (holder.find('.crisis-admin-component').length == 0) {
            $('.crisis-list-holder').find('img.spinner#m_' + monthId).removeClass('d-none');
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                TYPE: 'GET',
                url: '/ajax/get-crisis-interventions?date=' + monthId + '&page=1',
                success: function (data) {
                    if (data.html.length < page) {
                        $('.crisis-list-holder').find('button.load-more-cases#m_' + monthId).remove();
                    }

                    $('.crisis-list-holder').find('img.spinner#m_' + monthId).addClass('d-none');
                    holder.append(data.html);
                }
            })
        }
    }
}

function loadMore(monthId, element, all) {
    const holder = $('.crisis-list#' + monthId);
    const elements = holder.find('.crisis-admin-component');
    const p = (Math.ceil(elements.length / page)) + 1;

    $('.crisis-list-holder').find('img.spinner#m_' + monthId).removeClass('d-none');

    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'GET',
        url: '/ajax/get-crisis-interventions?date=' + monthId + '&page=' + p + '&all=' + all,
        success: function (data) {
            if (data.html.length < page) {
                $(element).remove();
                $('.crisis-list-holder').find('button.load-all-cases#m_' + monthId).addClass('d-none');
            }

            if (all) {
                $('.crisis-list-holder').find('button.load-more-cases#m_' + monthId).addClass('d-none');
            }

            $('.crisis-list-holder').find('img.spinner#m_' + monthId).addClass('d-none');
            holder.append(data.html);
        }
    })
}