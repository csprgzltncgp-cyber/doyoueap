@props([
    'name',
    'values',
    'defaultHover'
])

@php
    $randomAge = $values->filter(function ($item){
        return (calculate_percentage($item['count'], $item['total_count'], 0) > 0);
    })->keys()->random();
@endphp

<div class="flex flex-col">
    <div class="mb-3 h-96 flex space-x-2 justify-between">
        @foreach($values as $age => $data)
            <div class="flex flex-col justify-end"
                 x-data="{onscreen:false,defaultHover:{{($defaultHover && $age == $randomAge) ? 'true' : 'false'}}, hover:false}">
                <div
                        x-intersect.once.full.margin.-120px="onscreen = true; setTimeout(()=> {defaultHover = false;}, 1200)"
                        x-on:mouseover="hover = true"
                        x-on:mouseout="hover = false; defaultHover = false;"
                        class="bg-purple rounded-full w-3 mx-auto cursor-pointer relative"
                        style="height: {{calculate_percentage($data['count'], $data['total_count']) == 0 ? 3 :calculate_percentage($data['count'], $data['total_count'])}}%;">
                    <div x-show="(hover || defaultHover) && onscreen"
                         x-cloak
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 scale-90"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-300"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-90"
                         class="absolute flex justify-center items-center z-40 w-max h-max"
                         style="top: -61px; left: -30px">
                        <img class="h-full" src="{{asset('assets/img/client/msg-bub.svg')}}" alt="msg-bub"
                             style="height: 70px">
                        <span class="text-white w-full font-bold text-lg text-center z-40 absolute left-1/2 transform -translate-x-1/2"
                              style="top: 15px">{{$age}}</span>
                    </div>
                </div>
                <span class="text-purple font-bold mt-3 text-sm">{{calculate_percentage($data['count'], $data['total_count'], 0)}}%</span>
            </div>
        @endforeach
    </div>
    <div class="bg-purple h-0.5 mb-10"></div>
    <p class="text-2xl uppercase font-bold mx-auto text-center">{{$name}}</p>
</div>
