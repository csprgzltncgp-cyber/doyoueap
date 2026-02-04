@push('livewire_js')
    <script>
        Livewire.on('modelCannotDeleted', function () {
            Swal.fire(
                'Törlés sikertelen!',
                '',
                'error'
            );
        });

        $('input[type="checkbox"]').on('change', function () {
            $(this).next('span').children('.checked').toggleClass('d-none');
            $(this).next('span').children('.unchecked').toggleClass('d-none');
        });
    </script>
@endpush

<div>
    <style>
        .model {
            padding: 20px 20px;
            background: rgb(89, 198, 198);
            margin-bottom: 10px;
            cursor: pointer;
            color: #fff;
        }
    </style>

    <div class="form-row">
        <div wire:click="toggleOpen" class="case-list-in col model d-flex justify-content-between align-items-center p-3"
             style="{{$opened ? 'background: rgb(0,87,95) !important; color:white;' : ''}} margin-right:5px; margin-left:5px;">
            <label style="cursor: pointer;" class="mb-0">{{$country->code}}
                #{{$model->activity_id}}</label>

            <svg xmlns="http://www.w3.org/2000/svg"
                 style="color: white; width: 20px; height: 20px; {{$opened ? 'transform: rotate(180deg)' : ''}}"
                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
            </svg>
        </div>

        <div style="margin-bottom: 10px;" class="col-3 d-flex justify-content-center align-items-center">
            <svg wire:click="deleteModel" xmlns="http://www.w3.org/2000/svg"
                 style="width: 25px; height: 25px; color: rgb(89,198,198); cursor: pointer;" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
        </div>
    </div>

    <div class="{{$opened ? 'd-block' : 'd-none'}}">
        <div class="form-row">
            <div class="form-group col-6 mb-0">
                <label for="activity_id">Activity id</label>
                <input type="text" id="activity_id_{{$model->id}}" wire:model="model.activity_id"
                       style="margin-bottom: 0 !important">
            </div>

            <div class="form-group col-6 mb-0">
                @if($type == 'workshop')
                <label for="type">{{__('workshop.type')}}</label>
                <select name="model_type" id="model_type" wire:model="model_type">
                    <option disabled>{{__('common.please-choose')}}</option>
                    <option value="{{null}}">{{__('workshop.paid')}}</option>
                    <option value="free">{{__('workshop.free')}}</option>
                    <option value="gift">{{__('workshop.gift')}}</option>
                </select>
                @else
                    <label for="type"></label>
                    <label class="checkbox-container"
                        style="color: rgb(89, 198, 198); padding: 10px 0 10px 15px; border: 2px solid rgb(89,198,198) !important; font-size: 16px; margin-top: 8px;">{{__('workshop.free')}}
                        <input type="checkbox" wire:model="model.free">
                        <span class="checkmark d-flex justify-content-center align-items-center"
                            style="left:auto; right: 0; height: 100%; width: 50px; border-left: 2px solid rgb(89,198,198) !important">
                                <svg xmlns="http://www.w3.org/2000/svg" style="width: 25px; height: 25px; color: white"
                                    class="checked {{$model->free != null ? '' : 'd-none'}}"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>

                                <svg xmlns="http://www.w3.org/2000/svg" style="width: 25px; height: 25px;" fill="none"
                                    class="unchecked {{$model->free != null ? 'd-none' : ''}}"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                </svg>

                        </span>
                    </label>
                @endif
            </div>
        </div>

        @if(!$model->free)
            <label for="model_price">{{__('common.company_edit.price')}}</label>
            <div class="form-row">
                <div class="form-group col-4">
                    <select name="valuta" id="valuta_{{$model->id}}" wire:model="model.valuta">
                        <option value="chf">CHF</option>
                        <option value="czk">CZK</option>
                        <option value="eur">EUR</option>
                        <option value="huf">HUF</option>
                        <option value="mdl">MDL</option>
                        <option value="oal">OAL</option>
                        <option value="pln">PLN</option>
                        <option value="ron">RON</option>
                        <option value="rsd">RSD</option>
                        <option value="usd">USD</option>
                    </select>
                </div>
                <div class="form-group col">
                    <input type="number" id="model_price_{{$model->id}}" wire:model="model.{{$type}}_price"
                           placeholder="{{__('common.company_edit.price')}}">
                </div>
            </div>
        @endif
    </div>
</div>
