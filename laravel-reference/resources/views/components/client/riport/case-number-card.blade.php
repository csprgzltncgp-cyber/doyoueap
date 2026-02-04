@props([
    'infoText' => null,
    'allNumber' => null,
    'cardInfoText' => null,
    'cardInfoTextTop' => null,
    'defaultCumulateHover' => false,
    'defaultInfoHover' => false,
    'id',
    'quarter',
    'currentNumber',
    'text',
    'totalView'
])

<div class="flex justify-between items-center pb-3 pt-4 px-10 bg-white bg-opacity-80 rounded-lg shadow"
     x-data="{current:{{$currentNumber}}, all:{{$allNumber ?? 0}}, is_current:false}">
    <div class="flex flex-col sm:flex-row">
                <span class="text-2xl font-bold mr-5 uppercase">
                    {{$text}}:
                </span>
        @if(!empty($allNumber) && $quarter != 1)
            <div class="flex items-center relative" x-data="{onscreen:false,defaultHover:{{($defaultCumulateHover) ? 'true' : 'false'}}, hover:false}">
                    <span class="uppercase text-2xl font-bold mr-3 text-purple"
                          :class="is_current ? 'current_cases' : 'cursor-pointer hover:text-yellow transition-all duration-300'"
                          x-on:mouseover="hover = true"
                          x-on:mouseout="hover = false; defaultHover = false;"
                          x-intersect.once.full.margin.-120px="onscreen = true; setTimeout(()=> {defaultHover = false;}, 1200)"
                    >
                    {{__('riport.cumulate')}}</span>
                <label for="toggle-example-checked-{{$id}}" class="flex items-center cursor-pointer relative">
                    <input type="checkbox" id="toggle-example-checked-{{$id}}" class="sr-only" checked
                           x-on:click="is_current = !is_current">
                    <div class="toggle-bg bg-purple bg-opacity-20 border-2 border-purple border-opacity-0 h-6 w-11 rounded-full"></div>
                </label>
                <div x-cloak
                        x-show="(hover || defaultHover) && !is_current && onscreen"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 scale-90"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-300"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-90"
                        class="absolute flex justify-center items-center z-50 w-max h-max right-0"
                        style="top: -77px; left: 90px">
                    <img class="h-full" src="{{asset('assets/img/client/msg-bub-small.svg')}}" alt="msg-bub"
                            style="height: 80px">
                        <span class="text-white w-full text-center text-lg z-50 absolute font-bold left-1/2 transform -translate-x-1/2 px-5" style="padding-bottom: 10px;">
                            {{$infoText}}
                        </span>
                </div>
            </div>
        @endif
    </div>
    <div class="flex items-center justify-center relative" x-data="{hover:false, onscreen:false,defaultHover:{{($defaultInfoHover) ? 'true' : 'false'}},}">
            <span class="card-text font-bold m-0 p-0 mt-1 @if($cardInfoText) cursor-pointer @else text-black @endif"
                    @if($cardInfoText) :class="hover ? 'text-yellow' : 'text-black'" @endif
                    style="line-height: 1; font-size: 30px;"
                    x-on:mouseover="hover = true"
                    x-on:mouseover="hover = true"
                    x-on:mouseout="hover = false; defaultHover = false;"
                    x-intersect.once.full.margin.-120px="onscreen = true; setTimeout(()=> {defaultHover = false;}, 1200)"
                    @if(!empty($allNumber))
                        x-text="is_current ? current : all"
                    @else
                        x-text="current"
                    @endif
                    >
            </span>
            @if($cardInfoText)
                <div x-cloak
                    x-show="(hover || defaultHover) && onscreen"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 scale-90"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-300"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-90"
                    class="absolute flex justify-center items-center z-20 w-max h-max right-0"
                    style="top: -158px; right: -220px">
                    <img class="h-full" src="{{asset('assets/img/client/msg-bub-big.svg')}}" alt="msg-bub"
                            style="height: 163px">
                    <span class="text-white w-full text-center text-sm z-20 absolute font-bold left-1/2 transform -translate-x-1/2 px-5"
                            style="padding-bottom: 22px !important;">{!! nl2br(e($cardInfoText)) !!}</span>
                </div>
            @endif
    </div>
</div>
