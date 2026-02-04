function changeExpertCountry(id) {
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        },
        type: 'POST',
        url: '/ajax/change-expert-country',
        data: {
            id: id,
        },
        success: function (data) {
            if (data.status == 0) {
                window.location.reload();
            } else {
                Swal.fire('Az orszĂĄgvĂĄltĂĄs sikertelen!', '', 'error');
            }
        },
        error: function (error) {
            Swal.fire(
                'Az orszĂĄgvĂĄltĂĄs sikertelen!',
                'SERVER ERROR!',
                'error'
            );
        },
    });
}

function backToAdmin() {
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        },
        type: 'GET',
        url: '/ajax/login-back-as-admin',
        success: function (data) {
            if (data.status == 0) {
                window.location.replace(data.redirect);
            } else {
                Swal.fire('Az bejelentkezĂŠs sikertelen!', '', 'error');
            }
        },
        error: function (error) {
            Swal.fire(
                'Az bejelentkezĂŠs sikertelen!',
                'SERVER ERROR!',
                'error'
            );
        },
    });
}

function loginAsAdmin(id, type) {
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        },
        type: 'POST',
        url: '/ajax/login-as',
        data: {
            id: id,
            type: type,
        },
        success: function (data) {
            if (data.status == 0) {
                window.location.href = '/' + type + '/dashboard';
            } else {
                Swal.fire('Az bejelentkezĂŠs sikertelen!', '', 'error');
            }
        },
        error: function (error) {
            Swal.fire(
                'Az bejelentkezĂŠs sikertelen!',
                'SERVER ERROR!',
                'error'
            );
        },
    });
}

function loginAsOperator(id, element) {
    $(element).attr('disabled', 'disabled');
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        },
        type: 'POST',
        url: '/ajax/login-as-operator',
        data: {
            id: id,
            type: 'expert',
        },
        success: function (data) {
            if (data.status == 0) {
                window.location.replace(data.redirect);
            } else {
                Swal.fire('Az bejelentkezĂŠs sikertelen!', '', 'error');
            }
        },
        error: function (error) {
            Swal.fire(
                'Az bejelentkezĂŠs sikertelen!',
                'SERVER ERROR!',
                'error'
            );
        },
    });
}

function loginAsClient(id, element) {
    $(element).attr('disabled', 'disabled');
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        },
        type: 'POST',
        url: '/ajax/login-as-client',
        data: {
            id: id,
            type: 'client',
        },
        success: function (data) {
            if (data.status == 0) {
                window.location.replace(data.redirect);
            } else {
                Swal.fire('Az bejelentkezĂŠs sikertelen!', '', 'error');
            }
        },
        error: function (error) {
            Swal.fire(
                'Az bejelentkezĂŠs sikertelen!',
                'SERVER ERROR!',
                'error'
            );
        },
    });
}

let menuVisible = false;
function showHideMenu() {
    if (menuVisible === false) {
        $('#menu-button').hide();
        $('#menu-list-holder').show();
        $('#menu-list-holder').addClass('menu-shadow');
        menuVisible = true;
    } else {
        $('#menu-button').show();
        $('#menu-list-holder').hide();
        $('#menu-list-holder').removeClass('menu-shadow');
        menuVisible = false;
    }
}
