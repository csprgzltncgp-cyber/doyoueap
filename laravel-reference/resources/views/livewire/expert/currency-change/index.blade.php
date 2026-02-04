@push('livewire_js')
    <script>
        @if(!$currency_change->downloaded_at)
         document.addEventListener('DOMContentLoaded', function(){
                Swal.fire(
                    '{{__('currency-change.notification-popup-3-title')}}',
                    '{{__('currency-change.notification-popup-3-body')}}',
                    'warning'
                )
            });
        @endif
    </script>
@endpush

<div>
    <div class="lis-element-div d-flex">
        <div class="list-element col-12 mt-4">
            <span class="data mr-0">
                @if($currency_change->downloaded_at && $currency_change->document) {{$currency_change->updated_at->format('Y.m.d')}} - @endif
                {{__('currency-change.type')}}
            </span>

            @if (!($currency_change->downloaded_at && $currency_change->document))
            <button
                type="button"
                wire:click="download"
                class="{{ empty($currency_change->downloaded_at) ? 'pulse-container' : '' }} row-button ml-3 btn-radius align-items-center"
            >
                <svg wire:loading.delay.remove xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="height:20px; margin-bottom: 3px; margin-right: 5px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                </svg>

                <span wire:loading.delay.remove>{{ __('currency-change.download') }}</span>

                <div wire:loading.delay>
                    <img style="width: 20px; height: 20px; margin-right:5px;" src="{{asset('assets/img/spinner_white.svg')}}" alt="spinner" >
                </div>
            </button>

            <button
                type="button"
                onclick="document.querySelector('input[type=file]').click()"
                {{ empty($currency_change->downloaded_at) ? 'disabled style=opacity:0.3;': '' }}
                class="{{ $currency_change->downloaded_at && empty($currency_change->document) ? 'pulse-container' : '' }} row-button btn-radius"
            >
                <svg wire:loading.delay.remove xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"  style="height:20px; margin-bottom: 3px; margin-right: 5px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                </svg>

                <span wire:loading.delay.remove >{{ __('currency-change.upload') }}</span>

                <div wire:loading.delay>
                    <img style="width: 20px; height: 20px; margin-right:5px;" src="{{asset('assets/img/spinner_white.svg')}}" alt="spinner" >
                </div>
            </button>
            @endif
        </div>
    </div>

    @if(!empty($currency_change->document))
        <p style="color: rgb(0,87,95)">{{__('currency-change.success')}}</p>
    @endif

    <input type="file" hidden wire:model="document">
</div>
