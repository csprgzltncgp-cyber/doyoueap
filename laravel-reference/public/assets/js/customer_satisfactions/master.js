$(function () {
    selectAllSatisfactions();
    selectSatisfaction();
    satisfactionClick();
});
let selectedCompanies = [];
let allSatisfactionsSelected = false;
let selectSatisfactions = false;

function satisfactionClick() {
    $('.company-riports-holder .company-riport').click(function (e) {
        if (!allSatisfactionsSelected && !selectSatisfactions) {
            if ($(e.target).is('a')) {
                return true;
            }
            return false;
        }
        $(this).toggleClass('selected');
        const companyId = $(this).data('id');
        //ha ki van jelölve
        if ($(this).hasClass('selected')) {
            //ha nincs benne, akkor belerakjuk
            if (selectedCompanies.indexOf(companyId) == -1) {
                selectedCompanies.push(companyId);
            }
        }
        //ha nincs kijelölve
        else {
            if (selectedCompanies.indexOf(companyId) != -1) {
                selectedCompanies = selectedCompanies.filter(id => id != companyId);
            }
        }
        if (selectedCompanies.length > 0) {
            $('.riports-actions button#activate-riports').removeAttr('disabled');
            $('.riports-actions button#deactivate-riports').removeAttr('disabled');
        } else {
            $('.riports-actions button#activate-riports').attr('disabled', 'disabled');
            $('.riports-actions button#deactivate-riports').attr('disabled', 'disabled');
        }
    });
}

function selectSatisfaction() {
    $('#select-riports').click(function () {
        selectSatisfactions = !selectSatisfactions;
        if (selectSatisfactions) {
            $(this).addClass('active');
        } else {
            $(this).removeClass('active');
        }
    });
}

function selectAllSatisfactions() {
    $('#select-all-riports').click(function () {
        allSatisfactionsSelected = !allSatisfactionsSelected;
        if (allSatisfactionsSelected) {
            $('.company-riports-holder .company-riport').addClass('selected');
            $('.riports-actions button#activate-riports').removeAttr('disabled');
            $('.riports-actions button#deactivate-riports').removeAttr('disabled');
            $('.company-riports-holder .company-riport').each(function () {
                const companyId = $(this).data('id');
                selectedCompanies.push(companyId);
            });

        } else {
            $('.company-riports-holder .company-riport').removeClass('selected');
            $('.riports-actions button#activate-riports').attr('disabled', 'disabled');
            $('.riports-actions button#deactivate-riports').attr('disabled', 'disabled');
            selectedCompanies = [];
        }
    })
}

function deactivateSelectedSatisfactions() {
    if (!selectedCompanies.length) {
        return false;
    }

    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'POST',
        url: '/ajax/deactivate-satisfactions',
        data: {
            companyIds: selectedCompanies,
            from: from_date,
            to: to_date,
        },
        success: function (data) {
            if (data.status == 0) {
                $('.company-riport.selected').removeClass('selected');
                $('#activate-riports').attr('disabled', 'disabled');
                $('#deactivate-riports').attr('disabled', 'disabled');
                $('#select-riports').removeClass('active');
                selectedCompanies.map((value) => {
                    $('#company_' + value + ' .company-riport-head h2 svg').remove();
                    $('#company_' + value + ' .actions button.active').removeClass('active');
                    $('#company_' + value + ' .actions button.deactivate').addClass('active');
                });
                selectedCompanies = [];
                Swal.fire(
                    'Az deaktiválás sikeres!',
                    '',
                    'success'
                );
            } else {
                Swal.fire(
                    'Az módosítás sikertelen!',
                    '',
                    'error'
                );
            }
        },
        error: function (error) {
            Swal.fire(
                'Az módosítás sikertelen!',
                'SERVER ERROR!',
                'error'
            );
        }
    });
}

function activateSelectedSatisfactions() {
    if (!selectedCompanies.length) {
        return false;
    }

    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'POST',
        url: '/ajax/activate-satisfactions',
        data: {
            companyIds: selectedCompanies,
            from: from_date,
            to: to_date,
        },
        success: function (data) {
            if (data.status == 0) {
                $('.company-riport.selected').removeClass('selected');
                $('#activate-riports').attr('disabled', 'disabled');
                $('#deactivate-riports').attr('disabled', 'disabled');
                $('#select-riports').removeClass('active');
                selectedCompanies.map((value) => {
                    if ($('#company_' + value + ' .company-riport-head h2 i').length == 0) {
                        $('#company_' + value + ' .company-riport-head h2').prepend(' <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">\n' +
                            '                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />\n' +
                            '                                </svg>');
                    }
                    $('#company_' + value + ' .actions button.active').removeClass('active');
                    $('#company_' + value + ' .actions button.activate').addClass('active');
                });
                selectedCompanies = [];
                Swal.fire(
                    'Az aktiválás sikeres!',
                    '',
                    'success'
                );
            } else {
                Swal.fire(
                    'Az módosítás sikertelen!',
                    '',
                    'error'
                );
            }
        },
        error: function (error) {
            Swal.fire(
                'Az módosítás sikertelen!',
                'SERVER ERROR!',
                'error'
            );
        }
    });
}
