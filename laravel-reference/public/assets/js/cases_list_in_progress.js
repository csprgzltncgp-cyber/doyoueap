const page = 10;

(function () {
    country_ids.map(function (country_id, index) {
        setTimeout(function () {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'GET',
                url: '/ajax/need_exclamation/' + country_id,
                success: function (data) {
                    if (data) {
                        $('.holder-for-country-' + country_id).html(`
                       <svg xmlns="http://www.w3.org/2000/svg" style="height: 20px; width: 20px; margin-bottom: 1px;   color: rgb(219,11,32);"
                         class="mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    `);
                    } else {
                        $('.holder-for-country-' + country_id).html('');
                    }
                }
            });
        }, index * 50);
    });
})();

function toggleCases(countryId, element) {
    if ($(element).hasClass('active')) {
        $(element).removeClass('active');
        $('.no-case-text').remove();
        $('.case-list-holder').find('button.load-more-cases').addClass('d-none');
        $(element).find('svg.arrow').removeClass('rotated-icon');
        $('.case-list-holder').find('button.load-more-cases#country_' + countryId).addClass('d-none');
        $('.case-list-in-progress').each(function () {
            if ($(this).data('country') && $(this).data('country') == countryId) {
                $(this).addClass('d-none');
            }
        });
    } else {
        $('.case-list-in-progress-country.active').removeClass('active');
        $(element).addClass('active');
        $(element).next('.cases-list').removeClass('d-none');
        $('.no-case-text').remove();
        $('.case-list-holder').find('button.load-more-cases').addClass('d-none');
        $(element).find('svg.arrow').addClass('rotated-icon');
        $(element).find('i').not('.fa-exclamation-triangle').removeClass('fa-caret-left').addClass('fa-caret-down');
        $('.case-list-holder').find('button.load-more-cases#country_' + countryId).removeClass('d-none');
        $('.case-list-in-progress').each(function () {
            if ($(this).data('country') && $(this).data('country') == countryId) {
                $(this).removeClass('d-none');
            } else if (!$(this).hasClass('group')) {
                $(this).addClass('d-none');
            }
        });

        if ($(element).next('.cases-list').find('.case-list-in-progress').length == 0) {
            $('.case-list-holder').find('img.spinner#country_' + countryId).removeClass('d-none');
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'GET',
                url: '/ajax/get-cases/' + countryId + '?page=1',
                success: function (data) {
                    if (data.html.length == 0) {
                        $(element).next('.cases-list').append('<p class="text-center no-case-text">' + no_cases_text + '</p>');
                    }
                    if (data.html.length < page) {
                        $('.case-list-holder').find('button.load-more-cases#country_' + countryId).remove();
                    }
                    $('.case-list-holder').find('img.spinner#country_' + countryId).addClass('d-none');
                    $(element).next('.cases-list').append(data.html);
                }
            });
        }
    }
}

function loadMore(countryId, element, all) {
    const elements = $('.cases-list#country_' + countryId).find('.case-list-in-progress');
    const p = (Math.ceil(elements.length / page)) + 1;
    $('.case-list-holder').find('img.spinner#country_' + countryId).removeClass('d-none');
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'GET',
        url: '/ajax/get-cases/' + countryId + '?page=' + p + '&all=' + all,
        success: function (data) {
            if (data.html.length < page) {
                $(element).remove();
                $('.case-list-holder').find('button.load-all-cases#country_' + countryId).addClass('d-none');
            }

            if (all) {
                $('.case-list-holder').find('button.load-more-cases#country_' + countryId).addClass('d-none');
            }

            $('.case-list-holder').find('img.spinner#country_' + countryId).addClass('d-none');
            $('.cases-list#country_' + countryId).find('i.fa-sync-alt').remove();
            $('.cases-list#country_' + countryId).append(data.html);
        }
    });
}


function deleteCase(id, element) {
    Swal.fire({
        title: 'Biztos, hogy törölni szeretné?',
        text: "A művelet nem visszavonható!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Igen, törlöm!',
        cancelButtonText: 'Mégsem',

    }).then(function (result) {
        if (result.value) {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'DELETE',
                url: '/ajax/delete-case/' + id,
                success: function (data) {
                    if (data.status == 0) {
                        $(element).closest('.case-list-in-progress').remove();
                    }
                }
            });
        }
    });

}
