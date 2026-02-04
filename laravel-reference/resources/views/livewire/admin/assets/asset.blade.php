
@push('livewire_js')
    <script>
        // Initialise datepicker and set asset.date_of_purchase on datepicker date select event
        $("#date_of_purchase_{{$asset->id}}").datepicker({
            format: 'yyyy-mm-dd',
            setDate: moment().format('YYYY-MM-DD')
        });

        $("#date_of_purchase_{{$asset->id}}").change(function(event)  {
            @this.set('asset.date_of_purchase', event.target.value);
        });

    </script>
@endpush
<div>
    <div class="form-row mt-3 ml-0">
        <div class="form-group mr-2">
            <div class="d-flex flex-column">
                <div class="input-group p-0">
                    <div class="input-group-prepend">
                        <div class="eq-data">
                            <span class="eq-title mr-3">
                                {{ $asset_index + 1 }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- If owner is not strorage (900) --}}
        @if($is_search_result)
            <div class="form-group mr-2">
                <div class="d-flex flex-column">
                    <div class="input-group p-0">
                        <div class="input-group-prepend">
                            <div class="eq-data">
                                <span class="eq-title mr-3">
                                    @if(!empty($asset->deleted_at))
                                        {{__('asset.waste')}}
                                    @else
                                        {{$asset->owner->name}}
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="form-group mb-2 mr-2">
            <div class="d-flex flex-column">
                <div
                    class="
                @error('asset.name')
                    required-border
                @enderror
                input-group p-0">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            @foreach ($types as $type)
                                @if ($type->id == $asset->asset_type_id)
                                    {{ $type->name }}
                                @endif
                            @endforeach
                            {{ __('asset.inventory_name') }}:
                        </div>
                    </div>
                    <input type="text" size="{{strlen($asset->name)+3}}"
                        wire:model="asset.name">
                </div>
            </div>
        </div>

        <div class="form-group mb-0 mr-2" >
            <div class="d-flex flex-column">
                <div
                    class="
                @error('asset.own_id')
                    required-border
                @enderror
                input-group p-0">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            {{ __('asset.own_id') }}:
                        </div>
                    </div>
                    <input type="text" size="{{strlen($asset->own_id)+3}}"
                        wire:model="asset.own_id">
                </div>
            </div>
        </div>

        <div class="form-group mb-0 mr-2" >
            <div
                class="
            @error('asset.cgp_id')
                required-border
            @enderror
            input-group p-0">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        {{ __('asset.cgp_id') }}:
                    </div>
                </div>
                <input disabled type="text" class="bg-white" size="{{strlen($asset->cgp_id)+3}}"
                    value="{{ $asset->cgp_id }}">
            </div>
        </div>

        <div class="form-group mb-0 mr-2" >
            <div class="d-flex flex-column">
                <div
                    class="
                @error('asset.' . $asset->id . '.date_of_purchase')
                    required-border
                @enderror
                input-group p-0">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            {{ __('asset.date_of_purchase') }}:
                        </div>
                    </div>
                    <input type="text" class="datepicker"
                        id="date_of_purchase_{{$asset->id}}"
                        wire:model="asset.date_of_purchase">
                </div>
            </div>
        </div>
        {{-- IF type is mobile phone --}}
        @if ($asset->asset_type_id == 3)
            <div class="form-group mb-0 mr-2">
                <div class="d-flex flex-column">
                    <div
                        class="
                    @error('asset.phone_num')
                        required-border
                    @enderror
                    input-group p-0">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                {{ __('asset.phone_number') }}:
                            </div>
                        </div>
                        <input type="text"  size="{{strlen($asset->phone_num)+3}}"
                            wire:model="asset.phone_num">
                    </div>
                </div>
            </div>

            <div class="form-group mb-0 mr-2" >
                <div class="d-flex flex-column">
                    <div
                        class="
                    @error('asset.pin')
                        required-border
                    @enderror
                    input-group col-12 p-0">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                {{ __('asset.pin') }}:
                            </div>
                        </div>
                        <input type="text" size="{{strlen($asset->pin)+3}}"
                            wire:model="asset.pin">
                    </div>
                </div>
            </div>

            <div class="form-group mb-0 mr-2">
                <div class="d-flex flex-column">
                    <div
                        class="
                    @error('asset.provider')
                        required-border
                    @enderror
                    input-group p-0">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                {{ __('asset.provider') }}:
                            </div>
                        </div>
                        <input type="text"  size="{{strlen($asset->provider)+3}}"
                            wire:model="asset.provider">
                    </div>
                </div>
            </div>

            <div class="form-group mb-0 mr-2">
                <div class="d-flex flex-column">
                    <div
                        class="
                    @error('asset.package')
                        required-border
                    @enderror
                    input-group p-0">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                {{ __('asset.package') }}:
                            </div>
                        </div>
                        <input type="text"  size="{{strlen($asset->package)+3}}"
                            wire:model="asset.package">
                    </div>
                </div>
            </div>

            <x-asset.buttons
                key="{{$asset->id}}"
                waste="{{($asset->deleted_at) ? true : false}}"
                storage="{{(optional($asset->owner)->id == 900) ? true : false}}" {{-- IF owner is storage (900) than storage is true --}}
            />
        @endif
        {{-- IF type is mobile phone --}}

        {{-- IF type is SIM card --}}
        @if ($asset->asset_type_id == 14)
        <div class="form-group mb-0 mr-2">
            <div class="d-flex flex-column">
                <div
                    class="
                @error('asset.phone_num')
                    required-border
                @enderror
                input-group p-0">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            {{ __('asset.phone_number') }}:
                        </div>
                    </div>
                    <input type="text"  size="{{strlen($asset->phone_num)+3}}"
                        wire:model="asset.phone_num">
                </div>
            </div>
        </div>

        <div class="form-group mb-0 mr-2" >
            <div class="d-flex flex-column">
                <div
                    class="
                @error('asset.pin')
                    required-border
                @enderror
                input-group col-12 p-0">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            {{ __('asset.pin') }}:
                        </div>
                    </div>
                    <input type="text" size="{{strlen($asset->pin)+3}}"
                        wire:model="asset.pin">
                </div>
            </div>
        </div>

        <div class="form-group mb-0 mr-2" >
            <div class="d-flex flex-column">
                <div
                    class="
                @error('asset.puk')
                    required-border
                @enderror
                input-group col-12 p-0">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            {{ __('asset.puk') }}:
                        </div>
                    </div>
                    <input type="text" size="{{strlen($asset->puk)+3}}"
                        wire:model="asset.puk">
                </div>
            </div>
        </div>


        <div class="form-group mb-0 mr-2">
            <div class="d-flex flex-column">
                <div
                    class="
                @error('asset.provider')
                    required-border
                @enderror
                input-group p-0">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            {{ __('asset.provider') }}:
                        </div>
                    </div>
                    <input type="text"  size="{{strlen($asset->provider)+3}}"
                        wire:model="asset.provider">
                </div>
            </div>
        </div>

        <div class="form-group mb-0 mr-2">
            <div class="d-flex flex-column">
                <div
                    class="
                @error('asset.package')
                    required-border
                @enderror
                input-group p-0">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            {{ __('asset.package') }}:
                        </div>
                    </div>
                    <input type="text"  size="{{strlen($asset->package)+3}}"
                        wire:model="asset.package">
                </div>
            </div>
        </div>

        <x-asset.buttons
            key="{{$asset->id}}"
            waste="{{($asset->deleted_at) ? true : false}}"
            storage="{{(optional($asset->owner)->id == 900) ? true : false}}" {{-- IF owner is storage (900) than storage is true --}}
        />
        @endif
        {{-- IF type is SIM card --}}

        {{-- IF asset type is not mobile phone and sim card --}}
        @if (!in_array($asset->asset_type_id, [3, 14]))
            <x-asset.buttons
                key="{{$asset->id}}"
                waste="{{($asset->deleted_at) ? true : false}}"
                storage="{{(optional($asset->owner)->id == 900) ? true : false}}" {{-- IF owner is storage (900) than storage is true --}}
                />
        @endif
    </div>

    <!-- Modal for asset item discard -->
    <div wire:ignore.self class="modal" tabindex="-1" id="discard_modal_{{$asset->id}}" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('asset.discard') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="discard_asset(Object.fromEntries(new FormData($event.target))">
                        <h7>{{ __('asset.discard_reason') }}</h7>
                        <textarea wire:model="discard_reason" class="modal-input" cols="30" rows="3"></textarea>
                        <h7>{{ __('asset.recycling_method') }}</h7>
                        <textarea wire:model="recycling_method" class="modal-input" cols="30" rows="3"></textarea>
                        <button type="button" wire:click="discard_asset()" class="mt-1 btn-radius">{{ __('asset.discard') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        window.addEventListener('asset_added', event => {
            $("#date_of_purchase_{{$asset->id}}").datepicker({
                format: 'yyyy-mm-dd',
                setDate: moment().format('YYYY-MM-DD')
            });

            $("#date_of_purchase_{{$asset->id}}").change(function(event)  {
                @this.set('asset.date_of_purchase', event.target.value);
            });
        });

        window.Livewire.on('trigger_delete_popup_{{$asset->id}}', asset_id => {
            Swal.fire({
                title: '{{ __('asset.warning_delete_item_title') }}',
                html: '{{ __('asset.warning_delete_item_text') }}',
                icon: 'warning',
                showCancelButton: true,
                }).then((result) => {
                    if (result.value) {
                        @this.call('delete_asset');
                    }
                });
        });

        window.Livewire.on('trigger_storage_popup_{{$asset->id}}', asset_id => {
            Swal.fire({
                    title: '{{ __('asset.warning_store_item') }}',
                    html: '{{ __('asset.warning_store_item') }}',
                    icon: 'warning',
                    showCancelButton: true,
                }).then((result) => {
                    if (result.value) {
                        @this.call('to_storage');
                    }
                });
        });

        window.Livewire.on('close_discard_modal_{{$asset->id}}', function() {
            $('#discard_modal_{{$asset->id}}').modal('hide');
        });
    </script>
</div>
