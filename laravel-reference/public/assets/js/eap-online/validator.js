function showErrorPopUp(input) {
    Swal.fire(
        `${input} ${required_trans}!`,
        '',
        'error'
    );
}

function validateRequired(input) {
    if (input == "" || input == null || input == undefined) {
        return false;
    }
    return true;
}