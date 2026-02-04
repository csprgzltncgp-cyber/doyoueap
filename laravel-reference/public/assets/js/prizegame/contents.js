let sectionIndex = 1;
let questionIndex = 1;
let answerIndex = 1;
let digitIndex = 1;

$(function () {
    changeFileEvent();

    // prizegame_type initialization
    if ($('select[name="type"]').val()) {
        localStorage.setItem('prizegame_type', $('select[name="type"]').val());
    } else {
        localStorage.setItem(
            'prizegame_type',
            $('input[name="prizegame-type"]').val()
        );
    }

    $('input[type="file"]').on('change', function () {
        readImageUrl(this);
    });

    $('#phone_number').on('change', function () {
        addDigit(this);
        $('input[name="phone-number-changed"]').val(1);
    });

    $('select[name="type"]').on('change', function () {
        let type_id = $(this).val();

        if ($(this).val() === '5') {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                        'content'
                    ),
                },
                type: 'POST',
                url: '/ajax/set-prizegame-type',
                data: {
                    id: type_id,
                },
                success: function () {
                    localStorage.setItem('prizegame_type', type_id);
                    location.reload();
                },
            });

            $('#background-image').removeClass('d-flex').addClass('d-none');
            $('#block-1-sub-headline').removeClass('d-none');
            $('#block-1-body').removeClass('d-none');
            $('#block-1-new-section').removeClass('d-flex').addClass('d-none');
            // $('#block-1-new-section button').attr("onclick","newSection(5, 1, ['body', 'sub_headline', 'list'])");
            $('#block-2-headline').addClass('d-none');
            $('#block-2-sub-headline').removeClass('d-none');
            $('#block-2-body').removeClass('d-none');
            $('#block-2-new-section button').attr(
                'onclick',
                "newSection(5, 2, ['body', 'sub_headline'])"
            );
            $('#block-3-headline').addClass('d-none');
            $('#block-3-body').addClass('d-none');
            $('#block-3-phone').addClass('d-none');
            $('#numbers_holder').addClass('d-none');
            $('#questions_holder').removeClass('d-flex').addClass('d-none');
            $('#block-3-new-section').removeClass('d-none').addClass('d-flex');
            $('#block-3-new-section button').attr(
                'onclick',
                "newSection(5, 3, ['checkbox'])"
            );
        } else {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                        'content'
                    ),
                },
                type: 'POST',
                url: '/ajax/set-prizegame-type',
                data: {
                    id: type_id,
                },
                success: function () {
                    if (localStorage.getItem('prizegame_type') === '5') {
                        localStorage.setItem('prizegame_type', type_id);
                        location.reload();
                    } else {
                        localStorage.setItem('prizegame_type', type_id);
                    }
                },
            });

            $('#background-image').removeClass('d-none').addClass('d-flex');
            $('#block-1-sub-headline').addClass('d-none');
            $('#block-1-body').addClass('d-none');
            $('#block-1-new-section').removeClass('d-none').addClass('d-flex');
            // $('#block-1-new-section button').attr("onclick","newSection(4, 1, ['body', 'sub_headline', 'list'])");
            $('#block-2-headline').removeClass('d-none');
            $('#block-2-sub-headline').addClass('d-none');
            $('#block-2-body').addClass('d-none');
            $('#block-2-new-section button').attr(
                'onclick',
                "newSection(4, 2, ['body', 'sub_headline', 'list', 'checkbox'])"
            );
            $('#block-3-headline').removeClass('d-none');
            $('#block-3-body').removeClass('d-none');
            $('#block-3-phone').removeClass('d-none');
            $('#numbers_holder').removeClass('d-none');
            $('#questions_holder').removeClass('d-none').addClass('d-flex');
            $('#block-3-new-section').removeClass('d-flex').addClass('d-none');
            $('#block-3-new-section button').attr('onclick', '');
        }
    });
});

function addDigit(element) {
    $('#numbers_holder').html('');
    $('#questions_holder').html('');

    $(element)
        .val()
        .split('')
        .forEach(function (number) {
            $('#numbers_holder').append(`
            <div>
            <span class="number" onclick="selectNumber(this)" digit_id="${digitIndex}">${number}</span>
            <input type="hidden" name="digits[${digitIndex}][value]" value="${number}" />
            <input type="hidden" name="digits[${digitIndex}][question_id]" value />
            <input type="hidden" name="digits[${digitIndex}][sort]" value="${digitIndex}" />
            </div>
          `);

            digitIndex++;
        });
}

function selectNumber(element) {
    $(element).toggleClass('selected');

    if ($(element).hasClass('selected')) {
        $(element).attr('question_id', questionIndex);
        $(
            `input[name="digits[${$(element).attr('digit_id')}][question_id]"]`
        ).val(questionIndex);
        addQuestion($(element).attr('digit_id'));
        addAnswer($(element).attr('question_id'));
        addAnswer($(element).attr('question_id'));
    } else {
        deleteQuestion($(element).attr('question_id'));
        $(
            `input[name="digits[${$(element).attr('digit_id')}][question_id]"]`
        ).val(null);
    }
}

function addQuestion(digit_id) {
    const $questionHolder = $('#questions_holder');
    const questionHtml = `
             <div id="question-${questionIndex}-holder" class="mb-3" digit_id="${digit_id}">
                         <span class="number placeholder">${$(
                             `span[digit_id="${digit_id}"]`
                         ).text()}</span>
                        <div class="d-flex flex-column mt-3">
                            <div class="d-flex">
                                <input class="col-7 mr-3" type="text"
                                       placeholder="${question_trans}"
                                       name="questions[${questionIndex}][title]">
                                <div class="col-2 d-flex flex-column justify-content-center">
                                <span class="delete-quiz-section" >
                <button type="button" class="btn-radius" style="--btn-margin-bottom: 0px;" onclick="deleteQuestion(${questionIndex})">
                    <svg class="mr-1"  xmlns="http://www.w3.org/2000/svg" style="height: 20px; margin-bottom: 3px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg><span>${delete_trans}</span></button>
               </div>

                            </div>
                            <div id="question-${questionIndex}-answers" class="d-flex flex-column answer-holder">

                            </div>
                        </div>
                        <div class="d-flex mb-3" style="cursor: pointer" onclick="addAnswer(${questionIndex})">
                            <span class="new-quiz-section mr-3">+</span>
                            <span>${new_answer_trans}</span>
                        </div>
                    </div>
            `;

    $questionHolder.append(questionHtml);
    questionIndex++;
}

function deleteQuestion(question_id, force = false) {
    $(
        `input[name="digits[${$(`#question-${question_id}-holder`).attr(
            'digit_id'
        )}][question_id]"]`
    ).val(null);
    $(`#question-${question_id}-holder`).remove();
    $(`span[question_id='${question_id}']`).removeClass('selected');

    if (force) {
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            },
            type: 'POST',
            url: '/ajax/delete-existing-prizegame-question',
            data: {
                id: question_id,
            },
        });
    }
}

function addAnswer(question_id) {
    const $answerHolder = $(`#question-${question_id}-answers`);
    const answerHtml = `
            <div class="d-flex" id="answer-${answerIndex}-holder">
                <input class="col-7 mr-3" type="text"
                       placeholder="${answer_trans}"
                       name="questions[${question_id}][answers][${answerIndex}][title]">
                       <div class="col-2 mr-3 d-flex flex-column justify-content-center pb-3">
                 <label class="container"
                        id="customer-satisfaction-not-possible">${correct_trans}
                     <input type="radio" name="questions[${question_id}][correct]" value="${answerIndex}">
                     <span class="checkmark"></span>
                 </label>
                 </div>

                 <div class="col-2 d-flex flex-column justify-content-center pb-3">
                <button type="button" class="btn-radius" style="--btn-margin-bottom: 0px;" onclick="deleteAnswer(${answerIndex})">
                    <svg class="mr-1"  xmlns="http://www.w3.org/2000/svg" style="height: 20px; margin-bottom: 3px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg><span>${delete_trans}</span></button>
               </div>
            </div>
            `;

    $answerHolder.append(answerHtml);
    answerIndex++;
}

function deleteAnswer(answer_id, force = false) {
    $(`#answer-${answer_id}-holder`).remove();

    if (force) {
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            },
            type: 'POST',
            url: '/ajax/delete-existing-prizegame-answer',
            data: {
                id: answer_id,
            },
        });
    }
}

function openModal(id) {
    $(`#${id}`).modal('show');
}

function saveLanguage() {
    $('#modal-language-select').modal('hide');
    $('input[name="language"]').val($('select[name="content-language"]').val());
    $('#language-select-button').text(
        $('select[name="content-language"] option:selected').text()
    );
    $('#language-select-button').removeClass('error');
}

function saveCompany() {
    $('#modal-company-select').modal('hide');
    $('input[name="company"]').val($('select[name="content-company"]').val());
    $('#company-select-button').text(
        $('select[name="content-company"] option:selected').text()
    );
    $('#company-select-button').removeClass('error');
}

function saveCountry() {
    $('#modal-country-select').modal('hide');
    $('input[name="country"]').val($('select[name="content-country"]').val());
    $('#country-select-button').text(
        $('select[name="content-country"] option:selected').text()
    );
    $('#country-select-button').removeClass('error');
}

function changeSectionHeader(element, uploadContainerId) {
    const text = $(element).closest('label').text();
    $(element).parent().parent().parent().prev('.sectionHeader').html(text);

    if (text.includes('Checkbox')) {
        $(`#upload-container-${uploadContainerId}`)
            .removeClass('d-none')
            .addClass('d-flex');
    } else {
        $(`#upload-container-${uploadContainerId}`)
            .removeClass('d-flex')
            .addClass('d-none');
    }
}

function newSection(lastId, blockId, sectionType) {
    let justify = '';
    let margin = '';

    if (localStorage.getItem('prizegame_type') === '5') {
        justify = `justify-content-start`;
        margin = `mb-3`;
    } else {
        justify = `justify-content-between`;
        margin = ``;
    }

    let typeHTML = '';
    let sectionHeader = '';

    let subHeadlineHTML =
        `<label class="container ` +
        margin +
        `" id="customer-satisfaction-not-possible">${sub_headline_trans}
                     <input type="radio" name="sections[${
                         sectionIndex + lastId
                     }][type]" value="2">
                     <span class="checkmark" onclick="changeSectionHeader(this, ${
                         sectionIndex + lastId
                     })"></span>
                 </label>`;
    let listHTML = `<label class="container" id="customer-satisfaction-not-possible">${list_trans}
                      <span class="d-none">${list_alt_trans}</span>
                     <input type="radio" name="sections[${
                         sectionIndex + lastId
                     }][type]" value="3">
                     <span class="checkmark" onclick="changeSectionHeader(this, ${
                         sectionIndex + lastId
                     })"></span>
                 </label>`;
    let bodyHTML =
        `<label class="container ` +
        margin +
        `" id="customer-satisfaction-not-possible">${body_trans}
                     <input type="radio" name="sections[${
                         sectionIndex + lastId
                     }][type]" checked="checked" value="4">
                     <span class="checkmark" onclick="changeSectionHeader(this, ${
                         sectionIndex + lastId
                     })"></span>
                 </label>`;
    let checkboxHTML = `<label class="container" id="customer-satisfaction-not-possible">${checkbox_trans}
                     <input type="radio" name="sections[${
                         sectionIndex + lastId
                     }][type]" value="5">
                     <span class="checkmark" onclick="changeSectionHeader(this, ${
                         sectionIndex + lastId
                     })"></span>
                 </label>`;
    let checkboxCheckedHTML = `<label class="container" id="customer-satisfaction-not-possible">${checkbox_trans}
                     <input type="radio" name="sections[${
                         sectionIndex + lastId
                     }][type]" checked="checked" value="5">
                     <span class="checkmark" onclick="changeSectionHeader(this, ${
                         sectionIndex + lastId
                     })"></span>
                 </label>`;

    if (sectionType.indexOf('body') !== -1) {
        typeHTML += bodyHTML;
        sectionHeader = `${body_trans}`;
    }
    if (sectionType.indexOf('sub_headline') !== -1) {
        typeHTML += subHeadlineHTML;
        sectionHeader = `${body_trans}`;
    }
    if (sectionType.indexOf('list') !== -1) {
        typeHTML += listHTML;
        sectionHeader = `${body_trans}`;
    }
    if (sectionType.indexOf('checkbox') !== -1 && sectionType.length > 1) {
        typeHTML += checkboxHTML;
        sectionHeader = `${body_trans}`;
    }
    if (sectionType.indexOf('checkbox') !== -1 && sectionType.length === 1) {
        typeHTML += checkboxCheckedHTML;
        sectionHeader = `${checkbox_trans}`;
    }

    const html =
        `
       <div>
         <h1 class="sectionHeader">` +
        sectionHeader +
        `</h1>
         <div class="row">
         <input type="hidden" name="sections[${
             sectionIndex + lastId
         }][block]" value="${blockId}">
             <div class="col-8">
                 <textarea name="sections[${
                     sectionIndex + lastId
                 }][value]" cols="30" rows="5"
                           style="margin: 0 !important;"></textarea>
             </div>
             <div class="col-2 d-flex flex-column ` +
        justify +
        `">` +
        typeHTML +
        `</div>
               <div class="col-2 d-flex flex-column justify-content-center">
                <button type="button" class="btn-radius" onclick="deleteSection(this)">
                    <svg class="mr-1"  xmlns="http://www.w3.org/2000/svg" style="height: 20px; margin-bottom: 3px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg><span>${delete_trans}</span></button>
               </div>
         </div>
                            <div class="d-none align-items-center justify-content-between" style=" padding-top: 30px" id="upload-container-${
                                Number(sectionIndex) + Number(lastId)
                            }">
                                <div class="d-flex flex-column">
                                    <div class="d-flex align-items-center mb-3"
                                         style="cursor: pointer">

                                        <svg onclick="triggerFileUpload('${
                                            'section-file-' +
                                            (Number(sectionIndex) + lastId)
                                        }')"
                                             id="${
                                                 'section-file-' +
                                                 Number(
                                                     sectionIndex +
                                                         Number(lastId)
                                                 )
                                             }-input-file-upload-trigger"
                                             class="ml-n1 mr-1"
                                             xmlns="http://www.w3.org/2000/svg"
                                             style="color: rgb(89, 198, 198); height: 50px; width: 50px; cursor: pointer"
                                             fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  stroke-width="2"
                                                  d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                        </svg>

                                        <svg onclick="deleteUploadedFile('${
                                            'section-file-' +
                                            (Number(sectionIndex) +
                                                Number(lastId))
                                        }-input', '${file_trans}')"
                                             class="d-none mr-1 ml-n1"
                                             id="${
                                                 'section-file-' +
                                                 (Number(sectionIndex) +
                                                     Number(lastId))
                                             }-input-file-delete-trigger"
                                             xmlns="http://www.w3.org/2000/svg"
                                             style="color: rgb(89, 198, 198); height: 50px; width: 50px; cursor: pointer"
                                             fill="none"
                                             viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  stroke-width="2"
                                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>

                                        <span id="${
                                            'section-file-' +
                                            (Number(sectionIndex) +
                                                Number(lastId))
                                        }-input-uploaded-file-name"
                                        >${file_trans}</span>
                                    </div>
                                </div>
                                <input class="d-none"
                                       id="${
                                           'section-file-' +
                                           (Number(sectionIndex) +
                                               Number(lastId))
                                       }-input"
                                       name="sections[${
                                           Number(sectionIndex) + Number(lastId)
                                       }][document][file]" type="file">

                                        <input type="text" name="sections[${
                                            Number(sectionIndex) +
                                            Number(lastId)
                                        }][document][name]" class="col-6"
                                       placeholder="...">
                            </div>
       </div>
    `;

    $('#sections-block-' + blockId).append(html);

    if (sectionType.indexOf('checkbox') !== -1 && sectionType.length === 1) {
        $(`#upload-container-${sectionIndex + lastId}`)
            .removeClass('d-none')
            .addClass('d-flex');
    }

    changeFileEvent();
    sectionIndex++;
}

function deleteSection(element) {
    Swal.fire({
        title: 'Biztos, hogy törölni szeretné?',
        text: 'A művelet nem visszavonható!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Igen, törlöm!',
        cancelButtonText: 'Mégsem',
    }).then(function (result) {
        if (result.value) {
            const holder = $(element).parent().parent().parent();
            holder.remove();
            sectionIndex--;
        }
    });
}

function deleteExistingSection(id, element_id) {
    Swal.fire({
        title: 'Biztos, hogy törölni szeretné?',
        text: 'A művelet nem visszavonható!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Igen, törlöm!',
        cancelButtonText: 'Mégsem',
    }).then(function (result) {
        if (result.value) {
            $(`#${element_id}`).remove();

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                        'content'
                    ),
                },
                type: 'POST',
                url: '/ajax/delete-existing-prizegame-section',
                data: {
                    id: id,
                },
            });
        }
    });
}

function triggerFileUpload(id) {
    $(`input[id="${id}-input"]`).trigger('click');
}

function changeFileEvent() {
    $('input[type="file"]').each(function () {
        $(this).on('change', function () {
            const filename = $(this)
                .val()
                .replace(/C:\\fakepath\\/i, '');
            const id = $(this).attr('id');
            $(`#${id}-uploaded-file-name`).html(filename);
            $(`#${id}-file-upload-trigger`).addClass('d-none');
            $(`#${id}-file-delete-trigger`).removeClass('d-none');
        });
    });
}

function deleteUploadedFile(id, upload_trans, model_id = null, type = null) {
    Swal.fire({
        title: 'Biztosan törölni szeretné?',
        text: 'A művelet nem visszavonható!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Igen, törlöm!',
    }).then((result) => {
        if (result.value) {
            $(`input[id="${id}-input"]`).val('');
            $(`#${id}-uploaded-file-name`).html(upload_trans);
            $(`#${id}-file-upload-trigger`).removeClass('d-none');
            $(`#${id}-file-delete-trigger`).addClass('d-none');
            $(`#${id}-thumbnail-preview`).addClass('d-none');

            if (model_id) {
                if (type == 'document') {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                                'content'
                            ),
                        },
                        type: 'POST',
                        url: '/ajax/delete-existing-prizegame-document',
                        data: {
                            id: model_id,
                        },
                    });
                } else {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                                'content'
                            ),
                        },
                        type: 'POST',
                        url: '/ajax/delete-existing-prizegame-image',
                        data: {
                            id: model_id,
                        },
                    });
                }
            }
        }
    });
}

function readImageUrl(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        const id = input.getAttribute('id');
        reader.onload = function (e) {
            $(`#${id}-thumbnail-preview`)
                .attr('src', e.target.result)
                .removeClass('d-none');
        };

        reader.readAsDataURL(input.files[0]);
    }
}
