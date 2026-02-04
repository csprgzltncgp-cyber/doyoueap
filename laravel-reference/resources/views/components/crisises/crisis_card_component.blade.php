<div x-data="{flipped:false}"
     :class="flipped ? 'flipped' : ''"
     class="w-full h-full text-white h-72 card relative @if(optional($crisis->crisis_case)->status === \App\Enums\CrisisCaseStatus::CLOSED) text-white @else text-black @endif"
     x-show="country === {{$crisis->country_id}}"
>

    <div class="front w-full h-full py-5 px-16 flex flex-col justify-center items-center absolute  @if(optional($crisis->crisis_case)->status === \App\Enums\CrisisCaseStatus::CLOSED) bg-purple @else bg-green-light @endif">
        @if(optional($crisis->crisis_case)->status === \App\Enums\CrisisCaseStatus::CLOSED)
            <img src="{{asset('assets/img/client/crisis_card_icon_white.svg')}}" alt="crisis_card_icon" class="h-10">
        @else
            <img src="{{asset('assets/img/client/crisis_card_icon_black.svg')}}" alt="crisis_card_icon" class="h-10">
        @endif
        <h1 class=" mt-2 text-xl font-bold uppercase break-words text-center @if(optional($crisis->crisis_case)->status === \App\Enums\CrisisCaseStatus::CLOSED) text-white @else text-black @endif">
            @if(optional($crisis->crisis_case)->status === \App\Enums\CrisisCaseStatus::CLOSED)
                {{__('crisis.used')}}
            @elseif (optional($crisis->crisis_case)->status === \App\Enums\CrisisCaseStatus::PRICE_ACCEPTED)
                {{__('crisis.under_organization')}}
            @else
                {{__('crisis.available')}}
            @endif
        </h1>
        <h1 class="uppercase font-bold text-3xl @if(optional($crisis->crisis_case)->status === \App\Enums\CrisisCaseStatus::CLOSED) text-white @else text-black @endif mt-4">
            @if($crisis->free != 1)
                @if($crisis->crisis_price)
                    {{$crisis->crisis_price}}
                    <span> {{$crisis->valuta}}</span>
                @else
                    {{__('crisis.not_specified')}}
                @endif
            @else
                {{__('crisis.free')}}
            @endif
        </h1>
        <button type="button" x-on:click="flipped = !flipped"
                class="@if(optional($crisis->crisis_case)->status === \App\Enums\CrisisCaseStatus::CLOSED) text-white @else text-black @endif mt-4 transition duration-300 ease-in-out border border-black rounded-full px-10 py-2 uppercase hover:bg-black hover:bg-opacity-20
                @if(optional($crisis->crisis_case)->status === \App\Enums\CrisisCaseStatus::CLOSED) hover:bg-white border-white @else hover:bg-black border-black @endif">
            {{__('common.details')}}
        </button>
    </div>

    <div class="@if(optional($crisis->crisis_case)->status === \App\Enums\CrisisCaseStatus::CLOSED) text-white @else text-black @endif back w-full h-full p-5 flex flex-col justify-between absolute break-words relative @if(optional($crisis->crisis_case)->status === \App\Enums\CrisisCaseStatus::CLOSED) bg-purple @else bg-green-light @endif">
        <svg x-on:click="flipped = !flipped" xmlns="http://www.w3.org/2000/svg"
             class="h-6 w-6 absolute top-6 right-4 cursor-pointer" fill="none" viewBox="0 0 24 24" stroke="currentColor"
             stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
        </svg>
        <p><span class="font-bold">{{__('crisis.country')}}:</span> <span>{{optional(optional($crisis->crisis_case)->country)->name}}</span></p>
        <p><span class="font-bold">{{__('crisis.city')}}:</span> <span>{{optional(optional($crisis->crisis_case)->city)->name}}</span></p>
        <p><span class="font-bold">{{__('crisis.date')}}:</span> <span>{{optional($crisis->crisis_case)->date}}</span></p>
        <p><span class="font-bold">{{__('crisis.start_time')}}:</span> <span>{{optional($crisis->crisis_case)->start_time}}</span></p>
        <p><span class="font-bold">{{__('crisis.end_time')}}:</span> <span>{{optional($crisis->crisis_case)->end_time}}</span></p>
        <p><span class="font-bold">{{__('crisis.full_time')}}:</span> <span>
                @if(!empty(optional($crisis->crisis_case)->full_time) && optional($crisis->crisis_case)->full_time[0])
                    {{optional($crisis->crisis_case)->full_time[0]}} {{__('crisis.hour')}}
                @endif
                @if(!empty(optional($crisis->crisis_case)->full_time) && optional($crisis->crisis_case)->full_time[1])
                    {{optional($crisis->crisis_case)->full_time[1]}} {{__('crisis.minute')}}
                @endif
                                    </span></p>
        <p><span class="font-bold">{{__('crisis.expert')}}:</span> <span>{{optional(optional($crisis->crisis_case)->user)->name}}</span></p>
        <p><span class="font-bold">{{__('crisis.activity_id')}}:</span> <span>{{$crisis->activity_id}}</span></p>
    </div>

</div>
