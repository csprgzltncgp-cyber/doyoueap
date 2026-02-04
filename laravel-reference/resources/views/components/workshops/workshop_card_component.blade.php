<div x-data="{flipped:false}"
     x-cloak
     :class="flipped ? 'flipped' : ''"
     class="w-full h-full text-white h-72 card relative @if(optional($workshop->workshop_case)->status == \App\Enums\WorkshopCaseStatus::CLOSED) text-white @else text-black @endif"
     x-show="country === {{$workshop->country_id}}"
>

    <div class="front w-full h-full py-5 px-16 flex flex-col justify-center items-center absolute  @if(optional($workshop->workshop_case)->status === \App\Enums\WorkshopCaseStatus::CLOSED) bg-purple @else bg-green-light @endif">
        @if(optional($workshop->workshop_case)->status == \App\Enums\WorkshopCaseStatus::CLOSED)
            <img src="{{asset('assets/img/client/workshop_card_icon_white.svg')}}" alt="workshop_card_icon"
                 class="h-10">
        @else
            <img src="{{asset('assets/img/client/workshop_card_icon_black.svg')}}" alt="workshop_card_icon"
                 class="h-10">
        @endif
        <h1 class="mt-2 text-xl font-bold uppercase break-words text-center  @if(optional($workshop->workshop_case)->status === \App\Enums\WorkshopCaseStatus::CLOSED) text-white @else text-black @endif">
            @if(optional($workshop->workshop_case)->status === \App\Enums\WorkshopCaseStatus::CLOSED)
                {{__('workshop.used')}}
            @elseif (optional($workshop->workshop_case)->status == \App\Enums\WorkshopCaseStatus::PRICE_ACCEPTED)
                {{__('workshop.under_organization')}}
            @else
                {{__('workshop.available')}}
            @endif
        </h1>
        <h1 class="uppercase font-bold text-3xl mt-4 text-center  @if(optional($workshop->workshop_case)->status === \App\Enums\WorkshopCaseStatus::CLOSED) text-white @else text-black @endif">
            @if($workshop->free && $workshop->gift)
                {{__('workshop.gift')}}
            @else
                @if($workshop->free)
                    {{__('workshop.free')}}
                @else
                    @if($workshop->workshop_price)
                        {{$workshop->workshop_price}}
                        <span> {{$workshop->valuta}}</span>
                    @else
                        {{__('workshop.not_specified')}}
                    @endif
                @endif
            @endif
        </h1>
        <button type="button" x-on:click="flipped = !flipped"
                class="@if(optional($workshop->workshop_case)->status === \App\Enums\WorkshopCaseStatus::CLOSED) text-white @else text-black @endif mt-4 transition duration-300 ease-in-out border border-black rounded-full px-10 py-2 uppercase  hover:bg-opacity-20
                 @if(optional($workshop->workshop_case)->status === \App\Enums\WorkshopCaseStatus::CLOSED) hover:bg-white border-white @else hover:bg-black border-black @endif">
            {{__('common.details')}}
        </button>
    </div>

    <div class="back @if(optional($workshop->workshop_case)->status === \App\Enums\WorkshopCaseStatus::CLOSED) text-white @else text-black @endif  w-full h-full p-5 flex flex-col justify-between absolute break-words relative @if(optional($workshop->workshop_case)->status == \App\Enums\WorkshopCaseStatus::CLOSED) bg-purple @else bg-green-light @endif">
        <svg x-on:click="flipped = !flipped" xmlns="http://www.w3.org/2000/svg"
             class="h-6 w-6 absolute top-6 right-4 cursor-pointer" fill="none" viewBox="0 0 24 24" stroke="currentColor"
             stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
        </svg>
        <p><span class="font-bold">{{__('workshop.country')}}:</span> <span>{{optional(optional($workshop->workshop_case))->name}}</span></p>
        <p><span class="font-bold">{{__('workshop.city')}}:</span> <span>{{optional(optional($workshop->workshop_case)->city)->name}}</span></p>
        <p><span class="font-bold">{{__('workshop.date')}}:</span> <span>{{optional($workshop->workshop_case)->date}}</span></p>
        <p><span class="font-bold">{{__('workshop.start_time')}}:</span> <span>{{optional($workshop->workshop_case)->start_time}}</span></p>
        <p><span class="font-bold">{{__('workshop.end_time')}}:</span> <span>{{optional($workshop->workshop_case)->end_time}}</span></p>
        <p><span class="font-bold">{{__('workshop.full_time')}}:</span> <span>
                @if(!empty(optional($workshop->workshop_case)->full_time) && optional($workshop->workshop_case)->full_time[0])
                    {{optional($workshop->workshop_case)->full_time[0]}} {{__('workshop.hour')}}
                @endif
                @if(!empty(optional($workshop->workshop_case)->full_time) && optional($workshop->workshop_case)->full_time[1])
                    {{optional($workshop->workshop_case)->full_time[1]}} {{__('workshop.minute')}}
                @endif
            </span>
        </p>
        <p><span class="font-bold">{{__('workshop.workshop_theme')}}:</span> <span>{{\Illuminate\Support\Str::limit(optional($workshop->workshop_case)->topic, 20)}}</span></p>
        <p><span class="font-bold">{{__('workshop.expert')}}:</span> <span>{{optional(optional($workshop->workshop_case)->user)->name}}</span></p>
        <p><span class="font-bold">{{__('workshop.activity_id')}}:</span> <span>{{$workshop->activity_id}}</span></p>
        @if(optional($workshop->workshop_case)->number_of_participants)
            <p><span class="font-bold">{{__('workshop.number_of_participants')}}:</span>
                <span>{{optional($workshop->workshop_case)->number_of_participants}}</span></p>
        @endif
    </div>

</div>
