$(function () {

    // Prevent NULL input and replace text.
    $(document).on('change', 'input[type="number"]', function (event) {
        this.value = this.value.replace(/[^0-9]+/g, '');
    });

    // Block non-numeric chars.
    $(document).on('keypress', 'input[type="number"]', function (event) {
        return (((event.which > 47) && (event.which < 58)) || (event.which == 13));
    });
});