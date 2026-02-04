@push('livewire_js')

    <script src="{{asset('assets/js/toggleActive.js')}}?v={{time()}}" charset="utf-8"></script>
    <script src="/assets/js/datetime.js" charset="utf-8"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9.7.1/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.full.min.js"
            integrity="sha512-AIOTidJAcHBH2G/oZv9viEGXRqDNmfdPVPYOYKGy3fti0xIplnlgMHUGfuNRzC6FkzIo0iIxgFnr9RikFxK+sw=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>

        // Close forward modal
        window.livewire.on('ownerChanged', () => {
            $('#forward_modal').modal('hide');
        });

        // Close waste modal
        window.livewire.on('itemDiscarded', () => {
            $('#discard_modal').modal('hide');
        });

        // Confirm and delete asset item
        Livewire.on('triggerDelete', key => {
            Swal.fire({
                title: '{{ __('asset.warning_delete_item_title') }}',
                html: '{{ __('asset.warning_delete_item_text') }}',
                icon: 'warning',
                showCancelButton: true,
            }).then((result) => {
                if (result.value) {
                    @this.call('deleteItem',key)
                }
            });
        });


        window.Livewire.on('trigger_delete_popup', asset_id => {
            Swal.fire({
                title: '{{ __('asset.warning_delete_item_title') }}',
                html: '{{ __('asset.warning_delete_item_text') }}',
                icon: 'warning',
                showCancelButton: true,
                    }).then((result) => {
                        if (result.value) {
                            @this.call('delete_asset', asset_id);
                        }
                    });
            });

    </script>

@endpush

@props([
    'active_row' => 'list-element case-list-in mb-0 col-12 group active',
    'inactive_row' => 'list-element case-list-in mb-0 col-12 group',
    'active_arrow' => 'caret-left float-right rotated-icon',
    'inactive_arrow' => 'caret-left float-right',
])

<div>
    <link rel="stylesheet" href="{{asset('assets/css/list.css')}}?v={{time()}}">
    <style>
        .list-element button, .list-element a {
            margin-right: 10px;
            display: inline-block;
        }

        .list-element button.delete-button {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background: transparent;
            border: 0px solid black;
            color: #007bff;
            outline: none;
        }

        .list-element {
            cursor: pointer;;
        }

        .form-group {
            height: 48px!important;
            margin-bottom: 10px!important;
        }

        .input-group {
            margin-bottom: 10px!important;
        }
    </style>
    <link rel="stylesheet" href="{{ asset('/assets/css/invoice-helper/invoicing.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/invoice-helper/cgp-card.css') }}">
    <link rel="stylesheet" href="/assets/css/form.css?v={{time()}}">
    <link rel="stylesheet" href="/assets/css/asset/asset.css?v={{time()}}">
    <link rel="stylesheet" href="/assets/css/perfix-input.css?v={{time()}}">
    <div class="row m-0">

        {{ Breadcrumbs::render('storage') }}
        <h1 class="col-12 pl-0">{{__('common.storage')}}</h1>

        <div class="pl-0 col-12 group mx-auto mt-2">
            <x-asset.search desc="{{ __('invoice-helper.ascending-by-name') }}"
                asc="{{ __('invoice-helper.descending-by-name') }}" />
        </div>

        @if (count($assets) > 0)
            <button type="button" class="green-box btn-radius button-c border-0 mb-2" wire:click="export()">
                <svg class="mr-1" xmlns="http://www.w3.org/2000/svg" style="height:20px; margin-bottom: 3px"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" web=""
                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
                {{ __('asset.download_storage') }}
            </button>
        @endif

        <!-- List dates that have purchased asset -->
        <form action="" style="max-width: 1500px !important;" autocomplete="off" novalidate>
            @foreach($assets as $key => $item)
                <div class="form-row mt-2 ml-0 mb-5">
                    <div class="form-group mr-2">
                        <div class="d-flex flex-column">
                            <div class="input-group p-0">
                                <div class="input-group-prepend">
                                    <div class="eq-data">
                                        <span class="eq-title mr-3">{{$key + 1}}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if(!empty($this->search))
                        <div class="form-group mr-2">
                            <div class="d-flex flex-column">
                                <div class="input-group p-0">
                                    <div class="input-group-prepend">
                                        <div class="eq-data">
                                            <span class="eq-title mr-3">
                                                @if(!empty($item->deleted_at))
                                                {{__('asset.waste')}}
                                                @else
                                                {{$item->owner->name}}
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="form-group mb-0 mr-2">
                        <div class="d-flex flex-column">
                            <div class="input-group p-0">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        @foreach ($types as $type)
                                            @if ($type->id == $item->asset_type_id)
                                                {{$type->name}}
                                            @endif
                                        @endforeach
                                        {{__('asset.inventory_name')}}:
                                    </div>
                                </div>
                                <input type="text" class="bg-white" size="{{strlen($item->name)+3}}" wire:model="assets.{{$key}}.name">
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-0 mr-2">
                        <div class="d-flex flex-column">
                            <div class="input-group p-0">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        {{__('asset.own_id')}}:
                                    </div>
                                </div>
                                <input type="text" wire:model="assets.{{$key}}.own_id" class="bg-white" size="{{strlen($item->own_id)+3}}">
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-0 mr-2">
                        <div class="d-flex flex-column">
                            <div class="input-group p-0">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        {{__('asset.cgp_id')}}:
                                    </div>
                                </div>
                                <input disabled type="text" class="bg-white" size="{{strlen($item->cgp_id)+3}}" value="{{$item->cgp_id}}" >
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-0 mr-2">
                        <div class="d-flex flex-column">
                            <div class="input-group p-0">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        {{__('asset.date_of_purchase')}}:
                                    </div>
                                </div>
                                <input disabled type="text" class="bg-white datepicker"
                                name="date_of_purchase" id="date_of_purchase_{{$loop->index}}"
                                value="{{ date('Y-m-d', strtotime($item->date_of_purchase)) }}" >
                            </div>
                        </div>
                    </div>

                    <x-asset.buttons key="{{$item->id}}" waste="{{ ($item->deleted_at) ? true : false }}" storage="{{true}}"/>
                </div>
            @endforeach
        </form>
    </div>

    <!-- Modal for asset item foward -->
    <div wire:ignore.self class="modal" tabindex="-1" id="forward_modal" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('asset.forward') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="changeOwner(Object.fromEntries(new FormData($event.target)))">
                        <select name="owner_id">
                            <option value="">{{ __('asset.select_owner') }}</option>
                            @foreach ($owners as $owner)
                                <option value="{{ $owner->id }}">
                                    {{ $owner->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('newOwner') <small class="error-txt">{{__('asset.validation_owner_required')}}</small> <div style="height: 20px;"></div> @enderror
                        <button class="mt-1 btn-radius">
                            <img class="mr-1" src="{{asset('assets/img/save.svg')}}" style="height: 20px; width: 20px" alt="">
                            {{ __('common.save') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for asset item discard -->
    <div wire:ignore.self class="modal" tabindex="-1" id="discard_modal" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('asset.discard') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="discardItem({{ $current_asset_id }})">
                        <h7>{{ __('asset.discard_reason') }}</h7>
                        <textarea wire:model.defer="discard_reason" class="modal-input" cols="30" rows="3"></textarea>
                        <h7>{{ __('asset.recycling_method') }}</h7>
                        <textarea wire:model.defer="recycling_method" class="modal-input" cols="30" rows="3"></textarea>
                        <button class="btn-radius mt-1">
                            <svg class="fuction-btn mr-2 mb-1"
                                xmlns="http://www.w3.org/2000/svg"
                                style="width: 20px; height:20px; color: white; cursor: pointer;"
                                fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m6 4.125l2.25 2.25m0 0l2.25 2.25M12 13.875l2.25-2.25M12 13.875l-2.25 2.25M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                            </svg>
                            {{ __('asset.discard') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
