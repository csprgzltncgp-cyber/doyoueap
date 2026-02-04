$( function() {
   $( "#sortable" ).sortable();
   selectChange();
});

function selectChange(){
  $('select[name="type"]').change(function(){
    const type = $(this).val();
    console.log(type);
    $('select[name="input"] option').each(function(){
      if($(this).data('type') == type){
        $(this).removeClass('d-none');
      }
      else
      {
        $(this).addClass('d-none');
      }
    });
    $('select[name="input"]').val(0);
  })
}

function removeLi(element){
  $(element).closest('li').remove();
}

function add(){
  let html;
  const type = $('select[name="type"]').val();
  const input = $('select[name="input"]').val();
  const name = $('select[name="input"] option:selected').html();
  html = '<li>\
    <input type="hidden" name="type[]" value="' + type + '"/>\
    <input type="hidden" name="id[]" value="'+ input +'">\
    <span>' + name + '</span>\
    <button onClick="removeLi(this)"><i class="fas fa-trash-alt"></i></button>\
  </li>';
  $('#sortable').append(html);
}
