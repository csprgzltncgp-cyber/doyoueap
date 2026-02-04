function saveDate() {
    $('#visibility_date').text(
        `${$('input[name="from_date"]').val()} - ${$(
            'input[name="to_date"]'
        ).val()}`
    );
    $('input[name="start_date"]').val($('input[name="from_date"]').val());
    $('input[name="end_date"]').val($('input[name="to_date"]').val());
    $('#modal-date-picker').modal('hide');
    $('#theme-of-the-month-trigger').addClass('d-none');
    $('#theme-of-the-month-delete-trigger').removeClass('d-none');
}

function saveLanguage() {
    $('#modal-language-select').modal('hide');
    $('input[name="language"]').val($('select[name="article_language"]').val());
    $('#language-select-button').text(
        $('select[name="article_language"] option:selected').text()
    );
    $('#language-select-button').removeClass('error');
}

function openModal(id) {
    $(`#${id}`).modal('show');
}

function toggleCategories(categoryType, element) {
    if ($(element).hasClass('active')) {
        $(element).removeClass('active');
        $('.list-element > div > span')
            .html(`<svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                                    </svg>`);
        $('.list-el').each(function () {
            if (
                $(this).data('category') &&
                $(this).data('category') == categoryType
            ) {
                $(this).addClass('d-none');
            }
        });
    } else {
        $(element).addClass('active');
        $(element).find('span.caret-left')
            .html(`<svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                      <path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7" />
                                                    </svg>`);
        $('.list-el').each(function () {
            if (
                $(this).data('category') &&
                $(this).data('category') == categoryType
            ) {
                $(this).removeClass('d-none');
            } else if (!$(this).hasClass('group')) {
                $(this).addClass('d-none');
            }
        });
    }
}

function deleteResource() {
    Swal.fire({
        title: 'Biztosan törölni szeretné?',
        text: 'A művelet nem visszavonható!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Igen, törlöm!',
    }).then((result) => {
        if (result.value) {
            $('#deleteForm').submit();
        }
    });
}

function removeError(type, id) {
    $(`#${id}`).removeClass(type);
}

function changeVisibilityParameters(visibility) {
    const extra_visibility = $('#apperance-more');
    const date_picker = $('#date_picker');
    const visibility_alt = $('span[id="visibility_alt"]');
    const categorize = $('#categorize');
    const lesson = $('#lesson');
    const chapter = $('#chapter');

    if (visibility == 'theme_of_the_month') {
        extra_visibility.removeClass('d-none');
        date_picker.removeClass('d-none').addClass('d-flex');
        visibility_alt.removeClass('d-none');
        categorize.removeClass('d-none').addClass('d-flex');
        lesson.addClass('d-none').removeClass('d-flex');
        chapter.addClass('d-none').removeClass('d-flex');
    } else if (visibility == 'home_page') {
        extra_visibility.removeClass('d-none');
        date_picker.addClass('d-none').removeClass('d-flex');
        visibility_alt.removeClass('d-none');
        categorize.removeClass('d-none').addClass('d-flex');
        lesson.addClass('d-none').removeClass('d-flex');
        chapter.addClass('d-none').removeClass('d-flex');
    } else if (visibility == 'burnout_page') {
        extra_visibility.addClass('d-none');
        date_picker.addClass('d-none').removeClass('d-flex');
        visibility_alt.addClass('d-none');
        categorize.addClass('d-none').removeClass('d-flex');
        lesson.removeClass('d-none').addClass('d-flex');
        chapter.addClass('d-none').removeClass('d-flex');
    } else if (visibility == 'domestic_violence_page') {
        extra_visibility.addClass('d-none');
        date_picker.addClass('d-none').removeClass('d-flex');
        visibility_alt.addClass('d-none');
        categorize.addClass('d-none').removeClass('d-flex');
        lesson.addClass('d-none').removeClass('d-flex');
        chapter.removeClass('d-none').addClass('d-flex');
    } else {
        extra_visibility.addClass('d-none');
        date_picker.addClass('d-none').removeClass('d-flex');
        visibility_alt.removeClass('d-none');
        categorize.removeClass('d-none').addClass('d-flex');
        lesson.addClass('d-none').removeClass('d-flex');
        chapter.addClass('d-none').removeClass('d-flex');
    }

    $('input[name="type"]').prop('checked', false);
    $('input[name="prefix"]').prop('checked', false);

    $('input[name="start_date"]').val('');
    $('input[name="end_date"]').val('');
    $('input[name="from_date"]').val('');
    $('input[name="to_date"]').val('');
    $('#visibility_date').html('Select Date');
    $('#theme-of-the-month-trigger').removeClass('d-none');
    $('#theme-of-the-month-delete-trigger').addClass('d-none');
}

$(function () {
    $(document).find("input:checked[type='radio']").addClass('bounce');
    $("input[type='radio']").click(function () {
        $(this).prop('checked', false);
        $(this).toggleClass('bounce');

        if ($(this).hasClass('bounce')) {
            $(this).prop('checked', true);
            $(document)
                .find("input:not(:checked)[type='radio']")
                .removeClass('bounce');
        }
    });

    $('.datepicker').datepicker({
        format: 'yyyy-mm-dd',
        startDate: '0d',
    });

    $('input[name="type"]:checked').val() == 'rovat'
        ? $('#prefix-container').removeClass('d-none').addClass('d-flex')
        : $('#prefix-container').addClass('d-none').removeClass('d-flex');

    $('input[name="type"]').on('change', function (e) {
        if (e.target.value !== 'rovat') {
            $('input[name="prefix"]').prop('checked', false);
            $('#prefix-container').addClass('d-none').removeClass('d-flex');
        } else {
            $('#prefix-container').removeClass('d-none').addClass('d-flex');
        }
    });

    let visibility_prev_val;
    $('select[name="visibility"]')
        .focus(function () {
            visibility_prev_val = $(this).val();
        })
        .change(function () {
            if (visibility_prev_val != 'null') {
                Swal.fire({
                    title: 'Biztosan változtatni szeretne?',
                    text: 'A megjelenés paraméterei elvesznek!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Igen, törlöm!',
                }).then((result) => {
                    if (result.value) {
                        changeVisibilityParameters($(this).val());
                    } else {
                        $(this).val(visibility_prev_val);
                        return false;
                    }
                });
            } else {
                changeVisibilityParameters($(this).val());
            }

            $('#prefix-container').addClass('d-none').removeClass('d-flex');
        });
});
