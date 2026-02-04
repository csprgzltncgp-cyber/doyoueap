$(function () {
    save_on_click_outside();
    input_change();
});

function save_on_click_outside() {
    $('.container').on('focusout', 'input[type="number"]', function () {
        const value = parseInt($(this).val());
        const riport_value_id = parseInt($(this).attr('name').split('-').pop());

        edit_riport_value(value, riport_value_id);
    });
}

function input_change() {
    $('.container').on('input', 'input[type="number"]', function () {
        if ($(this).closest('div').find('svg').length !== 0) {
            $(this).closest('div').find('svg').remove();
        }

        if ($(this).closest('div').find('button').length !== 0) {
            $(this).closest('div').find('button').remove();
        }

        if ($(this).next().length !== 0) {
            $(this).next().after(`<button style="border:0; padding-left:5px;background:transparent;" onclick="edit_riport_value(${parseInt($(this).val())}, ${parseInt($(this).attr('name').split('-').pop())})"><svg style="width: 20px; height: 20px" id="Réteg_1" data-name="Réteg 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24.01 24.05"><defs><style>.cls-1,.cls-2{fill:#1d1d1b;stroke:#1d1d1b;stroke-miterlimit:10;}.cls-1{stroke-width:0.96px;}</style></defs><path class="cls-1" d="M20.83,23.54H3.14A2.68,2.68,0,0,1,.46,20.86V3.17A2.68,2.68,0,0,1,3.14.49H17.58l5.93,5.93V20.86A2.68,2.68,0,0,1,20.83,23.54ZM1.35,3.14V20.89a1.76,1.76,0,0,0,1.76,1.76H20.86a1.76,1.76,0,0,0,1.76-1.76V6.79L17.21,1.38H3.11A1.76,1.76,0,0,0,1.35,3.14Z" transform="translate(0.02 0.01)"/><path class="cls-2" d="M15.54,6.1H8.46A.47.47,0,0,1,8,5.63V1A.47.47,0,0,1,8.46.49h7.08A.47.47,0,0,1,16,1V5.63A.47.47,0,0,1,15.54,6.1ZM8.72,5.45h6.56V1.13H8.72Z" transform="translate(0.02 0.01)"/><path class="cls-2" d="M19.31,23.54H4.66a.47.47,0,0,1-.47-.46V13.5A.47.47,0,0,1,4.66,13H19.31a.47.47,0,0,1,.47.47v9.58A.47.47,0,0,1,19.31,23.54ZM5.12,22.61H18.85V14H5.12Z" transform="translate(0.02 0.01)"/><path class="cls-2" d="M16,16.7c0-.26-.14-.47-.32-.47H8.29c-.18,0-.32.21-.32.47s.14.46.32.46h7.42C15.89,17.16,16,17,16,16.7Z" transform="translate(0.02 0.01)"/><path class="cls-2" d="M16,19.88c0-.26-.14-.47-.32-.47H8.29c-.18,0-.32.21-.32.47s.14.46.32.46h7.42C15.89,20.34,16,20.13,16,19.88Z" transform="translate(0.02 0.01)"/></svg></button>`);
        } else {
            $(this).after(`<button style="border:0; padding-left:5px;background:transparent;" onclick="edit_riport_value(${parseInt($(this).val())}, ${parseInt($(this).attr('name').split('-').pop())})"><svg style="width: 20px; height: 20px" id="Réteg_1" data-name="Réteg 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24.01 24.05"><defs><style>.cls-1,.cls-2{fill:#1d1d1b;stroke:#1d1d1b;stroke-miterlimit:10;}.cls-1{stroke-width:0.96px;}</style></defs><path class="cls-1" d="M20.83,23.54H3.14A2.68,2.68,0,0,1,.46,20.86V3.17A2.68,2.68,0,0,1,3.14.49H17.58l5.93,5.93V20.86A2.68,2.68,0,0,1,20.83,23.54ZM1.35,3.14V20.89a1.76,1.76,0,0,0,1.76,1.76H20.86a1.76,1.76,0,0,0,1.76-1.76V6.79L17.21,1.38H3.11A1.76,1.76,0,0,0,1.35,3.14Z" transform="translate(0.02 0.01)"/><path class="cls-2" d="M15.54,6.1H8.46A.47.47,0,0,1,8,5.63V1A.47.47,0,0,1,8.46.49h7.08A.47.47,0,0,1,16,1V5.63A.47.47,0,0,1,15.54,6.1ZM8.72,5.45h6.56V1.13H8.72Z" transform="translate(0.02 0.01)"/><path class="cls-2" d="M19.31,23.54H4.66a.47.47,0,0,1-.47-.46V13.5A.47.47,0,0,1,4.66,13H19.31a.47.47,0,0,1,.47.47v9.58A.47.47,0,0,1,19.31,23.54ZM5.12,22.61H18.85V14H5.12Z" transform="translate(0.02 0.01)"/><path class="cls-2" d="M16,16.7c0-.26-.14-.47-.32-.47H8.29c-.18,0-.32.21-.32.47s.14.46.32.46h7.42C15.89,17.16,16,17,16,16.7Z" transform="translate(0.02 0.01)"/><path class="cls-2" d="M16,19.88c0-.26-.14-.47-.32-.47H8.29c-.18,0-.32.21-.32.47s.14.46.32.46h7.42C15.89,20.34,16,20.13,16,19.88Z" transform="translate(0.02 0.01)"/></svg></button>`);
        }
    });
}

function edit_riport_value(value, riport_value_id) {
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'POST',
        url: '/ajax/eap-online/edit-riport-value-count',
        data: {
            id: riport_value_id,
            value: value,
        },
        success: function (data) {
            location.reload();
        },
    });
}
