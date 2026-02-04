$('form button[type=submit]').on('click', function (e) {
    if (!validateRequired($('input[name="language"]').val())) {
        showErrorPopUp(language_trans);
        e.preventDefault()
        return;
    }
});