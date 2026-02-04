<script>
    function deleteContactInfo(id) {
        Swal.fire({
            title: '{{__('common.are-you-sure-to-delete')}}',
            text: '{{__('common.operation-cannot-undone')}}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: '{{__('common.yes')}}',
            cancelButtonText: '{{__('common.cancel')}}',
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    async: false,
                    type: 'DELETE',
                    url: '/ajax/delete-contact-information/' + id,
                    success: function () {
                        location.reload();
                    }
                });
            }
        });
    }
</script>

<div class="row col-12 d-flex align-items-center mt-5">
    <div class="d-flex flex-column col-12 p-0 row pb-10">
        <div class="col-12 d-flex">
            <input name="only_country[{{$country->id}}][country_id]" class="mr-3" type="hidden"
                   value="{{$country->id}}"
                   readonly>
            <div class="form-group w-25 mr-3">
                <label>{{__('eap-online.languages.country')}}</label>
                <input class="mr-3 mb-0 btn-input-field-height" type="text" value="{{$country->name}}"
                       readonly>
            </div>

            <div class="form-group w-25 mr-3">
                <label>{{__('common.email')}}</label>
                <input name="only_country[{{$country->id}}][email]" class="mr-3 mb-0 btn-input-field-height" type="email"
                       placeholder="{{__('common.email')}}"
                       value="{{$contact_info->email}}"
                >
            </div>
            <div class="form-group w-25 mr-3">
                <label>{{__('common.phone')}}</label>
                <input name="only_country[{{$country->id}}][phone]" class="mr-3 mb-0 btn-input-field-height" type="text"
                       placeholder="{{__('common.phone')}}"
                       value="{{$contact_info->phone}}"
                >
            </div>
            <div class="form-group w-25">
                <label></label>
                <button onclick="deleteContactInfo({{$contact_info->id}})"
                        class="text-center w-auto ml-5 btn-radius"
                        style="margin-top: 10px;  --btn-height:48px; --btn-min-width: auto; --btn-padding-x: 15px" type="button">
                    <svg xmlns="http://www.w3.org/2000/svg" style="height: 20px; width: 20px" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            </div>
        </div>
        <label class="pl-3">{{__('eap-online.articles.appearance')}}</label>
        <div class="col-12 d-flex">
            <div class="form-group w-25 mr-3">
                <label class="container checkbox-container"
                       id="customer-satisfaction-not-possible">{{__('eap-online.contact_information.phone_card')}}
                    <input type="hidden" name="only_country[{{$country->id}}][phone_card]"
                           value="disabled">
                    <input type="checkbox" name="only_country[{{$country->id}}][phone_card]"
                           @if(empty($contact_info->disabled_phone_card)) checked @endif>
                    <span class="checkmark"></span>
                </label>
            </div>
            <div class="form-group w-25 mr-3">
                <label class="container checkbox-container"
                       id="customer-satisfaction-not-possible">{{__('eap-online.contact_information.email_card')}}
                    <input type="hidden" name="only_country[{{$country->id}}][email_card]"
                           value="disabled">
                    <input type="checkbox" name="only_country[{{$country->id}}][email_card]"
                           @if(empty($contact_info->disabled_email_card)) checked @endif>
                    <span class="checkmark"></span>
                </label>
            </div>
            <div class="form-group w-25 mr-3">
                <label class="container checkbox-container"
                       id="customer-satisfaction-not-possible">{{__('eap-online.contact_information.chat_card')}}
                    <input type="hidden" name="only_country[{{$country->id}}][chat_card]" value="disabled">
                    <input type="checkbox" name="only_country[{{$country->id}}][chat_card]"
                           @if(empty($contact_info->disabled_chat_card)) checked @endif>
                    <span class="checkmark"></span>
                </label>
            </div>
        </div>
    </div>
</div>
