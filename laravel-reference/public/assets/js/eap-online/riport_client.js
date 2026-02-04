(function () {
    $('#eap-riports').on('click', function () {
        if ($('#eap-riports-group').hasClass('d-none')) {
            $('#eap-riports-group').removeClass('d-none').addClass('d-flex').addClass('flex-column');
        } else {
            $('#eap-riports-group').addClass('d-none').removeClass('d-flex').removeClass('flex-column');
        }

        $('#eap-online-riports-group').removeClass('d-flex').removeClass('flex-column').addClass('d-none');
    });

    $('#eap-online-riports').on('click', function () {
        if ($('#eap-online-riports-group').hasClass('d-none')) {
            $('#eap-online-riports-group').removeClass('d-none').addClass('d-flex').addClass('flex-column');
        } else {
            $('#eap-online-riports-group').addClass('d-none').removeClass('d-flex').removeClass('flex-column');
        }
        
        $('#eap-riports-group').removeClass('d-flex').removeClass('flex-column').addClass('d-none');
    });
})();