@push('livewire_js')
    <script>
        Livewire.on('file_upload_success', function() {
            Swal.fire({
                title: '{{ __('common.msg_upload_succesfull') }}',
                text: '',
                icon: 'success',
                confirmButtonText: 'Ok'
            });
        });

        Livewire.on('file_upload_failed', function(msg) {
            Swal.fire({
                title: msg,
                text: '',
                icon: 'error',
                confirmButtonText: 'Ok'
            });
        });

        Livewire.on('file_upload_exists', function(msg) {
            Swal.fire({
                title: '{{ __('data.warning_lifeworks_upload_exists') }}',
                text: '',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Igen',
                cancelButtonText: 'Nem',
            }).then(function (result) {
                if (result.value) {
                    Swal.fire({
                        text: '{{ __('data.uploading_data') }}',
                        imageUrl: '{{asset('assets/img/spinner.svg')}}',
                        showCancelButton: false,
                        showConfirmButton: false
                    });
                    @this.set('confirmed_replace', true);
                    @this.save();
                }
            });
        });

        Livewire.on('file_upload_exists', function(msg) {
            Swal.fire({
                title: '{{ __('data.warning_lifeworks_upload_exists') }}',
                text: '',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Igen',
                cancelButtonText: 'Nem',
            }).then(function (result) {
                if (result.value) {
                    show_upload_progress();
                    @this.set('confirmed_replace', true);
                    @this.save();
                }
            });
        });

        Livewire.on('show_upload_progress', function(msg) {
            show_upload_progress();
        });

        function show_upload_progress()
        {
            Swal.fire({
                text: '{{ __('data.uploading_data') }}',
                imageUrl: '{{asset('assets/img/spinner.svg')}}',
                showCancelButton: false,
                showConfirmButton: false
            });
        }
    </script>
@endpush
<div class="d-flex w-100 justify-content-end" style="height: 20px!important;">
    <link rel="stylesheet" href="/assets/css/form.css?v={{ time() }}">
    <div class="d-flex flex-row w-100 justify-content-end align-items-center">
        <div>
            <span>{{\Carbon\Carbon::now()->year}}/</span>
            @foreach(\Carbon\CarbonPeriod::create(\Carbon\Carbon::now()->startOfYear()->format('Y-m-d'), '1 month', \Carbon\Carbon::now()->endOfYear()->format('Y-m-d')) as $date)
                <span 
                    style="@if(in_array($date->format('m'), $file_months_exists)) color:rgb(145,183,82); @else color:rgb(176, 176, 176) @endif"
                    onclick="excel_file.click();"
                    wire:click="$set('month_to_upload', {{$date->format('m')}})"
                >
                    {{$date->format('m')}}
                </span>
                @if(!$loop->last) <span>/</span> @endif
            @endforeach
        </div>

        <form wire:submit.prevent="save" class="mr-3" style="margin-top:0px!important;">
            <div class="d-none">
                <input type="file" id="excel_file" wire:model="file">
            </div>
        </form>
    </div>
</div>
