$('form button[type=submit]').on('click', function (e) {
    let visibility = $('select[name="visibility"]').val();

    let categories = $('input[name^="categories[]"]:checked').map(function () {
        return Number($(this).val());
    }).get();


    if (!validateRequired($('input[name="language"]').val())) {
        showErrorPopUp(language_trans);
        e.preventDefault()
        return;
    }

    if (!validateRequired($('input[name="short_title"]').val())) {
        showErrorPopUp(short_title_trans);
        e.preventDefault()
        return;
    }

    if (!validateRequired($('input[name="long_title"]').val())) {
        showErrorPopUp(long_title_trans);
        e.preventDefault()
        return;
    }

    if (!validateRequired($('input[name="link"]').val())) {
        showErrorPopUp(link_trans);
        e.preventDefault()
        return;
    }


    if (visibility == 'theme_of_the_month') {
        if (!validateRequired($('input[name="start_date"]').val()) || !validateRequired($('input[name="end_date"]').val())) {
            showErrorPopUp(date_trans);
            e.preventDefault()
            return;
        }
    }


    if (!['domestic_violence_page', 'burnout_page'].includes(visibility)) {
        if ($(categories).filter(main_categories).length <= 0) {
            showErrorPopUp(category_trans);
            e.preventDefault()
            return;
        }
    }
});
