<div>
    {{-- <div wire:loading.delay.remove> --}}
        <div>
            <h1 style="font-size: 18px; color:black">{{__('company-edit.comments')}}:</h1>

            @foreach($invoiceComments as $invoiceComment)
                @livewire('admin.direct-invoicing.comment.show', ['invoiceComment' => $invoiceComment], key('invoice-comment-' . uniqid()))
            @endforeach

            <div class="form-row mt-2">
                <div class="form-group col-md-3 mb-0">
                    <button type="button" style="padding-bottom: 14px; padding-left:0px;" class="text-center btn-radius" wire:click="addInvoiceComment">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="width:20px; height:20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        <span style="margin-top: 3px;">
                            {{__('company-edit.add')}}
                        </span>
                    </button>
                </div>
            </div>
        </div>
    {{-- </div> --}}

    {{-- <div wire:loading.delay>
        <img style="width: 40px; height: 40px" src="{{asset('assets/img/spinner.svg')}}" alt="spinner" >
    </div> --}}
</div>
