<div x-data="{ edit: false }" class="flex flex-col justify-items-between
    @if ($volume_request->status === \App\Enums\VolumeRequestStatusEnum::PENDING ) bg-green-light @endif
    @if ($volume_request->status === \App\Enums\VolumeRequestStatusEnum::COMPLETED ) bg-purple @endif
    @if ($volume_request->status === \App\Enums\VolumeRequestStatusEnum::AUTO_COMPLETED ) bg-gradient-to-tr from-green-light  to-purple @endif
    text-white text-center px-10 py-5 items-center gap-y-3"
    :class="edit ? 'bg-none !bg-green-light' : '' ">
    <span>
        {{__('riport.volume_request')}}<br>
        {{($volume_request->volume->invoice_item->direct_invoice_data->admin_identifier) ? $volume_request->volume->invoice_item->direct_invoice_data->admin_identifier : $volume_request->volume->invoice_item->direct_invoice_data->name}}
    </span>

    @if ($volume_request->status === \App\Enums\VolumeRequestStatusEnum::PENDING )
        <input type="number" wire:model="volume_request.headcount" class="border-solid border-2 border-white bg-white bg-opacity-50 focus:ring-transparent focus:border-white text-black appearance-none">
    @else
        <input 
            type="number"
            @if ($volume_request->status === \App\Enums\VolumeRequestStatusEnum::AUTO_COMPLETED || $volume_request_closed)
                onclick="show_volume_request_closed();"
                wire:model="volume_request.headcount"
                readonly
            @else
                wire:model="volume_request.headcount" 
                value="{{$volume_request->headcount}}"
                x-on:click="edit = true"
            @endif
            :class="edit ? 'border-solid border-2 border-white bg-white bg-opacity-50 focus:ring-transparent focus:border-white' : 'border-solid border-2 border-white bg-white bg-opacity-50 text-black appearance-none' ">
    @endif

    <div x-show="!edit" class="mt-3">
        @if ($volume_request->status === \App\Enums\VolumeRequestStatusEnum::COMPLETED || $volume_request->status === \App\Enums\VolumeRequestStatusEnum::AUTO_COMPLETED)
            <svg xmlns="http://www.w3.org/2000/svg" class="w-14 -mt-1" fill="none" viewBox="0 0 24 24" stroke-width="1.0" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>   
        @else
            <button wire:click="save" class="px-9 py-2 border-solid border border-black rounded-full hover:bg-black hover:bg-opacity-20 text-black">
                {{__('common.send')}}
            </button>
        @endif
    </div>
    <div x-show="edit" class="mt-3">
        <button wire:click="save" x-on:click="edit = false" class="px-9 py-2 border-solid border border-black rounded-full hover:bg-black hover:bg-opacity-20 text-black">
            {{__('common.send')}}
        </button>
    </div>
</div>