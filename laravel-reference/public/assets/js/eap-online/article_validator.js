$('form button[type=submit]').on('click', function (e) {
    let visibility = $('select[name="visibility"]').val();
    let type = $('input[name="type"]:checked').val();

    let categories = $('input[name^="categories[]"]:checked').map(function () {
        return Number($(this).val());
    }).get();


    if (!validateRequired($('input[name="language"]').val())) {
        showErrorPopUp(language_trans);
        e.preventDefault()
        return;
    }

    if (!validateRequired($('textarea[name="headline"]').val())) {
        showErrorPopUp(headline_trans);
        e.preventDefault()
        return;
    }

    if (!validateRequired($('textarea[name="lead"]').val())) {
        showErrorPopUp(lead_trans);
        e.preventDefault()
        return;
    }

    if (!validateRequired($('textarea[name="sections[0][content]"]').val())) {
        showErrorPopUp(body_trans);
        e.preventDefault()
        return;
    }


    if (!validateRequired($('input[name="thumbnail-preview"]').val())) {
        showErrorPopUp(picture_trans);
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

    if (visibility == 'theme_of_the_month' || visibility == 'home_page') {
        if (!validateRequired(type)) {
            showErrorPopUp(appearance_trans);
            e.preventDefault()
            return;
        }
    }

    if (type == 'rovat') {
        let prefix = $('input[name="prefix"]:checked').val();
        if (!validateRequired(prefix)) {
            showErrorPopUp('Prefix');
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
