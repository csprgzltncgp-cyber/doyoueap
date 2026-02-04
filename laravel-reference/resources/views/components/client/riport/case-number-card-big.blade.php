@props([
    'value' => null,
    'text' => null,
    'info' => null,
])

<div x-data="{ tooltip: false }" class="flex justify-between items-center px-10 py-5 bg-purple text-white rounded-lg shadow" x-data="{hover: true, onscreen:false}">
    <div class="w-2/3 uppercase font-bold flex items-center h-full " style="font-size: 25px;">
        <span class="leading-none">{{$text}}</span>
    </div>
    <div class="w-2/3 uppercase font-bold flex justify-end items-center h-full relative" style="font-size: 100px;">
        <span class="leading-none mt-3 hover:text-yellow cursor-pointer transition-all duration-300"
            x-intersect.once.full.margin.-120px="tooltip = true; setTimeout(()=> {tooltip = false;}, 1200)"
            x-on:mouseover="tooltip = true;"
            x-on:mouseout="tooltip = false;"
        >{{$value}}</span>
        <div x-cloak
                x-show="tooltip"
                x-transition:enter="transition ease-in duration-300"
                x-transition:enter-start="opacity-0 scale-90"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-out duration-300"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-90"
                class="absolute flex justify-center items-center z-50 w-max h-max right-0"
                style="top: -140px; right: -220px">
            <img class="h-full" src="{{asset('assets/img/client/msg-bub-big.svg')}}" alt="msg-bub"
                    style="height: 163px">
            <span class="text-white w-full text-center z-50 absolute font-bold left-1/2 transform -translate-x-1/2 px-5"
                    style="padding-bottom: 18px; font-size: 11px">{{$info}} <br> {{__('riport.cumulated_info')}}</span>
        </div>
    </div>
</div>
