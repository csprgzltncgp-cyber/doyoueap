<div class="col-12 input mb-5">
    <style>
        button, .button {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            outline: none !important;
            background: rgb(89, 198, 198);
            color: white !important;
            font-weight: bold;
            padding: 10px 15px;
            border: 0 solid black;
            display: inline-block;
            text-decoration: none !important;
        }
    </style>
    <div class="row">
        <div class="col-4">
            <input type="text" wire:model="caseInput.name" required placeholder="Input neve">
        </div>
        <div class="form-group col-4 mb-0">
            <label class="checkbox-container mt-0"
                   style="color: rgb(89, 198, 198); padding: 10px 0 10px 15px; border: 2px solid rgb(89,198,198) !important; font-size: 16px; margin-top: 8px;">
                {{__('common.case_input_edit.deletable_3_after_3_months')}}
                <input type="checkbox" class="delete_later d-none" wire:model="caseInput.delete_later">
                <span class="checkmark d-flex justify-content-center align-items-center"
                      style="left:auto; right: 0; height: 100%; width: 50px; border-left: 2px solid rgb(89,198,198) !important">
                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="checked {{$caseInput->delete_later ? '' : 'd-none'}}"
                         style="width: 25px; height: 25px; color: white" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                         <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="unchecked {{$caseInput->delete_later ? 'd-none' : ''}}"
                         style="width: 20px; height: 20px;" fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </span>
            </label>
        </div>
        <div class="form-group col-3 mb-0">
            <label class="checkbox-container mt-0"
                   style="color: rgb(89, 198, 198); padding: 10px 0 10px 15px; border: 2px solid rgb(89,198,198) !important; font-size: 16px; margin-top: 8px;">
                {{__('common.case_input_edit.connected_to_company')}}
                <input type="checkbox" class="delete_later d-none" wire:model="caseInput.company_id">
                <span class="checkmark d-flex justify-content-center align-items-center"
                      style="left:auto; right: 0; height: 100%; width: 50px; border-left: 2px solid rgb(89,198,198) !important">
                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="checked {{$caseInput->company_id != null ? '' : 'd-none'}}"
                         style="width: 25px; height: 25px; color: white" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                         <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="unchecked {{$caseInput->company_id != null ? 'd-none' : ''}}"
                         style="width: 20px; height: 20px;" fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </span>
            </label>
        </div>
        <div class="col-1 pl-0">
            <button type="button" wire:click="delete"
                    style="margin-left: 9px; padding-bottom: 12px; padding-top: 12px; background: transparent;">
                <svg xmlns="http://www.w3.org/2000/svg"
                     style="height: 25px; width: 25px; color: rgb(89, 198, 198)"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </button>
        </div>
    </div>
    <div class="row">
        <div class="col-4">
            <select wire:model="caseInput.type">
                <option value="integer">{{__('common.case_input_edit.integer')}}</option>
                <option value="double">{{__('common.case_input_edit.double')}}</option>
                <option value="date">{{__('common.case_input_edit.date')}}</option>
                <option value="text">{{__('common.case_input_edit.text')}}</option>
                <option value="select">{{__('common.case_input_edit.select')}}</option>
            </select>
        </div>

        <div class="col-4">
            {{--            && $caseInput->default_type == null--}}
            @if($caseInput->type == 'select' && !empty($caseInput->name))
                <a class="button btn-radius d-flex"
                   href="{{route('admin.companies.inputs.values',['input_id' => $caseInput->id, 'company_id' => $company->id])}}"
                   style="padding-bottom: 12px; padding-top: 12px; --btn-height: 48px; --btn-max-width: var(--btn-min-width)">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="width: 20px; height: 20px;"
                         fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    <span>{{__('common.edit')}}</span>
                </a>
            @endif
        </div>
    </div>
    <div class="row">
        <div class="col-4">
            <button type="button" wire:click="toggleTranslations"
                    style="background: rgb(222, 240, 241) !important; color: black !important;"
                    class="d-flex justify-content-between">
                {{__('common.case_input_edit.translations')}}
                <svg xmlns="http://www.w3.org/2000/svg"
                     style="width: 20px; height: 20px; {{$is_translations_open ? 'transform: rotate(180deg);' :''}}"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
        </div>
    </div>

    @if($is_translations_open)
        @foreach($languages as $language)
            <livewire:admin.case-input-translation
                    :case-input="$caseInput"
                    :language="$language"
                    :wire:key="$language->id . $caseInput->id"
            />
        @endforeach
    @endif
</div>
