let sectionIndex = 0;

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

function newSection(lastId) {
    const html = `
       <div>
         <h1 class="sectionHeader">${body_trans}</h1>
         <div class="row">
             <div class="col-8">
                 <textarea name="sections[${
                     sectionIndex + lastId
                 }][content]" cols="30" rows="5"
                           style="margin: 0 !important;"></textarea>
             </div>
             <div class="col-2 d-flex flex-column justify-content-between">
                 <label class="container"
                        id="customer-satisfaction-not-possible">${body_trans}
                     <input type="radio" name="sections[${
                         sectionIndex + lastId
                     }][type]" checked="checked" value="body">
                     <span class="checkmark" onclick="changeSectionHeader(this)"></span>
                 </label>
                  <label class="container"
                        id="customer-satisfaction-not-possible">${subtitle_trans}
                     <input type="radio" name="sections[${
                         sectionIndex + lastId
                     }][type]" value="subtitle">
                     <span class="checkmark" onclick="changeSectionHeader(this)"></span>
                 </label>
                 <label class="container"
                        id="customer-satisfaction-not-possible">${list_trans}
                      <span class="d-none">${list_alt_trans}</span>
                     <input type="radio" name="sections[${
                         sectionIndex + lastId
                     }][type]" value="list">
                     <span class="checkmark" onclick="changeSectionHeader(this)"></span>
                 </label>
             </div>
               <div class="col-2 d-flex flex-column justify-content-center">
                <button type="button" class="btn-radius" onclick="deleteSection(this)">
                   <svg class="mr-1" xmlns="http://www.w3.org/2000/svg" style="height: 20px; margin-bottom: 3px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                   <span>${delete_trans}</span></button>
               </div>
         </div>
       </div>
    `;

    $('#sections').append(html);
    sectionIndex++;
}

function changeSectionHeader(element) {
    const text = $(element).closest('label').text();
    $(element).parent().parent().parent().prev('.sectionHeader').html(text);
}

function openModal(id) {
    $(`#${id}`).modal('show');
}

function saveLanguage() {
    $('#modal-language-select').modal('hide');
    $('input[name="language"]').val($('select[name="article_language"]').val());
    $('#language-select-button').text(
        $('select[name="article_language"] option:selected').text()
    );
    $('#language-select-button').removeClass('error');
}
