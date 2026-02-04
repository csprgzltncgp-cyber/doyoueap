<div class="mb-3" style="height: 48px;">
    <style>
        .country-checked {
            background-color:rgb(89,198,198)!important;
        }

        .checkbox {
            position: absolute;
            top: 0;
            left: 0;
            height: 25px;
            width: 25px;
            background-color: #eee;
        }
    </style>
    <div class="form-row">
        <div class="form-group col-md-3 mb-0">
            <div class="input-group col-12 p-0 mb-0">
                <label class="checkbox-container mt-0 w-100"
                    style="color: rgb(89,198,198); padding: 10px 0 10px 10px; border: 2px solid rgb(89,198,198) !important; font-size: 16px; margin-top: 8px;">
                    {{$country->name}}
                    <input type="checkbox" wire:click="toggle_video_chat()" class="delete_later d-none">
                    <span class="checkbox @if($checked) country-checked @endif d-flex justify-content-center align-items-center"
                        style="left:auto; right: 0; height: 100%; width: 50px; border-left: 2px solid rgb(89,198,198) !important;">
                        @if ($checked)
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="checked"
                                style="width: 25px; height: 25px; color: white" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="unchecked"
                                style="width: 20px; height: 20px;" fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        @endif
                    </span>
                </label>
            </div>
        </div>
    </div>
</div>
