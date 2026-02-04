@php
    $id = uniqid();
@endphp

<div class="flex justify-between items-center pb-3 pt-4 px-10 bg-white bg-opacity-80 rounded-lg shadow"
     x-data="{current:{{$value}}, all:{{$allValue ?? 0}}, is_current:false}">
    <div class="flex flex-col sm:flex-row">
                <span class="text-2xl font-bold mr-5 uppercase">
                    {{$text}}:
                </span>
        @if(!empty($allValue))
            <div class="flex items-center relative">
                    <span class="uppercase text-2xl font-bold mr-3 text-purple"
                          :class="is_current ? 'current_cases' : ''"
                    >
                    {{__('riport.cumulate')}}</span>
                <label for="toggle-example-checked-{{$id}}" class="flex items-center cursor-pointer relative">
                    <input type="checkbox" id="toggle-example-checked-{{$id}}" class="sr-only" checked
                           x-on:click="is_current = !is_current">
                    <div class="toggle-bg bg-purple bg-opacity-20 border-2 border-purple border-opacity-0 h-6 w-11 rounded-full"></div>
                </label>
            </div>
        @endif
    </div>
    <div class="flex items-center justify-center relative" x-data="{hover:false}">
            <span class="card-text font-bold m-0 p-0 mt-1 text-black"
                    style="line-height: 1; font-size: 30px;"
                    @if(!empty($allValue))
                        x-text="is_current ? current : all"
                    @else
                        x-text="current"
                    @endif
                    >
            </span>
    </div>

    @if($valueId && $allValueId)
        <input type="hidden" value="{{$value}}" id="{{$valueId}}">
        <input type="hidden" value="{{$allValue}}" id="{{$allValueId}}">
    @endif
</div>
