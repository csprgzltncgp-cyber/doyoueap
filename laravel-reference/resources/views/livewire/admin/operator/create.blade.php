@push('livewire_js')
    <script src="{{asset('assets/js/datetime.js')}}"></script>
    <script>
        Livewire.on('errorEvent', function(error){
            Swal.fire({
                title: error,
                text: '',
                icon: 'error',
                confirmButtonText: 'Ok'
            });
        });

        $('#connected_account_select').change(function () {
            @this.set('user.connected_account', $(this).val());
        });

         $('#start_of_employment').datepicker({
            format: 'yyyy-mm-dd',
            weekStart: 1,
        }).on('changeDate', function(e){
            @this.set('operatorData.start_of_employment', e.format('yyyy-mm-dd'));
            $('.datepicker').hide();
        });
    </script>
@endpush

<div>
    <link rel="stylesheet" href="/assets/css/form.css?v={{time()}}">
    <link rel="stylesheet" href="/assets/css/perfix-input.css?v={{time()}}">
    <link rel="stylesheet" href="{{asset('assets/css/cases/datetime.css')}}">
    @section('title', 'Admin Dashboard')

    {{ Breadcrumbs::render('operators.create', $user) }}
    <h1>{{__('common.create-operator')}}</h1>
    <form wire:submit.prevent='store' style="max-width:initial !important;">
        {{-- First block --}}
        <div style="margin-bottom:70px">
            <div class="input-group col-md-6 p-0">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        {{__('eap-online.footer.menu_points.name')}}:
                    </div>
                </div>
                <input type="text" wire:model='user.name' required>
            </div>

            <div class="input-group col-md-6 p-0">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        {{__("operator-data.call_center_country")}}:
                    </div>
                </div>
                <select wire:model='user.country_id' required>
                    <option value={{null}}>{{__('common.please-choose')}}</option>
                    @foreach($countries as $country)
                        <option value="{{$country->id}}">{{$country->code}}</option>
                    @endforeach
            </select>
            </div>

            @if(empty($user->connected_account))
                <div class="input-group col-md-6 p-0">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            {{__("operator-data.call_center_language")}}:
                        </div>
                    </div>
                    <input type="text" wire:model='operatorData.language' required>
                </div>


                <div class="input-group col-md-6 p-0">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            {{__("operator-data.position.title")}}:
                        </div>
                    </div>
                    <select wire:model='operatorData.position' required>
                        <option value={{null}}>{{__('common.please-choose')}}</option>
                        @foreach($positions as $id => $position)
                            <option value="{{$id}}">{{$position}}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="input-group col-md-6 p-0">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        {{__("common.operator-connected-account")}}:
                    </div>
                </div>
                <select id="connected_account_select">
                    <option @if($user->connected_account == null) selected @endif value="null">{{__('common.please-choose')}}</option>
                    @foreach($users as $u)
                        <option @if($user->connected_account == $u->id) selected @endif value="{{$u->id}}">{{$u->username}}</option>
                    @endforeach
                </select>
            </div>


            @if(!empty($user->connected_account))
                <div class="input-group col-md-6 mb-0 p-0">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            {{__("operator-data.dashboard_language")}}:
                        </div>
                    </div>
                    <select wire:model='user.language_id' required>
                        <option value={{null}}>{{__('common.please-choose')}}</option>
                        @foreach($languages as $language)
                            <option value="{{$language->id}}">{{$language->name}}</option>
                        @endforeach
                    </select>
                </div>
            @endif
        </div>
        {{-- First block --}}

        @if(empty($user->connected_account))
            {{-- Second block --}}
            <div style="margin-bottom:70px">
                <div class="form-row">
                    <div class="input-group col-md-6 pr-0">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                {{__("operator-data.employment_type.title")}}:
                            </div>
                        </div>
                        <select wire:model='operatorData.employment_type' required>
                            <option value={{null}}>{{__('common.please-choose')}}</option>
                            @foreach($employment_types as $id => $employment_type)
                                <option value="{{$id}}">{{$employment_type}}</option>
                            @endforeach
                        </select>
                    </div>
                    @if($this->operatorData->employment_type == App\Models\OperatorData::EMPLOYMENT_TYPE_CONTRACT)
                        <div class="input-group col-md-6 pr-0">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    {{__("operator-data.invoincing_name")}}:
                                </div>
                            </div>
                            <input type="text" wire:model='operatorData.invoincing_name'>
                        </div>
                    @else
                        <div class="form-group col-md-3 mb-0 pl-0">
                            <button onclick="document.getElementById('files').click();" class="text-center btn-radius" type="button"
                            style="padding-bottom: 12px; padding-top:12px; --btn-height:auto; --btn-margin-left: var(--btn-margin-x);">
                                <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="height:20px; width:20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                </svg>
                                {{__('operator-data.upload-contract')}}
                            </button>
                        </div>

                        <input class="d-none" type="file" id="files" wire:model="files" multiple>
                    @endif
                </div>

                @if($this->operatorData->employment_type != App\Models\OperatorData::EMPLOYMENT_TYPE_CONTRACT)
                    @foreach($files as $id => $file)
                        <div class="form-row" wire:key='file-{{$id}}'>
                            <div class="form-group col-md-6 mb-0">
                                <input type="text" class="col-12 dark" placeholder="{{$file->getClientOriginalName()}}" disabled>
                            </div>
                            <div class="form-row col-md-6">
                                <div class="form-group col-md-3">
                                    <button wire:click="deleteFile({{$id}})" type="button" style="padding-bottom: 14px; background: rgb(0,87,95);" class="text-center btn-radius">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="width:20px; height:20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        {{__('common.delete')}}
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif

                <div class="form-row">
                    <div class="form-group col-md-2 mb-0">
                        <input type="text" disabled placeholder="{{__('operator-data.start_of_employment')}}">
                    </div>

                    <div class="form-group col-md-4 mb-0" wire:ignore>
                        <input id="start_of_employment" name="start_of_employment" type="text"
                        value={{\Carbon\Carbon::parse($operatorData->start_of_employment)->format('Y-m-d')}}
                        placeholder="{{__('operator-data.please_select_a_date')}}">
                    </div>

                    @if($this->operatorData->employment_type == App\Models\OperatorData::EMPLOYMENT_TYPE_CONTRACT)
                        <div class="input-group col-md-3">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    {{__('operator-data.invoincing_post_code')}}:
                                </div>
                            </div>
                            <input type="text" wire:model='operatorData.invoincing_post_code'>
                        </div>

                        <div class="input-group col-md-3">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    {{__('operator-data.invoincing_country')}}:
                                </div>
                            </div>
                            <input type="text" wire:model='operatorData.invoincing_country'>
                        </div>
                    @endif
                </div>

                <div class="form-row">
                    <div class="input-group col-md-3">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                {{__("operator-data.salary")}}:
                            </div>
                        </div>
                        <input type="text" wire:model='operatorData.salary'>
                    </div>

                    <div class="input-group col-md-3">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                {{__("operator-data.salary_currency")}}:
                            </div>
                        </div>
                        <select wire:model='operatorData.salary_currency'>
                            <option value={{null}}>{{__('common.please-choose')}}</option>
                            <option value="huf">HUF</option>
                            <option value="eur">EUR</option>
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

                    @if($this->operatorData->employment_type == App\Models\OperatorData::EMPLOYMENT_TYPE_CONTRACT)
                        <div class="input-group col-md-6">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    {{__("operator-data.invoincing_city")}}:
                                </div>
                            </div>
                            <input type="text" wire:model='operatorData.invoincing_city'>
                        </div>
                    @endif
                </div>

                <div class="form-row">
                    <div class="input-group col-md-6  @if($this->operatorData->employment_type != App\Models\OperatorData::EMPLOYMENT_TYPE_CONTRACT) mb-0 @endif">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                {{__("operator-data.bank_account_number")}}:
                            </div>
                        </div>
                        <input type="text" wire:model='operatorData.bank_account_number'>
                    </div>

                    @if($this->operatorData->employment_type == App\Models\OperatorData::EMPLOYMENT_TYPE_CONTRACT)
                        <div class="input-group col-md-3">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    {{__("operator-data.invoincing_street")}}:
                                </div>
                            </div>
                            <input type="text" wire:model='operatorData.invoincing_street'>
                        </div>

                        <div class="input-group col-md-3">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    {{__("operator-data.invoincing_house_number")}}:
                                </div>
                            </div>
                            <input type="text" wire:model='operatorData.invoincing_house_number'>
                        </div>
                    @endif
                </div>

                @if($this->operatorData->employment_type == App\Models\OperatorData::EMPLOYMENT_TYPE_CONTRACT)
                    <div class="form-row justify-content-end">
                        <div class="input-group col-md-6">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    {{__("operator-data.tax_number")}}:
                                </div>
                            </div>
                            <input type="text" wire:model='operatorData.tax_number'>
                        </div>
                    </div>

                    <div class="form-row justify-content-end">
                        <div class="form-group col-md-6">
                            <button onclick="document.getElementById('files').click();"  class="text-center btn-radius" type="button" style="padding-bottom: 12px; padding-top:12px; --btn-height:auto;">
                                <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="height:20px; width:20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                </svg>
                                {{__('operator-data.upload-contract')}}
                            </button>
                        </div>
                    </div>

                    @foreach($files as $id => $file)
                        <div class="form-row col-md-6" wire:key='file-{{$id}}' style="margin-left: auto; padding:0;">
                            <div class="form-group col-md-6 mb-0 pl-0">
                                <input class="{{$loop->last ? 'mb-0' : ''}}" type="text" placeholder="{{$file->getClientOriginalName()}}" disabled>
                            </div>
                            <div class="form-row col-md-6 pr-0">
                                <div class="form-group col-md-12">
                                    <button wire:click="deleteFile({{$id}})" type="button" style="padding-bottom: 14px; background: rgb(0,87,95);" class="text-center btn-radius" >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="width:20px; height:20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        {{__('common.delete')}}
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <input class="d-none" type="file" id="files" wire:model="files" multiple>
                @endif
            </div>
            {{-- Second block --}}

            {{-- Third block --}}
            <div style="margin-bottom:70px">
                <div class="form-row">
                    <div class="input-group col-md-6">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                {{__("operator-data.operator_dashboard_username")}}:
                            </div>
                        </div>
                        <input type="text" wire:model='user.username' required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="input-group col-md-6">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                {{__("operator-data.operator_dashboard_password")}}:
                            </div>
                        </div>
                        <input type="password" wire:model='user.password'>
                    </div>
                </div>

                <div class="form-row">
                    <div class="input-group col-md-6 mb-0">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                {{__("operator-data.dashboard_language")}}:
                            </div>
                        </div>
                        <select wire:model='user.language_id' required>
                            <option value={{null}}>{{__('common.please-choose')}}</option>
                            @foreach($languages as $language)
                                <option value="{{$language->id}}">{{$language->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            {{-- Third block --}}

            {{-- Fourth block --}}
            <div style="margin-bottom:70px">
                <div class="input-group col-md-6 p-0">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            {{__("operator-data.eap_chat_username")}}:
                        </div>
                    </div>
                    <input type="text" wire:model='operatorData.eap_chat_username'>
                </div>

                <div class="input-group col-md-6 p-0">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            {{__("operator-data.eap_chat_password")}}:
                        </div>
                    </div>
                    <input type="password" wire:model='operatorData.eap_chat_password'>
                </div>
            </div>
            {{-- Fourth block --}}

            {{-- Fifth block --}}
            <div style="margin-bottom:70px">
                <div class="input-group col-md-6 p-0">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            {{__("operator-data.private_email")}}:
                        </div>
                    </div>
                    <input type="email" wire:model='operatorData.private_email' required>
                </div>

                <div class="input-group col-md-6 p-0">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            {{__("operator-data.company_email")}}:
                        </div>
                    </div>
                    <input type="email" wire:model='operatorData.company_email'>
                </div>

                <div class="input-group col-md-6 p-0">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            {{__("operator-data.private_phone")}}:
                        </div>
                    </div>
                    <input type="text" wire:model='operatorData.private_phone' required>
                </div>

                <div class="form-row">
                    <div class="input-group col-md-6 pr-0">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                {{__("operator-data.line_phone")}}:
                            </div>
                        </div>
                        <input type="text" wire:model='operatorData.company_phone' required>
                    </div>

                    <div class="form-group col-md-6 mb-0 pl-0">
                        <button class="text-center btn-radius" wire:click="addCompanyPhone" type="button"
                        style="padding-bottom: 12px; padding-top:12px; --btn-height:50px; --btn-margin-left: var(--btn-margin-x)">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="height:20px; width:20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                            </svg>
                            {{__('operator-data.add_company_phone')}}
                        </button>
                    </div>
                </div>

                @if(!empty($company_phones))
                    @foreach ($company_phones as $index => $company_phone)
                        <div class="input-group col-md-6 p-0">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    {{__("operator-data.company_phone")}}:
                                </div>
                            </div>
                            <input type="text" wire:model='company_phones.{{$index}}.phone'>
                        </div>
                    @endforeach
                @endif
            </div>
            {{-- Fifth block --}}
        @endif

        <div class="w-full mt-5 d-flex">
            <button type="submit" style="width:auto;" class="button btn-radius ml-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="width: 20px; height:20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                </svg>
                <span class="mt-1">
                    {{__('common.save')}}
                </span>
            </button>
        </div>
    </form>
</div>
