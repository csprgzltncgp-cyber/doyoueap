<div>
    {{-- <div wire:loading.delay.remove> --}}
        @foreach($invoiceNotes as $invoiceNote)
            @livewire('admin.direct-invoicing.invoice-note.show', ['invoiceNote' => $invoiceNote], key('invoice-note-' . uniqid()))
        @endforeach
    {{-- </div> --}}

    {{-- <div wire:loading.delay>
        <img style="width: 40px; height: 40px" src="{{asset('assets/img/spinner.svg')}}" alt="spinner" >
    </div> --}}
</div>
