$(function () {
    $('.datepicker').datepicker({
        'format': 'yyyy-mm-dd'
    });
});

function deleteGame(id) {
    Swal.fire({
        title: 'Biztos, hogy törölni szeretné?',
        text: "A művelet nem visszavonható!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Igen, törlöm!',
        cancelButtonText: 'Mégsem',
    }).then(function (result) {
        if (result.value) {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: "/ajax/delete-prizegame",
                data: {
                    game_id: id
                },
                success: function (data) {
                    location.reload();
                },
                error: function (error) {
                    location.reload();
                }
            });
        }
    });
}

function setViewable(game_id, viewable) {
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'POST',
        url: "/ajax/set-prizegame-viewable",
        data: {
            game_id,
            viewable
        },
        success: function (data) {
            location.reload();
        },
        error: function (error) {
            location.reload();
        }
    });
}

function setDate(game_id) {
    $(`#modal-set-date`).modal("show");
    $('input[name="game_id"]').val(game_id);
    $('input[name="to"]').val($(`#${game_id}_to`).attr('date'));
    $('input[name="from"]').val($(`#${game_id}_from`).attr('date'));
}
