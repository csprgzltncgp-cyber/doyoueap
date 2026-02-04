@push('livewire_js')
    <script src="/assets/js/chosen.js" type="text/javascript" charset="utf-8"></script>
    <script src="{{asset('assets/js/datetime.js')}}"></script>
    <script>

        Livewire.on('errorEvent', function(error){
            Swal.fire({
                title: '{{__('asset.validation_error')}}',
                text: '',
                icon: 'error',
                confirmButtonText: 'Ok'
            });
        });

        Livewire.on('inventoryEmpty', function(){
            Swal.fire({
                title: '{{__('asset.inventory_empty')}}',
                text: '',
                icon: 'warning',
                confirmButtonText: 'Ok'
            });
        });

        Livewire.on('inventoryDataSaved', function(){
            Swal.fire({
                title: '{{__('common.case_input_edit.successful_save')}}',
                text: '',
                icon: 'success',
                confirmButtonText: 'Ok'
            }).then((result) => {
                if (result.value) {
                    window.location.href = '{{route(auth()->user()->type.'.assets.menu')}}';
                }
            });
        });

        // Create datepicker element when new item is added to the asset.
        Livewire.on('addDatePicker', function(currentItem){

            // Setup datepicker on input
            $("#date_of_purchase_"+currentItem).datepicker({
                format: 'yyyy-mm-dd',
            });

            // Bind value
            $("#date_of_purchase_"+currentItem).change(function (event) {
                @this.set('inventoryItems.'+currentItem+'.date_of_purchase', event.target.value);
            });
        });

    </script>
@endpush

<div>
    <link rel="stylesheet" href="/assets/css/form.css?v={{time()}}">
    <link rel="stylesheet" href="/assets/css/asset/asset.css?v={{time()}}">
    <link rel="stylesheet" href="/assets/css/perfix-input.css?v={{time()}}">
    <link rel="stylesheet" href="{{asset('assets/css/bordered-checkbox.css')}}?v={{time()}}">
    <link rel="stylesheet" href="{{asset('assets/css/cases/datetime.css')}}">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.min.css"
          integrity="sha512-f0tzWhCwVFS3WeYaofoLWkTP62ObhewQ1EZn65oSYDZUg1+CyywGKkWzm8BxaJj5HGKI72PnMH9jYyIFz+GH7g=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <style>
        .chosen-container{
            width: auto !important;
            flex: 1 0 auto !important;
        }

        .form-group input {
            color: black !important;
        }

        .form-group select {
            color: black !important;
        }

        .chosen-container.chosen-container-multi{
            width: min-content !important;
        }
    </style>

    {{ Breadcrumbs::render('assets.create') }}

    <h1>{{__('common.add-new-equipment')}}</h1>

    <form wire:submit.prevent="storeOwner()" style="max-width: 1500px !important;">
        <div class="form-row">
            <div class="form-group col-md-3 mb-0">
                <div class="d-flex flex-column">
                    <div class="
                    @error ('owner.name')
                        required-border
                    @enderror
                    input-group col-12 p-0">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                {{__('asset.name')}}:
                            </div>
                        </div>
                        <input type="text" class="col-12" wire:model="owner.name" required>
                    </div>
                </div>
            </div>

            <div class="form-group col-md-4 mb-0 pr-md-0">
                <div class="d-flex flex-column">
                    <div class="
                    @error ('owner.country_id')
                        required-border
                    @enderror
                    input-group col-12 p-0">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                {{__('asset.country')}}:
                            </div>
                        </div>
                        <select name="country_id" wire:change="" wire:model="owner.country_id" required>
                            <option value=""></option>
                            @foreach($countries as $country)
                                <option value="{{$country->id}}">{{$country->code}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <form wire:submit.prevent="storeItem()" style="max-width: 1500px !important;" autocomplete="off" novalidate>

        <div x-data="{selected: false}">

        @if ($inventoryItems)
            @foreach ($inventoryItems as $item)

                <div class="form-row">

                    <div class="form-group col-md-0 mb-0">
                        <div class="d-flex flex-column">
                            <div class="input-group col-12 p-0">
                                <div class="input-group-prepend">
                                    <div class="eq-data">
                                        <span class="eq-title mr-3">
                                            {{ $loop->index+1 }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group col-md-3 mb-0">
                        <div class="d-flex flex-column">
                            <div class="
                            @error ('inventoryItems.'.$loop->index.'.name')
                                required-border
                            @enderror
                            input-group col-12 p-0">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        {{ $itemsType[$loop->index]['type_name'] }} {{__('asset.inventory_name')}}:
                                    </div>
                                </div>
                                <input type="text" class="col-12"
                                wire:model="inventoryItems.{{$loop->index}}.name" >
                            </div>
                        </div>
                    </div>

                    <div class="form-group col-md-3 mb-0">
                        <div class="d-flex flex-column">
                            <div class="
                            @error ('inventoryItems.'.$loop->index.'.own_id')
                                required-border
                            @enderror
                            input-group col-12 p-0">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        {{__('asset.own_id')}}:
                                    </div>
                                </div>
                                <input type="text" class="col-12" wire:model="inventoryItems.{{$loop->index}}.own_id" >
                            </div>
                        </div>
                    </div>

                    <div class="form-group col-md-3 mb-0">
                        <div class="d-flex flex-column">
                            <div class="
                            @error ('inventoryItems.'.$loop->index.'.date_of_purchase')
                                required-border
                            @enderror
                            input-group col-12 p-0">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        {{__('asset.date_of_purchase')}}:
                                    </div>
                                </div>
                                <input type="text" class="col-12 datepicker"
                                name="date_of_purchase" id="date_of_purchase_{{$loop->index}}"
                                wire:model="inventoryItems.{{$loop->index}}.date_of_purchase" >
                            </div>
                        </div>
                    </div>
                    @if ($itemsType[$loop->index]['type_id'] != 3)
                    <div class="form-group mb-0">
                        <div class="d-flex flex-column">
                            <svg wire:click="deleteItem({{$loop->index}})" xmlns="http://www.w3.org/2000/svg" style="width: 20px; height:20px; margin-top: 13px; color: rgb(89,198,198); cursor: pointer;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </div>
                    </div>
                    @endif

                </div>

                {{-- IF type is mobile phone --}}
                @if ($itemsType[$loop->index]['type_id'] == 3)
                    <div class="form-row">

                        <div class="invisible form-group col-md-0 mb-0">
                            <div class="d-flex flex-column">
                                <div class="input-group col-12 p-0">
                                    <div class="input-group-prepend">
                                        <div class="eq-data">
                                            <span class="eq-title mr-3">
                                                {{ $loop->index+1 }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group col-md-3 mb-0">
                            <div class="d-flex flex-column">
                                <div class="
                                @error ('inventoryItems.'.$loop->index.'.phone_num')
                                    required-border
                                @enderror
                                input-group col-12 p-0">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            {{__('asset.phone_number')}}:
                                        </div>
                                    </div>
                                    <input type="text" class="col-12" wire:model="inventoryItems.{{$loop->index}}.phone_num" >
                                </div>
                            </div>
                        </div>

                        <div class="form-group col-md-2 mb-0" style="max-width: 120px;">
                            <div class="d-flex flex-column">
                                <div class="
                                @error ('inventoryItems.'.$loop->index.'.pin')
                                    required-border
                                @enderror
                                input-group col-12 p-0">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            {{__('asset.pin')}}:
                                        </div>
                                    </div>
                                    <input type="text" class="col-12" wire:model="inventoryItems.{{$loop->index}}.pin" >
                                </div>
                            </div>
                        </div>

                        <div class="form-group col-md-2 mb-0">
                            <div class="d-flex flex-column">
                                <div class="
                                @error ('inventoryItems.'.$loop->index.'.provider')
                                    required-border
                                @enderror
                                input-group col-12 p-0">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            {{__('asset.provider')}}:
                                        </div>
                                    </div>
                                    <input type="text" class="col-12" wire:model="inventoryItems.{{$loop->index}}.provider" >
                                </div>
                            </div>
                        </div>

                        <div class="form-group col-md-2 mb-0">
                            <div class="d-flex flex-column">
                                <div class="
                                @error ('inventoryItems.'.$loop->index.'.package')
                                    required-border
                                @enderror
                                input-group col-12 p-0">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            {{__('asset.package')}}:
                                        </div>
                                    </div>
                                    <input type="text" class="col-12" wire:model="inventoryItems.{{$loop->index}}.package" >
                                </div>
                            </div>
                        </div>

                        <div class="form-group col-md-2 mb-md-0 pl-md-2">
                            <svg wire:click="deleteItem({{$loop->index}})" xmlns="http://www.w3.org/2000/svg" style="width: 20px; height:20px; margin-top: 13px; color: rgb(89,198,198); cursor: pointer;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </div>
                    </div>
                @endif
                {{-- IF type is mobile phone --}}

                {{-- IF type is SIM card --}}
                @if ($itemsType[$loop->index]['type_id'] == 14)
                <div class="form-row">
                    <div class="form-group col-md-3 mb-0">
                        <div class="d-flex flex-column">
                            <div class="
                            @error ('inventoryItems.'.$loop->index.'.phone_num')
                                required-border
                            @enderror
                            input-group col-12 p-0">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        {{__('asset.phone_number')}}:
                                    </div>
                                </div>
                                <input type="text" class="col-12" wire:model="inventoryItems.{{$loop->index}}.phone_num" >
                            </div>
                        </div>
                    </div>

                    <div class="form-group col-md-2 mb-0" style="max-width: 120px;">
                        <div class="d-flex flex-column">
                            <div class="
                            @error ('inventoryItems.'.$loop->index.'.pin')
                                required-border
                            @enderror
                            input-group col-12 p-0">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        {{__('asset.pin')}}:
                                    </div>
                                </div>
                                <input type="text" class="col-12" wire:model="inventoryItems.{{$loop->index}}.pin" >
                            </div>
                        </div>
                    </div>

                    <div class="form-group col-md-2 mb-0" style="max-width: 120px;">
                        <div class="d-flex flex-column">
                            <div class="
                            @error ('inventoryItems.'.$loop->index.'.puk')
                                required-border
                            @enderror
                            input-group col-12 p-0">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        {{__('asset.puk')}}:
                                    </div>
                                </div>
                                <input type="text" class="col-12" wire:model="inventoryItems.{{$loop->index}}.puk" >
                            </div>
                        </div>
                    </div>

                    <div class="form-group col-md-2 mb-0">
                        <div class="d-flex flex-column">
                            <div class="
                            @error ('inventoryItems.'.$loop->index.'.provider')
                                required-border
                            @enderror
                            input-group col-12 p-0">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        {{__('asset.provider')}}:
                                    </div>
                                </div>
                                <input type="text" class="col-12" wire:model="inventoryItems.{{$loop->index}}.provider" >
                            </div>
                        </div>
                    </div>

                    <div class="form-group col-md-2 mb-0">
                        <div class="d-flex flex-column">
                            <div class="
                            @error ('inventoryItems.'.$loop->index.'.package')
                                required-border
                            @enderror
                            input-group col-12 p-0">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        {{__('asset.package')}}:
                                    </div>
                                </div>
                                <input type="text" class="col-12" wire:model="inventoryItems.{{$loop->index}}.package" >
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                {{-- IF type is SIM card --}}
            @endforeach
        @endif

        @if ($showType)
            <div class="form-row">
                <div class="form-group col-md-0 mb-0">
                    <div class="d-flex flex-column">
                        <div class="input-group col-12 p-0">
                            <div class="input-group-prepend">
                                <div class="eq-data">
                                    <span class="eq-title mr-3">
                                        {{ ($index == 0) ? 1 : $index }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group col-md-4 mb-0 pr-md-0">
                    <div class="d-flex flex-column">
                        <div class="input-group col-12 p-0">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    {{__('asset.type')}}:
                                </div>
                            </div>
                            <select name="asset_type_id" wire:change="changeType($event.target.value)" required>
                                <option @if ($type_id == null)
                                    selected
                                @endif >{{__('asset.select_type')}}</option>
                                @foreach($types as $type)
                                    <option value="{{$type->id}}">{{$type->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </form>

    <div class="row mt-3">
        <div class="col-sm-auto d-flex align-items-center">
            <button type="button" style="text-transform: uppercase;" wire:click="showType()" class="text-center btn-radius">
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-1"
                    style="width: 20px; height:20px;" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 4v16m8-8H4">
                    </path>
                </svg>
                <span class="mt-1">
                    {{ __('asset.add') }}
                </span>
            </button>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-auto d-flex align-items-center">
            <button type="button"
                style="padding-bottom: 14px; padding-left:10px; text-transform: uppercase;"
                wire:click="storeItem()" name="button"
                class="text-center btn-radius">
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-1"
                    style="height: 20px; width:20px;" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4">
                    </path>
                </svg>
                <span class="mt-1">
                    {{ __('common.save') }}
                </span>
            </button>
        </div>
    </div>
</div>
