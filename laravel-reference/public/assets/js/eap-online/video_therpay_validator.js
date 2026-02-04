$('form#new-appointment button[type=submit]').on('click', function (e) {
    if (!validateRequired($('select[name="day"]').val())) {
        showErrorPopUp(date_trans);
        e.preventDefault()
        return;
    }

    if (!validateRequired($('input[name="from_time"]').val())) {
        showErrorPopUp(date_trans);
        e.preventDefault()
        return;
    }

    if (!validateRequired($('input[name="to_time"]').val())) {
        showErrorPopUp(date_trans);
        e.preventDefault()
        return;
    }

    if (!validateRequired($('input[name="expert"]').val())) {
        showErrorPopUp(expert_trans);
        e.preventDefault()
        return;
    }
});

$('form#appointment-edit button[type=submit]').on('click', function (e) {
    if (!validateRequired($('select[name="edit_modal_day"]').val())) {
        showErrorPopUp(date_trans);
        e.preventDefault()
        return;
    }

    if (!validateRequired($('input[name="edit_modal_from_time"]').val())) {
        showErrorPopUp(date_trans);
        e.preventDefault()
        return;
    }

    if (!validateRequired($('input[name="edit_modal_to_time"]').val())) {
        showErrorPopUp(date_trans);
        e.preventDefault()
        return;
    }
});