<div>
    <div class="form-row mt-3 col-md-12 p-0">
        <div class="form-group col mb-0">
            <div class="input-group col-12 p-0">
                <textarea cols="2" wire:model="invoiceComment.value" style="resize: auto !important;"></textarea>
            </div>
        </div>
        <div style="margin-bottom: 20px;" class="col-1 d-flex justify-content-center align-items-center">
            <svg wire:click="delete()" xmlns="http://www.w3.org/2000/svg"
                    style="width: 25px; height: 25px; color: rgb(89,198,198); cursor: pointer; margin-bottom:20px !important" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
        </div>
    </div>
</div>
