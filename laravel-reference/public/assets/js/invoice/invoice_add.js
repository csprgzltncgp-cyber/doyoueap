$(document).ready(function () {
  formSubmit();
  issueDateChange();
  invoiceSelected();
  $('.datepicker').datepicker({
    format: 'yyyy-mm-dd',
    startDate: startDate,
  });
  if (!user_is_hungarian) {
    $('input[name="account_number"]').mask(
      'AAAA AAAA AAAA AAAA AAAA AAAA AAAA AAAA',
      {
        placeholder: '____ ____ ____ ____ ____ ____ ____ ____',
      }
    );
    $('input[name="swift"]').mask('AAAAAAAAAAA', {
      placeholder: '____ ____ ____ ____ ____ __',
    });
    $('input[name="grand_total"]').maskMoney({
      thousands: ' ',
      decimal: '',
      allowZero: false,
      allowEmpty: true,
      precision: 0,
      suffix: ' ' + grand_total_currency,
    });
  } else {
    $('input[name="account_number"]').mask(
      '0000 0000 0000 0000 0000 0000 0000 0000',
      {
        placeholder: '____ ____ ____ ____ ____ ____ ____ ____',
      }
    );
  }
});

function invoiceSelected() {
  var uploadField = $('form#invoice_add input[type="file"]');

  $('form#invoice_add input[type="file"]').on('change', function () {
    //20MB = 20.000.000 bytes
    if (this.files[0].size > 20000000) {
      $('#uploaded-file-name span').html('');
      $('#uploaded-file-name').addClass('d-none');
      Swal.fire({
        icon: 'error',
        title: file_not_exceed_20mb,
      });
      this.value = '';
    }
  });
}

function formSubmit() {
  $('#invoice_add').submit(function (e) {
    $(this).find('button[type="submit"]').attr('disabled', true);

    /* SZÁMLA */
    const invoice = $(this).find('input[name="file"]').val();
    if (invoice == '') {
      e.preventDefault();
      $(this).find('button[type="submit"]').attr('disabled', false);
      Swal.fire({
        icon: 'error',
        title: error,
        text: uploading_invoice_is_mandatory,
      });
      return false;
    }
  });
}

$(function () {
  $('.datepicker').datepicker({
    format: 'yyyy-mm-dd',
  });
});
