$('form button[type=submit]').on('click', function (e) {
    if (location.pathname.includes('create')) {
        if (!validateRequired($('input[name="language"]').val())) {
            showErrorPopUp(language_trans);
            e.preventDefault()
            return;
        }

        if (!validateRequired($('select[name="type"] option:selected').val())) {
            showErrorPopUp(type_trans);
            e.preventDefault()
            return;
        }
    }

    let hasBodyError = false
    $('textarea').each(function () {
        if (!validateRequired($(this).val()) && $('select[name="type"] option:selected').val() !== "5" && $('input[name="prizegame-type"]').val() !== "5") {
            hasBodyError = true;
        }
    })

    if (hasBodyError) {
        showErrorPopUp(body_trans);
        e.preventDefault();
        return;
    }

    if (!validateRequired($('input[name="phone-number"]').val()) && $('select[name="type"] option:selected').val() !== "5" && $('input[name="prizegame-type"]').val() !== "5") {
        showErrorPopUp(phone_trans);
        e.preventDefault();
        return;
    }

    if ($("#questions_holder > div").length < 1 && $('select[name="type"] option:selected').val() !== "5" && $('input[name="prizegame-type"]').val() !== "5") {
        showErrorPopUp(min_questions_trans);
        e.preventDefault();
        return;
    }

    $('.answer-holder').each(function () {
        if ($(this).children().length < 2) {
            showErrorPopUp(min_answers_trans);
            e.preventDefault();
            return;
        }
    });

    let checkedCount = 0;

    $('input[type="radio"]').each(function () {
        if ($(this).attr('name').includes('correct') && $(this).is(':checked')) {
            checkedCount++;
        }
    });

    if (checkedCount != $("#questions_holder > div").length) {
        showErrorPopUp(correct_trans);
        e.preventDefault();
        return;
    }

    if (localStorage.getItem("prizegame_type") !== null) {
        localStorage.removeItem("prizegame_type");
    }
});
