<div>
    <div>
        <style>
            .input-group-text.dark,
            .input-group-text,
            label.checkbox-container {
                background-color: white !important;
            }

            .input-group input.dark {
                background-color: rgb(0, 87, 95) !important;
                color: white !important;
            }

            .input-group input.dark::placeholder {
                color: white !important;
            }

            .input-group-text.dark {
                background-color: rgb(0, 87, 95) !important;
                color: white !important;
            }

            .yellow-input-notification {
                background: #ffc107;
                height: 48px;
                margin-bottom: 12px;
                text-align: center;
                color: white;
                display: flex;
                justify-content: center;
                align-items: center;
            }
        </style>
        <div class="pl-4 pr-4 pt-2 pb-2 mb-3"
            style="{{ empty(optional($invoiceItem)->input) ? 'background: rgba(127, 64, 116, 0.2)' : 'background: rgba(89, 198, 198, 0.2)' }}">
            <div wire:loading.delay.remove>
                <div class="form-row mt-3 col-md-12 p-0">
                    <div class="form-group col-md-5 mb-0">
                        <div class="input-group col-12 p-0">
                            <div class="input-group-prepend">
                                <div class="input-group-text dark">
                                    <svg xmlns="http://www.w3.org/2000/svg" style="height:20px; width:20px;" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </div>
                            </div>
                            <input type="text" class="dark" wire:model="invoiceItem.name"
                                placeholder="{{ __('company-edit.invoice-item-placeholder') }}" />
                        </div>
                    </div>
                    <div class="form-group col-md-3 mb-0">
                        <div class="input-group col-12 p-0">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    Input:
                                </div>
                            </div>
                            <select wire:model="invoiceItem.input">
                                <option value="{{ null }}">{{ __('common.please-choose') }}</option>
                                @foreach ($invoiceItemTypes as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    @if ($invoiceItem->needActivityIdCheckbox())
                        <div class="form-group col-md-3 mb-0">
                            <div class="input-group col-12 p-0 mb-0">
                                <label class="checkbox-container mt-0 w-100"
                                    style="color: rgb(89,198,198); padding: 10px 0 10px 10px; border: 2px solid rgb(89,198,198) !important; font-size: 16px; margin-top: 8px;">
                                    {{ __('company-edit.activity-id-shows') }}

                                    <input type="checkbox" class="delete_later d-none"
                                        wire:model='invoiceItem.is_activity_id_shown' />
                                    <span class="checkmark d-flex justify-content-center align-items-center"
                                        style="left:auto; right: 0; height: 100%; width: 50px; border-left: 2px solid rgb(89,198,198) !important;">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="checked {{ optional($invoiceItem)->is_activity_id_shown ? '' : 'd-none' }}"
                                            style="width: 25px; height: 25px; color: white" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="unchecked {{ optional($invoiceItem)->is_activity_id_shown ? 'd-none' : '' }}"
                                            style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </span>
                                </label>
                            </div>
                        </div>
                    @endif

                    @if ($invoiceItem->isInputTypeMultiplication() || $invoiceItem->isInputTypeContractHolder())
                        <div class="form-group col-md-3 mb-0">
                            <div class="input-group col-12 p-0 mb-0">
                                <label class="checkbox-container mt-0 w-100"
                                    style="color: rgb(89,198,198); padding: 10px 0 10px 10px; border: 2px solid rgb(89,198,198) !important; font-size: 16px; margin-top: 8px;">
                                    {{ __('company-edit.shown-by-item') }}

                                    <input type="checkbox" class="delete_later d-none"
                                        wire:model='invoiceItem.shown_by_item' />
                                    <span class="checkmark d-flex justify-content-center align-items-center"
                                        style="left:auto; right: 0; height: 100%; width: 50px; border-left: 2px solid rgb(89,198,198) !important;">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="checked {{ optional($invoiceItem)->shown_by_item ? '' : 'd-none' }}"
                                            style="width: 25px; height: 25px; color: white" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="unchecked {{ optional($invoiceItem)->shown_by_item ? 'd-none' : '' }}"
                                            style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </span>
                                </label>
                            </div>
                        </div>
                    @endif


                    <div style="margin-bottom: 20px; margin-left:auto" class="col-1 d-flex flex-column justify-content-end align-items-start">
                        <svg wire:click="delete()" xmlns="http://www.w3.org/2000/svg"
                            style="width: 25px; height: 25px; color: rgb(89,198,198); cursor: pointer; margin-left:auto;"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>

                        @if (!is_null($invoiceItem->comment) || $isCommentShown)
                            <svg wire:click="$set('isCommentShown', false)" xmlns="http://www.w3.org/2000/svg"
                                class="mt-1"
                                style="width: 26px; height: 26px; color: rgb(89,198,198); cursor: pointer; margin-left:auto;"
                                viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z"
                                    clip-rule="evenodd" />
                            </svg>
                        @else
                            <svg wire:click="$set('isCommentShown', true)" xmlns="http://www.w3.org/2000/svg"
                                class="mt-1"
                                style="width: 25px; height: 25px; color: rgb(89,198,198); cursor: pointer; margin-left:auto;"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                        @endif

                        @if ($invoiceItem->isInputTypeMultiplication() || $invoiceItem->isInputTypeAmount())
                            @if (!$invoiceItem->with_timestamp)
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    wire:click="$set('invoiceItem.with_timestamp', true)" class="mt-1"
                                    style="width: 26px; height: 26px; color: rgb(89,198,198); cursor: pointer; margin-left:auto;"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    wire:click="$set('invoiceItem.with_timestamp', false)" class="mt-1"
                                    style="width: 25px; height: 25px; color: rgb(89,198,198); cursor: pointer; margin-left:auto;"
                                    viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                        clip-rule="evenodd" />
                                </svg>
                            @endif
                        @endif
                    </div>
                </div>

                @if ($invoiceItem->isInputTypeMultiplication() || $invoiceItem->isInputTypeContractHolder())
                    <h1 class="mt-0 pt-0 mb-0" style="font-size: 14px; color:#59c6c6">
                        {{ __('company-edit.set-multiplication') }}</h1>
                    <div class="form-row col-md-12 p-0 mb-0 mt-1">
                        <div class="form-group col-md-5 mb-0">
                            <div class="input-group col-12 p-0">
                                <div class="input-group-prepend">
                                    <div class="input-group-text"
                                        @if (in_array('invoiceItem.volume', $customErrors)) style="border-color: red !important;" @endif>
                                        <svg xmlns="http://www.w3.org/2000/svg" style="height:20px; width:20px;"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                            stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </div>
                                </div>
                                <input type="text" wire:model="volume.name"
                                    placeholder="{{ __('company-edit.volume-placeholder') }}"
                                    @if (in_array('invoiceItem.volume', $customErrors)) style="border-color: red !important;" @endif />
                            </div>
                        </div>

                        @if (optional($invoiceItem->volume)->is_changing)
                            <div class="form-group col-md-3 mb-0">
                                <div class="yellow-input-notification">
                                    {{ __('company-edit.volume_changing') }}
                                </div>
                            </div>
                        @else
                            <div class="form-group col-md-3 mb-0">
                                <input type="text" wire:model.lazy="volume.value"
                                    style="
                                        @if (in_array('invoiceItem.volume_value', $customErrors)) border-color: red !important; @endif

                                        @if ($invoiceItem->isInputTypeContractHolder()) opacity: 0.5; @endif
                                    "
                                    @if ($invoiceItem->isInputTypeContractHolder()) disabled placeholder="Automatikus kitöltés" @endif />
                            </div>
                        @endif

                        @if ($invoiceItem->isInputTypeMultiplication())
                            <div class="form-group col-md-3 mb-0">
                                <div class="input-group col-12 p-0 mb-0">
                                    <label class="checkbox-container mt-0 w-100"
                                        style="color: rgb(89,198,198); padding: 10px 0 10px 10px; border: 2px solid rgb(89,198,198) !important; font-size: 16px; margin-top: 8px;">
                                        {{ __('company-edit.changing') }}

                                        <input type="checkbox" class="delete_later d-none"
                                            wire:model='volume.is_changing' />
                                        <span class="checkmark d-flex justify-content-center align-items-center"
                                            style="left:auto; right: 0; height: 100%; width: 50px; border-left: 2px solid rgb(89,198,198) !important;">
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                class="checked {{ optional($volume)->is_changing ? '' : 'd-none' }}"
                                                style="width: 25px; height: 25px; color: white" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                class="unchecked {{ optional($volume)->is_changing ? 'd-none' : '' }}"
                                                style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </span>
                                    </label>
                                </div>
                            </div>
                        @endif
                    </div>

                      @if (optional($invoiceItem->volume)->is_changing)
                        <div class="form-row col-md-12 mt-0 mb-0 p-0">
                            <div class="form-group col-md-5 mb-0">
                                <div class="input-group col-12 p-0">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text" style="@if (in_array('invoiceItem.data_request_email', $customErrors)) border-color: red !important; @endif">
                                            {{ __('company-edit.data_request_email') }}:
                                        </div>
                                    </div>
                                    <input type="text" wire:model="invoiceItem.data_request_email" style="@if (in_array('invoiceItem.data_request_email', $customErrors)) border-color: red !important; @endif"/>
                                </div>
                            </div>
                            <div class="form-group col-md-5 mb-0">
                                <div class="input-group col-12 p-0">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            {{ __('company-edit.data_request_salutations') }}:
                                        </div>
                                    </div>
                                    <input type="text" wire:model="invoiceItem.data_request_salutation"/>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="form-row col-md-12 p-0 mt-0">
                        <div class="form-group col-md-5 mb-0">
                            <div class="input-group col-12 p-0">
                                <div class="input-group-prepend">
                                    <div class="input-group-text"
                                        @if (in_array('invoiceItem.amount', $customErrors)) style="border-color: red !important;" @endif>
                                        <svg xmlns="http://www.w3.org/2000/svg" style="height:20px; width:20px;"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                            stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </div>
                                </div>
                                <input type="text" wire:model="amount.name"
                                    placeholder="{{ __('company-edit.amount-placeholder') }}"
                                    @if (in_array('invoiceItem.amount', $customErrors)) style="border-color: red !important;" @endif />
                            </div>
                        </div>
                        <div class="form-group col-md-3 mb-0">
                            <div class="input-group col-12 p-0">
                                <div class="input-group-prepend">
                                    <div class="input-group-text"
                                        @if (in_array('invoiceItem.amount_value', $customErrors)) style="border-color: red !important;" @endif>
                                        {{ strtoupper($currency) }}:
                                    </div>
                                </div>
                                <input type="text" wire:model.lazy="amount.value"
                                    @if (in_array('invoiceItem.amount_value', $customErrors)) style="border-color: red !important;" @endif />
                            </div>
                        </div>
                    </div>
                @endif

                @if ($invoiceItem->isInputTypeAmount())
                    <h1 class="mt-0 pt-0 mb-0" style="font-size: 14px; color:#59c6c6">
                        {{ __('company-edit.set-amount') }}</h1>
                    <div class="form-row col-md-12 p-0 mt-1">
                        @if (optional($amount)->is_changing)
                            <div class="form-group col-md-5 mb-0">
                                <div class="yellow-input-notification">
                                    {{ __('company-edit.amount_changing') }}
                                </div>
                            </div>
                        @else
                            <div class="form-group col-md-5 mb-0">
                                <div class="input-group col-12 p-0">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            {{ strtoupper($currency) }}:
                                        </div>
                                    </div>
                                    <input type="text" wire:model.lazy="amount.value"
                                        placeholder="{{ __('company-edit.amount-placeholder') }}" />
                                </div>
                            </div>
                        @endif


                        <div class="form-group col-md-3 mb-0">
                            <div class="input-group col-12 p-0 mb-0">
                                <label class="checkbox-container mt-0 w-100"
                                    style="color: rgb(89,198,198); padding: 10px 0 10px 10px; border: 2px solid rgb(89,198,198) !important; font-size: 16px; margin-top: 8px;">
                                    {{ __('company-edit.changing') }}

                                    <input type="checkbox" class="delete_later d-none"
                                        wire:model='amount.is_changing' />
                                    <span class="checkmark d-flex justify-content-center align-items-center"
                                        style="left:auto; right: 0; height: 100%; width: 50px; border-left: 2px solid rgb(89,198,198) !important;">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="checked {{ optional($amount)->is_changing ? '' : 'd-none' }}"
                                            style="width: 25px; height: 25px; color: white" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="unchecked {{ optional($amount)->is_changing ? 'd-none' : '' }}"
                                            style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>

                    @if (optional($amount)->is_changing)
                        <div class="form-row col-md-12 mt-0 mb-0 p-0">
                            <div class="form-group col-md-5 mb-0">
                                <div class="input-group col-12 p-0">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            Email cím adatbekéréshez:
                                        </div>
                                    </div>
                                    <input type="text" style="@if (in_array('invoiceItem.volume_value', $customErrors)) border-color: red !important; @endif"/>
                                </div>
                            </div>
                            <div class="form-group col-md-5 mb-0">
                                <div class="input-group col-12 p-0">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            Megszólítás:
                                        </div>
                                    </div>
                                    <input type="text" style="@if (in_array('invoiceItem.volume_value', $customErrors)) border-color: red !important; @endif"/>
                                </div>
                            </div>
                        </div>
                    @endif
                @endif

                @if (!is_null($invoiceItem->comment) || $isCommentShown)
                    <div class="form-row col-md-12 p-0 ">
                        <div class="input-group col-11 pr-0">
                            <textarea cols="2" wire:model="invoiceItem.comment" style="resize: auto !important; background: white;"></textarea>
                        </div>
                    </div>
                @endif
            </div>

            <div wire:loading.delay style="width: 100%; text-align: center;">
                <img style="width: 30px; height: 30px" src="{{ asset('assets/img/spinner.svg') }}" alt="spinner">
            </div>
        </div>
    </div>
</div>
