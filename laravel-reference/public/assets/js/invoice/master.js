$(function () {
  editData();
  changeFileEvent();
  addCaseForm();
  downloadInvoice();
});

function toggleSeen(invoiceId, element) {
  $.ajax({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
    },
    type: 'PUT',
    url: '/ajax/toggle-invoice-seen-status/' + invoiceId,
    success: function (data) {
      if (data.status == 0) {
        if (data.seen == 1) {
          $(element).addClass('downloaded-invoice');
        } else {
          $(element).removeClass('downloaded-invoice');
        }
      } else {
        Swal.fire(
          'Hiba',
          'Hiba történt a művelet végrehajtása közben (S3R)!',
          'error'
        );
      }
    },
  });

  return false;
}

function deleteInvoiceByAdmin(invoiceId, element) {
  Swal.fire({
    title: are_you_sure_you_want_to_delete_your_invoice,
    text: operation_isnt_reversible,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: yes_delete_it,
    cancelButtonText: cancel,
  }).then((result) => {
    if (result.value) {
      $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        },
        type: 'DELETE',
        url: '/ajax/delete-invoice-by-admin/' + invoiceId,
        success: function (data) {
          if (data.status == 0) {
            $(element).closest('.list-element').remove();
          } else {
            Swal.fire(error, deletion_was_unsuccessful + '(S3R)', 'error');
          }
        },
      });
    }
  });
}

function downloadInvoice() {
  $('a.admin-invoice-index-download').on('click', function (e) {
    if (!$(this).hasClass('downloaded-invoice')) {
      e.preventDefault();
      $(this).addClass('downloaded-invoice');
      const url = $(this).attr('href');
      window.open(url, '_blank');
    }
  });
}

function setInvoiceStatus(invoiceId, status, element) {
  $.ajax({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
    },
    type: 'PUT',
    url: '/ajax/set-invoice-status/' + invoiceId,
    data: {
      status: status,
    },
    success: function (data) {
      if (data.status == 0) {
        console.log(data.invoice.status);
        if (data.invoice.status == 'listed_in_a_bank') {
          $(element)
            .closest('.list-element')
            .find('span.data')
            .addClass('listed_in_a_bank');
          $(element)
            .closest('.list-element')
            .find('.invoice-info.payment-due')
            .remove();
          $(`#delete_button_${invoiceId}`)
            .css('display', 'block')
            .removeAttr('disabled');
          $(element)
            .addClass('downloaded-invoice')
            .attr('onClick', `setInvoiceStatus(${invoiceId},'created', this)`);
        }

        if (data.invoice.status == 'created') {
          $(element)
            .closest('.list-element')
            .find('span.data')
            .removeClass('listed_in_a_bank');
          $(`#delete_button_${invoiceId}`)
            .css('display', 'block')
            .attr('disabled', 'disabled');
          $(element)
            .removeClass('downloaded-invoice')
            .attr(
              'onClick',
              `setInvoiceStatus(${invoiceId},'listed_in_a_bank', this)`
            );
        }
      } else {
        Swal.fire(error, editing_was_unsuccessful + '(S3R)', 'error');
      }
    },
  });
}

function deleteCaseFromInvoice(
  element,
  caseId = null,
  invoice = null,
  case_type = null
) {
  Swal.fire({
    title: are_you_sure_you_want_to_delete_caseid,
    text: operation_isnt_reversible,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: yes_delete_it,
    cancelButtonText: cancel,
  }).then((result) => {
    if (result.value) {
      $(element).next('input').remove();
      $(element).remove();
    }
  });
}

function addCaseForm() {
  $('form[name="add-case"]').on('submit', function (e) {
    const form = $(this);
    $(form).find('#invoice-error').addClass('d-none');
    e.preventDefault();
    const value = form.find('input[name="case"]').val();
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
      },
      type: 'POST',
      url: '/ajax/add-case-to-invoice',
      data: {
        case: value,
        invoice: invoice_id,
      },
      success: function (data) {
        if (Boolean(data.status)) {
          $(form).find('#invoice-error').addClass('d-none');
          $('#add-case-modal').modal('hide');
          $(form).find('input').val('');

          $('#case-list').append(`
                             <button type="button" class="mr-3"  onclick="deleteCaseFromInvoice(this, ${value}, ${invoice_id})">
                                <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="height: 20px; width: 20px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                               ${value}
                            </button>

                            <input type="hidden" name="caseids[]" value="${value}"/>
                    `);

          Swal.fire(data.msg, '', 'success');
        } else {
          $(form).find('#invoice-error').removeClass('d-none').html(data.msg);
        }
      },
    });
  });
}

function triggerFileUpload() {
  $('input[name="file"]').trigger('click');
}

function changeFileEvent() {
  $('input[name="file"]').on('change', function () {
    var filename = $(this)
      .val()
      .replace(/C:\\fakepath\\/i, '');
    $('#uploaded-file-name span').html(filename);
    $('#uploaded-file-name').removeClass('d-none');
  });
}

function deleteUploadedFile(element) {
  $(element).closest('#uploaded-file-name').addClass('d-none');
  $('input[name="file"]').val('');
}

function editData() {
  $('.form-group-inner button.edit').on('click', function () {
    const input = $(this).prev('input');
    if ($(this).hasClass('focused')) {
      $(this).removeClass('focused');
      input.blur();
    } else {
      $(this).addClass('focused');
      input.focus();
    }

    let hidden = $(this).find('svg:not(.active)');
    $(this).find('svg.active').removeClass('active');
    hidden.addClass('active');
  });
}

//számla esemény aktiválás/létrehozás
function invoiceEventCreateExpert(
  event,
  invoiceId,
  element,
  afterPaymentDeadline = 1,
  payment_deadline = null
) {
  if (event == 'invoice_expired_and_not_paid' && !afterPaymentDeadline) {
    Swal.fire(
      system_message,
      invoice_due_date_if_you_1 +
        ': ' +
        payment_deadline +
        '. <br/>' +
        invoice_due_date_if_you_2 +
        '!',
      'error'
    );
    return false;
  }

  let text;
  if (event == 'invoice_paid') {
    text = are_you_sure_your_invoice_is_settled;
  } else if (event == 'invoice_expired_and_not_paid') {
    text = are_you_sure_your_invoice_is_unsettled;
  }

  Swal.fire({
    title: text,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: yes,
    cancelButtonText: cancel,
  }).then((result) => {
    if (result.value) {
      $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        },
        type: 'POST',
        url: '/ajax/invoice-event/' + invoiceId,
        data: {
          event: event,
        },
        success: function (data) {
          if (data.status == 0) {
            $(element).addClass('active');
            if (event == 'invoice_paid') {
              $(element)
                .closest('div.list-element')
                .find('#invoice_payment_sent')
                .remove();
              $(element)
                .closest('div')
                .find('#invoice_expired_and_not_paid')
                .removeClass('active');
            } else if (event == 'invoice_expired_and_not_paid') {
              $(element)
                .closest('div.list-element')
                .find('#invoice_payment_sent')
                .remove();
              $(element)
                .closest('div')
                .find('#invoice_paid')
                .removeClass('active');
            } else if (event == 'invoice_payment_sent') {
              $(element)
                .closest('div.list-element')
                .find('span.data')
                .addClass('listed_in_a_bank');
              $(element)
                .closest('div.list-element')
                .find('.invoice-info.payment-due')
                .remove();
              $(element)
                .closest('div.list-element')
                .find('#listed-in-a-bank')
                .remove();
              $(element).remove();
            }
          }
        },
      });
    }
  });
}

//számla esemény aktiválás/létrehozás
function invoiceEventCreate(
  event,
  invoiceId,
  element,
  afterPaymentDeadline = 1,
  payment_deadline = null
) {
  $.ajax({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
    },
    type: 'POST',
    url: '/ajax/invoice-event/' + invoiceId,
    data: {
      event: event,
    },
    success: function (data) {
      if (data.status == 0) {
        $(element).addClass('active');
        if (event == 'invoice_paid') {
          $(element)
            .closest('div.list-element')
            .find('#invoice_payment_sent')
            .remove();
          $(element)
            .closest('div')
            .find('#invoice_expired_and_not_paid')
            .removeClass('active');
        } else if (event == 'invoice_expired_and_not_paid') {
          $(element)
            .closest('div.list-element')
            .find('#invoice_payment_sent')
            .remove();
          $(element).closest('div').find('#invoice_paid').removeClass('active');
        } else if (event == 'invoice_payment_sent') {
          $(element)
            .closest('div.list-element')
            .find('span.data')
            .addClass('listed_in_a_bank');
          $(element)
            .closest('div.list-element')
            .find('.invoice-info.payment-due')
            .remove();
          $(element)
            .closest('div.list-element')
            .find('#listed-in-a-bank')
            .remove();
          $(element).remove();
        }
      }
    },
  });
}

//számla esemény deaktiválás/törlés
function invoiceEventDelete(event, invoiceId, element) {
  $.ajax({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
    },
    type: 'DELETE',
    url: '/ajax/invoice-event/' + invoiceId,
    data: {
      event: event,
    },
    success: function (data) {
      if (data.status == 0) {
      }
    },
  });
}

function deleteInvoice(id, element) {
  Swal.fire({
    title: are_you_sure_you_want_to_delete_this,
    text: operation_isnt_reversible + '!',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: yes_delete_it,
    cancelButtonText: cancel,
  }).then((result) => {
    if (result.value) {
      $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        },
        type: 'DELETE',
        url: '/ajax/delete-invoice-by-expert/' + id,
        success: function (data) {
          if (data.status == 0) {
            $(element).closest('.list-element').remove();
            location.reload();
          } else {
            Swal.fire(deletion_was_unsuccessful, '', 'error');
          }
        },
      });
    }
  });
}
