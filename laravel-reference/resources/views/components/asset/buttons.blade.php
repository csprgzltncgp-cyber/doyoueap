

<div class="d-flex flex-row">
    @if (!$waste)
        <div class="form-group mb-0">
            <button type="button"
            wire:click='@if (!$storage)trigger_storage_popup() @else setItemId({{ $key }}) @endif'
            data-toggle="modal" 
            data-target="@if($storage) #forward_modal @endif"
            class="d-flex btn-radius" style="--btn-min-width:auto; --btn-margin-right: 0px; height: 48px!important;">
                <svg class="fuction-btn mr-2 mb-1"
                    xmlns="http://www.w3.org/2000/svg"
                    style="width: 20px; height:20px; color: white; cursor: pointer;"
                    fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 8.688c0-.864.933-1.405 1.683-.977l7.108 4.062a1.125 1.125 0 010 1.953l-7.108 4.062A1.125 1.125 0 013 16.81V8.688zM12.75 8.688c0-.864.933-1.405 1.683-.977l7.108 4.062a1.125 1.125 0 010 1.953l-7.108 4.062a1.125 1.125 0 01-1.683-.977V8.688z" />
                </svg>
                <span>
                    @if ($storage)
                        {{__('asset.forward')}}
                    @else
                        {{__('asset.to_storage')}}
                    @endif
                </span>
            </button>
        </div>
        <div class="form-group mb-0 ml-2">
            <button type="button" data-toggle="modal" 
            data-target="@if($storage) #discard_modal @else #discard_modal_{{ $key }} @endif" 
            wire:click="@if($storage) setItemId({{ $key }}) @endif"
            type="button" class="d-flex btn-radius" style="--btn-min-width:auto; --btn-margin-right: 0px; height: 48px!important;">
                <svg class="fuction-btn mr-2 mb-1"
                    xmlns="http://www.w3.org/2000/svg"
                    style="width: 20px; height:20px; color: white; cursor: pointer;"
                    fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                    d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m6 4.125l2.25 2.25m0 0l2.25 2.25M12 13.875l2.25-2.25M12 13.875l-2.25 2.25M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                </svg>
                <span>{{__('asset.discard')}}</span>
            </button>
        </div>
    @endif
    <div class="form-group mb-0 ml-2">
        <button type="button" @if($waste) onclick="Livewire.emit('trigger_delete', {{ $key }})" @else wire:click="emit_delete_popup({{$key}})"  @endif  class="d-flex btn-radius" style="--btn-min-width:auto; --btn-margin-right: 0px; height: 48px!important;">
            <svg class="fuction-btn mr-2 mb-1"
                xmlns="http://www.w3.org/2000/svg"
                style="width: 20px; height:20px; color: white; cursor: pointer;"
                fill="none" viewBox="0 0 24 24"
                stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
            {{__('common.delete')}}
        </button>
    </div>
</div>
