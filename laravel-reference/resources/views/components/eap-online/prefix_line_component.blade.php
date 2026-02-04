<script>
    function deletePrefix(id) {
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            async: false,
            type: 'GET',
            url: '/ajax/has-attached-article-prefix/' + id,
            success: function (response) {
                if (response.has_article_attached) {
                    Swal.fire(
                        '{{__('eap-online.prefix.in_use')}}',
                        '',
                        'error'
                    );
                } else {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        async: false,
                        type: 'GET',
                        url: '/ajax/delete-prefix/' + id,
                        success: function () {
                            location.reload();
                        }
                    });
                }
            }
        });
    }
</script>

<div class="row col-12 d-flex">
    <input type="text" class="w-25 mr-5 btn-input-field-height"  value="{{$prefix->firstTranslation}}" readonly>
    <input class="w-25 btn-input-field-height" type="text" value="{{$prefix->name}}"
           readonly>
    <button onclick="deletePrefix({{$prefix->id}})" class="text-center w-auto h-100 ml-5 btn-radius" style="--btn-min-width:auto; --btn-height:48px; --btn-padding-x: 15px" type="button">
        <svg xmlns="http://www.w3.org/2000/svg" style="height:20px; width:20px" fill="none" viewBox="0 0 24 24"
             stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
        </svg>
    </button>
</div>
