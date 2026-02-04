@push('livewire_js')

    <script src="{{asset('assets/js/toggleActive.js')}}?v={{time()}}" charset="utf-8"></script>
    <script src="/assets/js/datetime.js" charset="utf-8"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9.7.1/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.full.min.js"
            integrity="sha512-AIOTidJAcHBH2G/oZv9viEGXRqDNmfdPVPYOYKGy3fti0xIplnlgMHUGfuNRzC6FkzIo0iIxgFnr9RikFxK+sw=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        function deleteCompany(id, element) {
            Swal.fire({
                title: '{{__('common.are-you-sure-to-delete')}}',
                text: "{{__('common.operation-cannot-undone')}}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '{{__('common.yes-delete-it')}}',
                cancelButtonText: '{{__('common.cancel')}}',
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: 'DELETE',
                        url: '/ajax/delete-company/' + id,
                        success: function (data) {
                            if (data.status == 0) {
                                $(element).closest('.list-element').remove();
                            }
                        }
                    });
                }
            });
        }

        // Confirm and delete asset item
        Livewire.on('trigger_delete', key => {
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
            cursor: pointer;
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

        {{ Breadcrumbs::render('waste') }}
        <h1 class="col-12 pl-0">{{__('common.waste')}}</h1>

        <div class="pl-0 col-12 group mx-auto mt-2">
            <x-asset.search desc="{{__('asset.ascending_by_date')}}" asc="{{__('asset.descending_by_date')}}" />
        </div>

        @if (count($assets) > 0)
            <button type="button" class="green-box btn-radius button-c border-0 mb-2" wire:click="export()">
                <svg class="mr-1" xmlns="http://www.w3.org/2000/svg" style="height:20px; margin-bottom: 3px"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" web=""
                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
                {{ __('asset.download_waste') }}
            </button>
        @endif

        <!-- List dates that have assets -->
            <form action="" style="max-width: 1500px !important;" autocomplete="off" novalidate>
                @foreach($assets as $key => $item)
                    <div class="col-14 group mx-auto mb-5">
                        <div class="form-row">
                            <div class="form-group mr-2">
                                <div class="d-flex ">
                                    <div class="input-group col-12 p-0">
                                        <div class="input-group-prepend">
                                            <div class="eq-data">
                                                <span class="eq-title mr-3">{{$loop->index+1}}</span>
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
                                                        {{optional($item->owner)->name}}
                                                        @endif
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="form-group mb-0 mr-2">
                                <div class="d-flex">
                                    <div class="input-group col-12 p-0">
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
                                        <input wire:model="assets.{{$key}}.name" type="text" class="col-12 bg-white" size={{strlen($item->name)+3}}>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mb-0 mr-2">
                                <div class="d-flex ">
                                    <div class="input-group col-12 p-0">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                {{__('asset.own_id')}}:
                                            </div>
                                        </div>
                                        <input type="text" class="col-12 bg-white" wire:model="assets.{{$key}}.own_id" size={{strlen($item->own_id)+3}}>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mb-0 mr-2">
                                <div class="d-flex ">
                                    <div class="input-group col-12 p-0">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                {{__('asset.cgp_id')}}:
                                            </div>
                                        </div>
                                        <input disabled type="text" class="col-12 bg-white" value="{{$item->cgp_id}}" size={{strlen($item->cgp_id)+3}}>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mb-0 mr-2">
                                <div class="d-flex ">
                                    <div class="input-group col-14 p-0">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                {{__('asset.date_of_purchase')}}:
                                            </div>
                                        </div>
                                        <input type="text" class="col-12 bg-white datepicker"
                                        name="date_of_purchase" id="date_of_purchase_{{$loop->index}}"
                                        disabled
                                        value="{{$item->date_of_purchase}}"
                                        size={{strlen($item->date_of_purchase)}}>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group mb-0 mr-2">
                                <div class="d-flex ">
                                    <div class="input-group col-12 p-0">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                {{__('asset.discard_reason')}}:
                                            </div>
                                        </div>
                                        <input type="text" class="col-12 bg-white"
                                        wire:model="assets.{{$key}}.discard_reason"
                                        size={{strlen($item->discard_reason)+3}}>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group mb-0 mr-2">
                                <div class="d-flex">
                                    <div class="input-group col-12 p-0">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                {{__('asset.recycling_method')}}:
                                            </div>
                                        </div>
                                        <input type="text" class="col-12 bg-white"
                                        wire:model="assets.{{$key}}.recycling_method" size={{strlen($item->recycling_method)+3}}>
                                    </div>
                                </div>
                            </div>
                            <x-asset.buttons key="{{$key}}" waste="{{true}}"/>
                        </div>
                    </div>
                @endforeach
            </form>
    </div>
</div>
