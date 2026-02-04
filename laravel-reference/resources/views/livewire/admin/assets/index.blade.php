

@section('extra_css')
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
    </style>
@endsection

@section('extra_js')
    <script src="{{asset('assets/js/toggleActive.js')}}?v={{time()}}" charset="utf-8"></script>
    <script src="/assets/js/datetime.js" charset="utf-8"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9.7.1/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.full.min.js"
            integrity="sha512-AIOTidJAcHBH2G/oZv9viEGXRqDNmfdPVPYOYKGy3fti0xIplnlgMHUGfuNRzC6FkzIo0iIxgFnr9RikFxK+sw=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.js" type="text/javascript"></script>
    <script>
        Livewire.on('asset_data_saved', function() {
            Swal.fire({
                title: '{{ __('common.successful-change') }}',
                text: '',
                icon: 'success',
                confirmButtonText: 'Ok'
            });
        });

        Livewire.on('delete_owner_popup', function(owner_id) {
            Swal.fire({
                title: '{{ __('asset.warning_delete_owner_title') }}',
                html: '{{ __('asset.warning_delete_item_text') }}',
                icon: 'warning',
                showCancelButton: true,
            }).then((result) => {
                if (result.value) {
                    @this.call('delete_owner',owner_id)
                }
            });
        });
    </script>
@endsection

@section('extra_css')
    <link rel="stylesheet" href="/assets/css/cases/datetime.css?t={{time()}}">
@endsection

@section('title')
    Admin Dashboard
@endsection

<div>
    <link rel="stylesheet" href="{{ asset('/assets/css/invoice-helper/invoicing.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/invoice-helper/cgp-card.css') }}">
    <link rel="stylesheet" href="/assets/css/form.css?v={{time()}}">
    <link rel="stylesheet" href="/assets/css/asset/asset.css?v={{time()}}">
    <link rel="stylesheet" href="/assets/css/perfix-input.css?v={{time()}}">
    <link rel="stylesheet" href="{{ asset('assets/css/cases/datetime.css') }}">
    <style>
         .form-group {
            height: 48px!important;
            margin-bottom: 10px!important;
        }
        
        .input-group {
            margin-bottom: 10px!important;
        }

        form {
            max-width: 100%!important;
        }
    </style>
    <div class="row m-0">
        {{ Breadcrumbs::render('assets.list') }}
        <h1 class="col-12 pl-0">{{__('common.assets')}}</h1>
        <a class="col-12 pl-0 d-block" href="{{route(auth()->user()->type . '.assets.create')}}">{{__('common.add-new-equipment')}}</a>
        <a class="col-12 pl-0 d-block" href="{{route(auth()->user()->type . '.asset-types.create')}}">{{__('common.add-new-equipment-type')}}</a>

        <div class="pl-0 col-12 group mx-auto mt-2">
            <x-asset.search desc="{{ __('invoice-helper.ascending-by-name') }}"
                asc="{{ __('invoice-helper.descending-by-name') }}" />
        </div>
        
        <button type="button" class="green-box btn-radius button-c border-0 mb-2" wire:click="export">
            <svg class="mr-1" xmlns="http://www.w3.org/2000/svg" style="height:20px; margin-bottom: 3px"
                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" web=""
                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
            </svg>
            {{ __('asset.download_inventory') }}
        </button>

        <!-- List people who have asset items -->
        @if ($search != '')
            @foreach($filtered_assets as $asset)
                <form action="">
                    <div class="pl-0 col-12 bg-white">
                        @livewire('admin.assets.asset', ['asset' => $asset, 'index' => $loop->index, 'is_search_result' => true], key('asset_'.$asset->id))
                    </div>
                </form>
            @endforeach
        @else
            @foreach($owners as $owner)
                <div class="col-12 group mx-auto ml-0 pl-0">
                    <div class="{{ $show_inventory == $owner->id ? 'list-element case-list-in mb-0 col-12 group active ' : 'list-element case-list-in mb-0 col-12 group'}} d-flex flex-row">
                        @if (has_missing_information_on_asset($owner))
                            <svg xmlns="http://www.w3.org/2000/svg"
                                style="width: 20px; height:20px; color:#ffc208; margin-right:5px; margin-top:2px;" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd" />
                            </svg>
                        @endif
                        @if (!$owner->assets->count())
                            <svg class="fuction-btn" wire:click="$emit('delete_owner_popup', {{$owner->id}})"
                                xmlns="http://www.w3.org/2000/svg"
                                style="width: 20px; height:20px; color: red; margin-right:5px; cursor: pointer;"
                                fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        @endif
                        <div wire:click="show_inventory({{ $owner->id }})" class="d-flex w-100 justify-content-between">
                            <span id="owner_{{ $owner->id }}">{{ $owner->name }}</span>
                            <button class="{{ $show_inventory == $owner->id ? 'caret-left rotated-icon ' : 'caret-left'}}">
                                <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px;" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    @if ($show_inventory == $owner->id)
                        <div class="pl-0 mt-4 col-12 bg-white">
                            @livewire('admin.assets.owner', ['owner' => $owner], key('owner_'.$owner->id))
                        </div>
                    @endif
                </div>
            @endforeach
        @endif
    </div>
</div>
