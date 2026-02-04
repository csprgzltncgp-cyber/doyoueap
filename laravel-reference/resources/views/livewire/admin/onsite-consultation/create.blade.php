@push('livewire_js')
    <script src="/assets/js/chosen.js" type="text/javascript" charset="utf-8"></script>
    <script>
        Livewire.on('save_succesfull', function(){
            Swal.fire({
                title: '{{__('common.case_input_edit.successful_save')}}',
                text: '',
                icon: 'success',
                confirmButtonText: 'Ok'
            }).then((result) => {
                if (result.value) {
                    window.location.href = '{{route(auth()->user()->type.'.eap-online.onsite-consultation.index')}}';
                }
            });
        });

        $("#languages").chosen().change(function(e) {
            @this.set('selected_languages', $(e.target).val());
        });
    </script>
@endpush
<div class="row">
    <link rel="stylesheet" href="/assets/css/list.css?v={{time()}}">
    <link rel="stylesheet" href="/assets/css/cases/view.css?t=<?php echo e(time()); ?>">
    <link rel="stylesheet" href="/assets/css/eap-online/articles.css?v={{time()}}">
    <link rel="stylesheet" href="/assets/css/form.css?v={{time()}}">
    <link href="{{ asset('assets/css/chosen.css') }}" rel="stylesheet" type="text/css">
    <style>
        #languages_chosen {
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
            {{ Breadcrumbs::render('eap-online.onsite-consultation.create') }}
            <h1>{{ __('eap-online.onsite_consultation.new_consultatiom') }}</h1>

            <form class="col-5 ml-0 pl-0">
                <div class="mb-1 mt-3" style="border: 2px solid rgb(89,198,198) !important;">
                    <div class="d-flex flex-row align-items-center pl-2">
                        <span class="mr-1" style="color: rgb(89, 198, 198);">{{ __('eap-online.onsite_consultation.company') }}:</span>
                        <select wire:model="selected_company" style="margin:0px!important; padding: 10px 0px 10px 0px !important; border:0px!important; color:black!important">
                            <option value="" selected hidden>{{__('common.please-choose-one')}}</option>
                            @foreach ($companies as $company)
                                <option value="{{$company->id}}">{{$company->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="mb-1 mt-3" style="border: 2px solid rgb(89,198,198) !important;">
                    <div class="d-flex flex-row align-items-center pl-2">
                        <span class="mr-1" style="color: rgb(89, 198, 198);">{{ __('eap-online.onsite_consultation.country') }}:</span>
                        <select @if(!$allow_related_selection) disabled @endif wire:model="selected_country" style="margin:0px!important; padding: 10px 0px 10px 0px !important; border:0px!important; color:black!important">
                            <option value="" selected hidden>{{__('common.please-choose-one')}}</option>
                            @if ($countries)
                                @foreach ($countries as $country)
                                    <option value="{{$country->id}}">{{$country->name}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
                <div class="mb-1 mt-3" style="border: 2px solid rgb(89,198,198) !important;">
                    <div class="d-flex flex-row align-items-center pl-2">
                        <span class="mr-1" style="color: rgb(89, 198, 198);">{{ __('eap-online.onsite_consultation.permission') }}:</span>
                        <select @if(!$allow_related_selection) disabled @endif wire:model="selected_permission" style="margin:0px!important; padding: 10px 0px 10px 0px !important; border:0px!important; color:black!important">
                            <option value="" selected hidden>{{__('common.please-choose-one')}}</option>
                            @if ($permissions)
                                @foreach ($permissions as $permission)
                                    <option value="{{$permission->id}}">{{(optional($permission->translation)->value) ? $permission->translation->value : $permission->slug}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
                <div class="mb-1 mt-3" style="border: 2px solid rgb(89,198,198) !important;">
                    <div class="d-flex flex-row align-items-center pl-2">
                        <span class="mr-1" style="color: rgb(89, 198, 198);">{{ __('eap-online.onsite_consultation.place') }}:</span>
                        <select @if(!$allow_related_selection) disabled @endif wire:model="selected_place" style="margin:0px!important; padding: 10px 0px 10px 0px !important; border:0px!important; color:black!important">
                            <option value="" selected hidden>{{__('common.please-choose-one')}}</option>
                            @if ($places)
                                @foreach ($places as $place)
                                    <option value="{{$place->id}}">{{$place->name}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
                <div class="mb-1 mt-3" style="border: 2px solid rgb(89,198,198) !important;" wire:ignore>
                    <div class="d-flex flex-row align-items-center pl-2">
                        <span class="mr-1" style="color: rgb(89, 198, 198);">{{ __('eap-online.actions.language') }}:</span>
                        <select id="languages" wire:ignore multiple class="chosen-select"  style="margin:0px!important; padding: 10px 0px 10px 0px !important; border:0px!important; color:black!important">
                            @if ($languages)
                                @foreach ($languages as $language)
                                    <option value="{{$language->id}}">{{$language->name}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
                <div class="mb-1 mt-3" style="border: 2px solid rgb(89,198,198) !important;" wire:ignore>
                    <div class="d-flex flex-row align-items-center pl-2">
                        <span class="mr-1" style="color: rgb(89, 198, 198);">{{ __('eap-online.onsite_consultation.consultation_type') }}:</span>
                        <select wire:model="selected_type" style="margin:0px!important; padding: 10px 0px 10px 0px !important; border:0px!important; color:black!important">
                            <option value={{null}}>{{__('common.please-choose')}}</option>
                            <option value="{{\App\Enums\OnsiteConsultationType::WITH_EXPERT}}">{{ __('eap-online.onsite_consultation.with_expert') }}</option>
                            <option value="{{\App\Enums\OnsiteConsultationType::WITHOUT_EXPERT}}">{{ __('eap-online.onsite_consultation.without_expert') }}</option>
                            <option value="{{\App\Enums\OnsiteConsultationType::ONLINE_WITH_EXPERT}}">{{ __('eap-online.onsite_consultation.online_with_expert') }}</option>
                        </select>
                    </div>
                </div>
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
