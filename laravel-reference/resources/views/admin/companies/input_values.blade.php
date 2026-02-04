@extends('layout.master')

@section('title')
    Admin Dashboard
@endsection

@section('extra_js')
    <script>
        function addValue() {
            const randomId = Math.random().toString(16).slice(2);
            let html = `
            <div class="col-12 value">
                <div class="row">
                    <div class="col-5 pl-0">
                        <input name="new[name][]"  required value="" placeholder="Opció neve">
                    </div>

                    @if($input->id == 16)
                        <div class="col-4">
                            <select name="new[permission][]">
                                @foreach($permissions as $permission)
                                    <option value="{{$permission->id}}">{{$permission->slug}}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="form-group col-md-4 mb-0">
                        <div class="input-group col-12 p-0 mb-0" onclick="contractHolderCheckboxClicked('${randomId}')">
                            <label class="checkbox-container mt-0 w-100"
                                style="color: rgb(89,198,198); padding: 10px 0 10px 10px; border: 2px solid rgb(89,198,198) !important; font-size: 16px; margin-top: 8px;">
                                Contract Holderhez rendelve
                                <input disabled id="contract_holder_checkbox_${randomId}" type="checkbox" class="delete_later d-none">
                                <span class="checkmark d-flex justify-content-center align-items-center"
                                    style="left:auto; right: 0; height: 100%; width: 50px; border-left: 2px solid rgb(89,198,198) !important;">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        id="contract_holder_checkbox_checked_${randomId}"
                                        class="checked d-none"
                                        style="width: 25px; height: 25px; color: white" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        id="contract_holder_checkbox_unchecked_${randomId}"
                                        class="unchecked"
                                        style="width: 20px; height: 20px;" fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </span>
                            </label>
                        </div>
                    </div>

                    <div class="col-2">
                        <button type="button" onClick="deleteValue(this)" style="background: transparent;">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                style="height: 25px; width: 25px; color: rgb(89, 198, 198)"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </div>
                </div>
           `;

            @foreach($languages as $language)
                html += `
                    <div class="row translation">
                        <div class="col-1 text-center" style="padding-top:15px;">
                            {{$language->code}}
                        </div>
                        <div class="col-4 pl-0">
                            <input name="new_translations[{{$language->id}}][]"  value="" placeholder="Fordítás">
                        </div>
                `;
                @if($loop->first)
                    html += `
                    <div class="col-4">
                        <select name="new[contract_holder_id][]" class="d-none" id="contract_holder_select_${randomId}" >
                            <option value="{{null}}">Nincs</option>
                            @foreach($contract_holders as $contract_holer)
                                    <option value="{{$contract_holer->id}}">{{$contract_holer->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    `;
                @endif

                html += '</div>';
            @endforeach

            html += '</div>';

            $('form .values-holder').append(html);
        }

        function deleteValue(element) {
            $(element).closest('.value').remove();
        }

        function contractHolderCheckboxClicked(valueId){
            if($('#contract_holder_checkbox_' + valueId).is(':checked')){
                $('#contract_holder_select_' + valueId).addClass('d-none');
                $('#contract_holder_select_' + valueId).val(null).change();
                $('#contract_holder_checkbox_' + valueId).removeAttr('checked');
                $('#contract_holder_checkbox_checked_' + valueId).addClass('d-none');
                $('#contract_holder_checkbox_unchecked_' + valueId).removeClass('d-none');
            }else{
                $('#contract_holder_checkbox_' + valueId).attr('checked', 'checked');
                $('#contract_holder_select_' + valueId).removeClass('d-none');
                $('#contract_holder_checkbox_checked_' + valueId).removeClass('d-none');
                $('#contract_holder_checkbox_unchecked_' + valueId).addClass('d-none');
            }
        }
    </script>
@endsection

@section('extra_css')
    <style>
        h1 {
            margin-bottom: 20px;
        }

        input, select {
            width: 100%;
            display: block;
            appearance: none;
            -moz-appearance: none;
            -webkit-appearance: none;
            border: 2px solid rgb(89, 198, 198);
            padding: 10px 15px;
            width: 100%;
            margin-bottom: 20px;
            border-radius: 0px;
            outline: none !important;
            color: rgb(89, 198, 198);
        }

        .input {
            display: block;
            margin-bottom: 10px;
        }

        button, .button {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            outline: none !important;
            background: rgb(89, 198, 198);
            color: white !important;
            font-weight: bold;
            padding: 10px 15px;
            border: 0px solid black;
            text-decoration: none !important;
        }

        </style>
        <link rel="stylesheet" href="/assets/css/form.css?v={{time()}}">
        <link rel="stylesheet" href="{{asset('assets/css/bordered-checkbox.css')}}?v={{time()}}">
@endsection

@section('content')
    <div class="row m-0">
        <h1>'{{optional($input->translation)->value}}' opciók szerkesztése</h1>
        <form method="post" class="row w-100" style="max-width: initial;">
            {{csrf_field()}}
            <div class="values-holder col-12">
                @foreach($input->values as $value)
                    <div class="col-12 value">
                        <div class="row">
                            <div class="col-5 pl-0">
                                <input name="name[{{$value->id}}]" required value="{{$value->value}}"
                                       placeholder="Opció neve">
                            </div>
                            @if($input->id == 16)
                                <div class="col-4">
                                    <select name="permission[{{$value->id}}]">
                                        @foreach($permissions as $permission)
                                            @if($value->permission_id == $permission->id)
                                                <option selected
                                                        value="{{$permission->id}}">{{$permission->slug}}</option>
                                            @else
                                                <option value="{{$permission->id}}">{{$permission->slug}}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            <div class="form-group col-md-4 mb-0">
                                <div class="input-group col-12 p-0 mb-0" onclick="contractHolderCheckboxClicked({{$value->id}})">
                                    <label class="checkbox-container mt-0 w-100"
                                        style="color: rgb(89,198,198); padding: 10px 0 10px 10px; border: 2px solid rgb(89,198,198) !important; font-size: 16px; margin-top: 8px;">
                                        Contract Holderhez rendelve
                                        <input disabled id="contract_holder_checkbox_{{$value->id}}"  type="checkbox" {{ !empty($value->contract_holder_id) ? 'checked' : '' }} class="delete_later d-none">
                                        <span class="checkmark d-flex justify-content-center align-items-center"
                                            style="left:auto; right: 0; height: 100%; width: 50px; border-left: 2px solid rgb(89,198,198) !important;">
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                id="contract_holder_checkbox_checked_{{$value->id}}"
                                                class="checked {{!empty($value->contract_holder_id) ? '' : 'd-none'}}"
                                                style="width: 25px; height: 25px; color: white" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                id="contract_holder_checkbox_unchecked_{{$value->id}}"
                                                class="unchecked {{!empty($value->contract_holder_id) ? 'd-none' : ''}}"
                                                style="width: 20px; height: 20px;" fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </span>
                                    </label>
                                </div>
                            </div>

                            <div class="col-2">
                                <button type="button" onClick="deleteValue(this)" style="background: transparent;">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                         style="height: 25px; width: 25px; color: rgb(89, 198, 198)"
                                         fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        @foreach($languages as $language)
                            @php
                                $allTranslation = $value->allTranslations()->get();
                                $translation = $allTranslation->firstWhere('language_id',$language->id);
                            @endphp
                            <div class="row translation">
                                <div class="col-1 text-center" style="padding-top:15px;">
                                    {{$language->code}}
                                </div>
                                <div class="col-4 pl-0">
                                    <input name="translations[{{$language->id}}][{{$value->id}}]"
                                           value="{{$translation ? $translation->value : ''}}" placeholder="Fordítás">
                                </div>

                                @if($loop->first)
                                    <div class="col-4">
                                        <select id="contract_holder_select_{{$value->id}}" name="contract_holder_id[{{$value->id}}]" class="@if(empty($value->contract_holder_id)) d-none @endif">
                                            <option value="{{null}}">Nincs</option>
                                            @foreach($contract_holders as $contract_holer)
                                                @if($value->contract_holder_id == $contract_holer->id)
                                                    <option selected
                                                            value="{{$contract_holer->id}}">{{$contract_holer->name}}</option>
                                                @else
                                                    <option value="{{$contract_holer->id}}">{{$contract_holer->name}}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
            <div class="col-12 pl-0 mb-5">
                <div class="col-5">
                    <button type="button" onClick="addValue()" style="margin-bottom:10px;" class="col-4 btn-radius">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="height: 20px; width: 20px;"
                             fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                        </svg>
                        {{__('common.add')}}
                    </button>
                </div>
                <div class="col-5">
                    <button type="submit" class="col-4 btn-radius">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 " style="width: 20px; height: 20px;"
                             fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                        </svg>
                        {{__('common.save')}}
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection
