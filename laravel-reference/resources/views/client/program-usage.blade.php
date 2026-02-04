@extends('layout.client.master', ['bg' =>'program-usage'])

@section('content')
    <div class="relative">
        <x-client.riport.connected-companies
            :connectedCompanies="$connected_companies"
            :currentCompany="$company"
            route="client.program_usage"
        />

        @if(Auth::user()->all_country)
            <div class="flex flex-wrap justify-center py-2 space-x-5 text-white uppercase bg-green-light">
                @foreach ($company->countries as $selectable_country)
                        <a href="{{route('client.program_usage', ['country' => $selectable_country->id])}}" class="cursor-pointer @if($country->id == $selectable_country->id) underline @endif">
                            {{$selectable_country->name}}
                        </a>
                @endforeach
            </div>
        @endif

        <div class="bg-purple bg-opacity-80 py-16 flex justify-center items-center">
            <div class="w-4/5 relative">
                <div class="h-2.5 bg-white rounded-lg w-full"></div>
                <div class="w-full h-full absolute top-0 items-start grid grid-cols-6">
                    <a
                        href="{{route('client.program_usage', ['country' => $country->id, 'year' => 2022])}}"
                        class="flex flex-col items-center justify-between -mt-5 group relative"
                         x-data="{clicked: false}" x-on:click="clicked = true"
                    >
                        <div class="h-8 w-8 rounded-full bg-white mb-2 mt-2.5  flex justify-center items-center relative group"">
                            <div class="w-6 h-6 rounded-full {{$year == 2022 ? 'bg-purple' : 'bg-white'}} border border-white"></div> <div class="w-6 h-6 rounded-full bg-purple absolute top-1 opacity-0 group-hover:opacity-50 text-white transition-all duration-300 flex justify-center items-center border border-white">
                                <svg x-show="clicked" class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                        </div>
                        <p class="text-white">2022</p>
                    </a>

                    <a
                        href="{{route('client.program_usage', ['country' => $country->id, 'year' => 2023])}}"
                        class="flex flex-col items-center justify-between -mt-5 group relative"
                        x-data="{clicked: false}" x-on:click="clicked = true"
                    >
                        <div class="h-8 w-8 rounded-full bg-white mb-2 mt-2.5  flex justify-center items-center relative group"">
                            <div class="w-6 h-6 rounded-full {{$year == 2023 ? 'bg-purple' : 'bg-white'}} border border-white"></div>
                            <div class="w-6 h-6 rounded-full bg-purple absolute top-1 opacity-0 group-hover:opacity-50 text-white transition-all duration-300 flex justify-center items-center border border-white">
                                <svg x-show="clicked" class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                        </div>
                        <p class="text-white">2023</p>
                    </a>

                    <a
                        href="{{route('client.program_usage', ['country' => $country->id, 'year' => 2024])}}"
                        class="flex flex-col items-center justify-between -mt-5 group relative"
                        x-data="{clicked: false}" x-on:click="clicked = true"
                    >
                        <div class="h-8 w-8 rounded-full bg-white mb-2 mt-2.5  flex justify-center items-center relative group">
                            <div class="w-6 h-6 rounded-full {{$year == 2024 ? 'bg-purple' : 'bg-white'}} border border-white"></div>
                            <div class="w-6 h-6 rounded-full bg-purple absolute top-1 opacity-0 group-hover:opacity-50 text-white transition-all duration-300 flex justify-center items-center border border-white">
                                <svg x-show="clicked" class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                        </div>
                        <p class="text-white">2024</p>
                    </a>

                    <a
                        href="{{route('client.program_usage', ['country' => $country->id, 'year' => 2025])}}"
                        class="flex flex-col items-center justify-between -mt-5 group relative"
                        x-data="{clicked: false}" x-on:click="clicked = true"
                    >
                        <div class="h-8 w-8 rounded-full bg-white mb-2 mt-2.5  flex justify-center items-center relative group">
                            <div class="w-6 h-6 rounded-full {{$year == 2025 ? 'bg-purple' : 'bg-white'}} border border-white"></div>
                            <div class="w-6 h-6 rounded-full bg-purple absolute top-1 opacity-0 group-hover:opacity-50 text-white transition-all duration-300 flex justify-center items-center border border-white">
                                <svg x-show="clicked" class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                        </div>
                        <p class="text-white">2025</p>
                    </a>

                    <div class="flex flex-col items-center justify-between -mt-5 group">
                        <div class="h-8 w-8 rounded-full bg-white mb-2 mt-2.5 flex justify-center items-center">
                            <div class="w-6 h-6 rounded-full bg-white border border-white"></div>
                        </div>
                        <p class="text-white opacity-30">2026</p>
                    </div>

                    <div class="flex flex-col items-center justify-between -mt-5 group">
                        <div class="h-8 w-8 rounded-full bg-white mb-2 mt-2.5 flex justify-center items-center">
                            <div class="w-6 h-6 rounded-full bg-white border border-white"></div>
                        </div>
                        <p class="text-white opacity-30">2027</p>
                    </div>
                </div>
            </div>
        </div>

        @if(!empty($calculated_records[$country->id]))
            <div class="space-y-8 pb-16 pt-2 mb-10">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 w-full">
                    <div class="bg-white bg-opacity-90 flex flex-col items-center justify-between w-full h-full p-16 mx-auto space-y-10">
                        <h1 class="text-green-light text-xl uppercase text-center font-bold">{{__('program-usage.usage')}}</h1>
                        <img src="{{asset('assets/img/client/program-usage/usage.svg')}}" class="gauge-pointer h-40" style="transform-origin: center 85%;" />
                        <div class="w-full bg-purple rounded-xl flex items-center justify-center py-8 relative">
                            <h1 class="text-white text-3xl text-center">{{$calculated_records[$country->id]['usage']}}%</h1>
                            @if($calculated_records[$country->id]['show_badge'])
                                <img alt="badge" src="{{asset('assets/img/client/program-usage/badge_' . app()->getLocale() . '.svg')}}" class="h-24 absolute rotate-12" style="top: -50px; right: -50px;">
                            @endif
                        </div>
                    </div>

                    <div class="bg-white bg-opacity-90 flex flex-col items-center justify-between w-full h-full p-16 mx-auto space-y-10" x-data="{
                        titleHover: false,
                        numberHover: false,
                        defaultHover: false
                    }"
                    x-intersect.once="setTimeout(()=> {defaultHover = true;}, 100); setTimeout(()=> {defaultHover = false;}, 1200)"
                    >
                        <div class="relative cursor-pointer"
                            x-on:mouseover="titleHover = true"
                            x-on:mouseout="titleHover = false;"
                        >
                            <h1 class="text-green-light text-xl uppercase text-center font-bold">{{__('program-usage.usage_global')}}</h1>
                                <div
                                    x-show="(titleHover || defaultHover)"
                                    x-cloak
                                x-transition:enter="transition ease-out duration-300"
                                    x-transition:enter-start="opacity-0 scale-90"
                                    x-transition:enter-end="opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-300"
                                    x-transition:leave-start="opacity-100 scale-100"
                                    x-transition:leave-end="opacity-0 scale-90"
                                    class="absolute flex justify-center items-center z-50 w-max h-max right-0"
                                    style="top: -165px; left: 165px"
                                >
                                    <img class="h-full" src="{{asset('assets/img/client/msg-bub-small.svg')}}" alt="msg-bub" style="height: 170px">
                                    <span class="text-white w-full text-center text-lg z-50 absolute font-bold left-1/2 transform -translate-x-1/2 px-5" style="padding-bottom: 20px;">
                                        {{__('program-usage.buble_title')}}
                                    </span>
                            </div>
                        </div>
                        <img src="{{asset('assets/img/client/program-usage/pointer.svg')}}" class="gauge-pointer h-40" style="transform-origin: center 85%;" />
                        <div class="w-full bg-purple rounded-xl flex items-center justify-center py-8 relative cursor-pointer"
                            x-on:mouseover="numberHover = true"
                            x-on:mouseout="numberHover = false;"
                        >
                            @if($calculated_records[$country->id]['show_badge'])
                                <img alt="badge" src="{{asset('assets/img/client/program-usage/badge_' . app()->getLocale() . '.svg')}}" class="h-24 absolute rotate-12" style="top: -50px; right: -50px;">
                            @endif

                            @if($calculated_records[$country->id]['usage_global'] < 5)
                                <h1 class="text-white text-3xl text-center">{{$calculated_records[$country->id]['usage_global'] * 100}}%</h1>
                            @else
                                <h1 class="text-white text-3xl text-center"><span class="italic">x</span> {{$calculated_records[$country->id]['usage_global']}}</h1>
                            @endif

                            <div
                                x-show="(numberHover || defaultHover)"
                                x-cloak
                                x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0 scale-90"
                                x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-300"
                                x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-90"
                                class="absolute flex justify-center items-center z-50 w-max h-max right-0"
                                style="top: -165px; left: 165px"
                            >
                                <img class="h-full" src="{{asset('assets/img/client/msg-bub-small.svg')}}" alt="msg-bub" style="height: 170px">
                                <span class="text-white w-full text-center text-lg z-50 absolute font-bold left-1/2 transform -translate-x-1/2 px-5" style="padding-bottom: 20px;">
                                    @if($calculated_records[$country->id]['usage_global'] < 5)
                                        {{__('program-usage.buble_number_percentage')}}
                                    @else
                                        {{__('program-usage.buble_number_multiply')}}
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white bg-opacity-90 flex flex-col items-center justify-between w-full h-full p-16 mx-auto space-y-10">
                        <h1 class="text-green-light text-xl uppercase text-center font-bold">{{__('program-usage.best_usage_month')}}</h1>
                        <img src="{{asset('assets/img/client/program-usage/months.svg')}}" class="gauge-pointer h-40" style="transform-origin: center 85%;" />
                        <div class="w-full bg-purple rounded-xl flex items-center justify-center py-8">
                            <h1 class="text-white text-3xl text-center">{{ucfirst($calculated_records[$country->id]['best_usage_month'])}}</h1>
                        </div>
                    </div>

                    <div class="bg-white bg-opacity-90 flex flex-col items-center justify-between w-full h-full p-16 mx-auto space-y-10">
                        <h1 class="text-green-light text-xl uppercase text-center font-bold">{{__('program-usage.problem_type')}}</h1>
                        <img src="{{$calculated_records[$country->id]['problem_type']['icon']}}" class="gauge-pointer h-40" style="transform-origin: center 85%;" />
                        <div class="w-full bg-purple rounded-xl flex items-center justify-center py-8">
                            <h1 class="text-white text-3xl text-center">{{ucfirst($calculated_records[$country->id]['problem_type']['title'])}}</h1>
                        </div>
                    </div>

                    <div class="bg-white bg-opacity-90 flex flex-col items-center justify-between w-full h-full p-16 mx-auto space-y-10">
                        <h1 class="text-green-light text-xl uppercase text-center font-bold">{{__('program-usage.gender')}}</h1>
                        <img src="{{$calculated_records[$country->id]['gender']['icon']}}" class="gauge-pointer h-40" style="transform-origin: center 85%;" />
                        <div class="w-full bg-purple rounded-xl flex items-center justify-center py-8">
                            <h1 class="text-white text-3xl text-center">{{ucfirst($calculated_records[$country->id]['gender']['title'])}}</h1>
                        </div>
                    </div>

                    <div class="bg-white bg-opacity-90 flex flex-col items-center justify-between w-full h-full p-16 mx-auto space-y-10">
                        <h1 class="text-green-light text-xl uppercase text-center font-bold">{{__('program-usage.age')}}</h1>
                        <img src="{{asset('assets/img/client/program-usage/age.svg')}}" class="gauge-pointer h-40" style="transform-origin: center 85%;" />
                        <div class="w-full bg-purple rounded-xl flex items-center justify-center py-8">
                            <h1 class="text-white text-3xl text-center">{{ucfirst($calculated_records[$country->id]['age'])}}</h1>
                        </div>
                    </div>

                </div>
            </div>
        @endif
    </div>
@endsection
