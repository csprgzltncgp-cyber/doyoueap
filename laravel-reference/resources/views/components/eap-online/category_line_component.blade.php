<script>
    function deleteCategory(id) {
        Swal.fire({
            title: '{{__('common.are-you-sure-to-delete')}}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: '{{__('common.delete')}}',
            cancelButtonText: '{{__('common.cancel')}}',
        }).then(function (result) {
            if (result.value) {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    async: false,
                    type: 'GET',
                    url: '/ajax/has-attached-article-category/' + id,
                    success: function (response) {
                        if (response.has_article_attached) {
                            Swal.fire(
                                'A kategória használatban van!',
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
                                url: '/ajax/delete-category/' + id,
                                success: function () {
                                    location.reload();
                                }
                            });
                        }
                    }
                });
            }
        });

    }
</script>

<div class="row col-12 d-flex">
    <input type="text" readonly class="w-25 mr-5" value="{{$current_category->firstTranslation}}">
    @if($type == 'self-help')
        <select name="old_categories[{{$index}}][parent_id]" class="w-25 mr-5">
            <option value="null">{{__('eap-online.categories.no_parent')}}</option>
            @foreach($categories as $category)
                @if($category->id != $current_category->id)
                    <option value="{{$category->id}}"
                            @if($category->id == $current_category->parent_id) selected @endif>{{$category->name}}</option>
                @endif
            @endforeach
        </select>
    @endif
    <input class="w-25" type="text" value="{{$current_category->name}}"
           readonly>
    <button onclick="deleteCategory({{$current_category->id}})" class="text-center w-auto h-100 ml-5 btn-radius" style="--btn-height: 48px; --btn-min-width: auto; --btn-padding-x: 15px;" type="button">
        <svg xmlns="http://www.w3.org/2000/svg" style="height:20px; width:20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
        </svg>
    </button>
    <input type="hidden" name="old_categories[{{$index}}][category_id]" value="{{$current_category->id}}">
</div>
