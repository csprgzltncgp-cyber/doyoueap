@push('livewire_js')
    <script src="/assets/js/chosen.js" type="text/javascript" charset="utf-8"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.full.min.js"
        integrity="sha512-AIOTidJAcHBH2G/oZv9viEGXRqDNmfdPVPYOYKGy3fti0xIplnlgMHUGfuNRzC6FkzIo0iIxgFnr9RikFxK+sw=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        Livewire.on('save_succesfull', function(){
            Swal.fire({
                title: '{{__('common.case_input_edit.successful_save')}}',
                text: '',
                icon: 'success',
                confirmButtonText: 'Ok'
            }).then((result) => {
                if (result.value) {
                    window.location.href = '{{route(auth()->user()->type.'.eap-online.live-webinar.index')}}';
                }
            });
        });

        Livewire.on('show_company_country', function($show){
            $("#companies_select_cont").toggle();
            $("#countries_select_cont").toggle();
        });

        Livewire.on('show_invoice_data_error', function(){
            Swal.fire({
                title: 'Could not retrive invocing data for the selected expert!',
                text: "Please check the expert's invocoing settings.",
                icon: 'error',
                confirmButtonText: 'Ok'
            });
        });

        Livewire.on('show_form_error', function(message) {
            Swal.fire({
                title: "{{ __('common.error-occured') }}",
                text: message,
                icon: 'error',
                confirmButtonText: 'Ok'
            });
        });

        $("#companies").chosen().change(function(e) {
            @this.set('selected_companies', $(e.target).val());
        });

        $("#countries").chosen().change(function(e) {
            @this.set('selected_countries', $(e.target).val());
        });

        $('#from').datetimepicker({
            format: 'Y-m-d H:i',
            step: 1,
            onChangeDateTime: function(currentDateTime, $input){
                @this.set('selected_date', $input.val());
            }
        });

        $('#upload_webinar_photo').click(function(){
            $('#webinar_photo').click();
        });
    </script>
@endpush
<div class="row">
    <link rel="stylesheet" href="/assets/css/list.css?v={{time()}}">
    <link rel="stylesheet" href="/assets/css/cases/view.css?t=<?php echo e(time()); ?>">
    <link rel="stylesheet" href="/assets/css/eap-online/articles.css?v={{time()}}">
    <link rel="stylesheet" href="/assets/css/form.css?v={{time()}}">
    <link rel="stylesheet" href="{{asset('assets/css/bordered-checkbox.css')}}?v={{time()}}">
    <link href="{{ asset('assets/css/chosen.css') }}" rel="stylesheet" type="text/css">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.min.css"
        integrity="sha512-f0tzWhCwVFS3WeYaofoLWkTP62ObhewQ1EZn65oSYDZUg1+CyywGKkWzm8BxaJj5HGKI72PnMH9jYyIFz+GH7g=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="{{asset('assets/css/datetimepicker.css')}}">
    <style>
        #companies_chosen {
            flex: 1 !important;
        }

        .chosen-container {
            display: flex!important;
            height: 100%!important;
        }
        .chosen-choices {
            border: none!important;
            margin-bottom: 0px !important;
            box-shadow: none !important;
        }

        .chosen-search-input {
            color: black!important;
        }
    </style>

    <div class="row col-12">
        <div class="col-12">
            {{ Breadcrumbs::render('eap-online.live-webinars.create') }}
            <h1>{{ __('eap-online.live-webinars.add') }}</h1>

            <form class="col-5 ml-0 pl-0">
                <label class="checkbox-container mt-0 w-100 pl-2"
                style="color: rgb(89, 198, 198); border: 2px solid rgb(89,198,198) !important; font-size: 16px; margin-top: 8px; padding-top: 10px !important; padding-bottom: 10px !important;">
                    {{ __('eap-online.live-webinars.all_company') }}
                    <input type="checkbox" class="delete_later d-none" wire:model="all_company" />
                    <span class="checkmark d-flex justify-content-center align-items-center"
                            style="left:auto; right: 0; height: 100%; width: 50px; border-left: 2px solid rgb(89,198,198) !important; {{ !$all_company ? 'background-color: #eee !important;' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg"
                                class="checked {{!$all_company ? 'd-none' : ''}}"
                                style="width: 25px; height: 25px; color: white" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                        <svg xmlns="http://www.w3.org/2000/svg"
                                class="unchecked {{$all_company ? 'd-none' : ''}}"
                                style="width: 20px; height: 20px;" fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </span>
                </label>
                <div id="companies_select_cont" class="mb-1 mt-3" style="border: 2px solid rgb(89,198,198) !important;" wire:ignore>
                    <div class="d-flex flex-row align-items-center pl-2">
                        <span class="mr-1" style="color: rgb(89, 198, 198);">{{ __('eap-online.live-webinars.companies') }}:</span>
                        <select data-placeholder="{{__('common.please-choose-one')}}" id="companies" wire:ignore multiple class="chosen-select" style="margin:0px!important; padding: 10px 0px 10px 0px !important; border:0px!important; color:black!important">
                            @foreach ($companies as $company)
                                <option value="{{$company->id}}">{{$company->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div id="countries_select_cont" class="mb-1 mt-3" style="border: 2px solid rgb(89,198,198) !important;" wire:ignore>
                    <div class="d-flex flex-row align-items-center pl-2">
                        <span class="mr-1" style="color: rgb(89, 198, 198);">{{ __('eap-online.live-webinars.countries') }}:</span>
                        <select data-placeholder="{{__('common.please-choose-one')}}" id="countries" wire:ignore multiple class="chosen-select" style="margin:0px!important; padding: 10px 0px 10px 0px !important; border:0px!important; color:black!important">
                            @foreach ($countries as $country)
                                <option value="{{$country->id}}">{{$country->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="mb-1 mt-3" style="border: 2px solid rgb(89,198,198) !important;">
                    <div class="d-flex flex-row align-items-center pl-2">
                        <span class="mr-1" style="color: rgb(89, 198, 198);">{{ __('eap-online.live-webinars.language') }}:</span>
                        <select data-placeholder="{{__('common.please-choose-one')}}" wire:model="live_webinar.language_id" class="chosen-select" style="margin:0px!important; padding: 10px 0px 10px 0px !important; border:0px!important; color:black!important">
                            <option value="" selected hidden>{{__('common.please-choose-one')}}</option>
                            @if ($languages)
                                @foreach ($languages as $language)
                                    <option value="{{$language->id}}">{{$language->name}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
                <div class="mb-1 mt-3" style="border: 2px solid rgb(89,198,198) !important;">
                    <div class="d-flex flex-row align-items-center pl-2">
                        <span class="mr-1" style="color: rgb(89, 198, 198);">{{ __('eap-online.live-webinars.permission') }}:</span>
                        <select wire:model="live_webinar.permission_id" style="margin: 0px 0px 0px 10px !important; padding: 10px 0px 10px 0px !important; border:0px!important; color:black!important">
                            <option value="" selected hidden>{{__('common.please-choose-one')}}</option>
                            @if ($permissions)
                                @foreach ($permissions as $permission)
                                    <option value="{{$permission->id}}">{{ $permission->translation->value }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
                <div class="mb-1 mt-3" style="border: 2px solid rgb(89,198,198) !important;">
                    <div class="d-flex flex-row align-items-center pl-2">
                        <span class="mr-1" style="color: rgb(89, 198, 198);">{{ __('eap-online.live-webinars.expert') }}:</span>
                        <select wire:model="live_webinar.user_id" style="margin: 0px 0px 0px 10px !important; padding: 10px 0px 10px 0px !important; border:0px!important; color:black!important">
                            <option value="" selected hidden>{{__('common.please-choose-one')}}</option>
                            @if ($experts)
                                @foreach ($experts as $expert)
                                    <option value="{{$expert->id}}">{{ $expert->name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
                <div class="mb-1 mt-3" style="border: 2px solid rgb(89,198,198) !important;" wire:ignore>
                    <div class="d-flex flex-row align-items-center pl-2">
                        <span class="mr-1" style="color: rgb(89, 198, 198);">{{ __('eap-online.live-webinars.currency') }}:</span>
                        <input readonly type="text" wire:model='live_webinar.currency' style="margin-bottom: 0 !important; border: 0px !important; background-color: transparent !important; color: black !important;">
                    </div>
                </div>
                <div class="mb-1 mt-3" style="border: 2px solid rgb(89,198,198) !important;" wire:ignore>
                    <div class="d-flex flex-row align-items-center pl-2">
                        <span class="mr-1" style="color: rgb(89, 198, 198);">{{ __('eap-online.live-webinars.price') }}:</span>
                        <input type="number" wire:model='live_webinar.price' style="margin-bottom: 0 !important; border: 0px !important; background-color: transparent !important; color: black !important;">
                    </div>
                </div>
                <div class="mb-1 mt-3" style="border: 2px solid rgb(89,198,198) !important;" wire:ignore>
                    <div class="d-flex flex-row align-items-center pl-2">
                        <span class="mr-1" style="color: rgb(89, 198, 198);">{{ __('eap-online.live-webinars.topic') }}:</span>
                        <input type="text" wire:model='live_webinar.topic' style="margin-bottom: 0 !important; border: 0px !important; background-color: transparent !important; color: black !important;">
                    </div>
                </div>
                <div class="mb-1 mt-3" style="border: 2px solid rgb(89,198,198) !important;" wire:ignore>
                    <div class="d-flex flex-row align-items-center pl-2">
                        <span class="mr-1" style="color: rgb(89, 198, 198);">{{ __('eap-online.live-webinars.starts_at') }}:</span>
                        <input id="from" type="text" style="width:50% !important; margin-bottom: 0 !important; border: 0px !important; background-color: transparent !important; color: black !important;">
                    </div>
                </div>
                <div class="mb-1 mt-3" style="border: 2px solid rgb(89,198,198) !important;" wire:ignore>
                    <div class="d-flex flex-row align-items-center pl-2">
                        <span class="mr-1" style="color: rgb(89, 198, 198);">{{ __('eap-online.live-webinars.duration') }}:</span>
                        <input type="number" wire:model='live_webinar.duration' style="width:50% !important; margin-bottom: 0 !important; border: 0px !important; background-color: transparent !important; color: black !important;">
                    </div>
                </div>
                <div class="mb-1 mt-3" style="border: 2px solid rgb(89,198,198) !important;" wire:ignore>
                    <div class="d-flex flex-row align-items-center pl-2">
                        <div class="d-flex flex-column" style="height:240px!important">
                            <span class="mr-1" style="color: rgb(89, 198, 198);">{{ __('eap-online.live-webinars.description') }}:</span>
                        </div>
                        <textarea wire:model='live_webinar.description' cols="30" rows="10" style="margin-bottom: 0 !important; border: 0px !important; background-color: transparent !important; color: black !important;"></textarea>
                    </div>
                </div>
                <div class="d-flex flex-row w-100 ml-0 mt-3">
                    <input type="text" style="margin-right:15px!important;" placeholder="@if(empty($image)){{__('prizegame.gallery.photo')}} (jpg, jpeg, png max. 2 MB) @else{{basename($image)}}@endif" disabled>
                    <div class="justify-content-end {{empty($image) ? 'd-flex' : 'd-none'}}">
                        <button type="button" id="upload_webinar_photo" style="--btn-height:48px; --btn-margin-right: 0px"
                            class="text-center btn-radius">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="width:20px; height:20px;"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                            {{ __('common.upload') }}
                        </button>
                    </div>
                    <div class="justify-content-end {{empty($image) ? 'd-none' : 'd-flex '}}">
                        <button type="button" wire:click="delete_image" style="--btn-height:48px; --btn-margin-right: 0px"
                            class="text-center btn-radius">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="width:20px; height:20px;"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                            {{__('common.cancel')}}
                        </button>
                    </div>
                </div>
                @if($image)
                    <div class="d-flex flex-row ml-0">
                        <img class="col-12 h-100" src="{{$image->temporaryUrl()}}" alt="preview" style="border:2px solid rgb(89,198,198); border-radius: 1rem; width:200px; height:200px; padding:0; object-fit:cover;">
                    </div>
                @endif
                <input id="webinar_photo" class="d-none" type="file" wire:model='image'>
                <button type="button"
                    style="padding-bottom: 14px; padding-left:10px; text-transform: uppercase;"
                    wire:click="save()" name="button"
                    class="text-center btn-radius mt-3">
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
            </form>
        </div>
    </div>
</div>
