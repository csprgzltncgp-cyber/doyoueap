$('form button[type=submit]').on('click', function (e) {
    let visibility = $('select[name="visibility"]').val();

    if (!validateRequired($('input[name="language"]').val())) {
        showErrorPopUp(language_trans);
        e.preventDefault()
        return;
    }

    if (!validateRequired($('input[name="title"]').val())) {
        showErrorPopUp(title_trans);
        e.preventDefault()
        return;
    }

    if (!validateRequired($('input[name="thumbnail"]').val())) {
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
});