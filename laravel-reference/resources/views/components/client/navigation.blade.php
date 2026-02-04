<div x-data="{ languageSelectorOpen: false, mobileMenuOpen: false }" class="space-y-2 relative">
    <div class="flex justify-between" style="min-height: 40px">
        @if(auth()->check() && url()->current() != route('client.force-change-password'))
            <div x-show="!mobileMenuOpen"
                 x-transition
                 :class="languageSelectorOpen ? '-translate-y-7' : ''"
                 class="flex items-center space-x-2 cursor-pointer transform transition-all duration-300 px-10 sm:px-0"
                 @click="languageSelectorOpen = !languageSelectorOpen">
                <p class=" text-white text-opacity-70">
                    {{config('client-languages')[app()->getLocale()]}}
                </p>
                <svg :class="languageSelectorOpen ? '' : 'rotate-180'"
                     xmlns="http://www.w3.org/2000/svg"
                     class="h-4 w-4 text-white text-opacity-70 transform transition-all duration-300"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </div>


            <div class="px-10 block sm:hidden w-full">
                <svg @click="mobileMenuOpen = !mobileMenuOpen" x-show="!languageSelectorOpen" x-transition
                     xmlns="http://www.w3.org/2000/svg"
                     class="ml-auto h-10 w-10 text-opacity-80 text-white cursor-pointer"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </div>
        @endif
    </div>

    @if(auth()->check() && url()->current() != route('client.force-change-password'))
        <div x-cloak class="sm:hidden flex flex-col absolute z-40 right-0 space-y-1.5 w-3/6 overflow-hidden">
            <a class="py-3 px-9 bg-white bg-opacity-60 w-full transform transition-all duration-300 cursor-pointer"
               :class="mobileMenuOpen ? '' : 'translate-x-full'"
               href="{{route('client.riport.show', ['totalView' => 1])}}"
            >
                <span class="uppercase text-black">{{__('common.reports')}}</span>
            </a>

            <a class="py-3 px-9 bg-white bg-opacity-60 w-full transform transition-all duration-300 delay-100 cursor-pointer text-left"
               :class="mobileMenuOpen ? '' : 'translate-x-full'"
               href="{{route('client.health-map')}}"
            >
                <span class="uppercase text-black">{{__('riport.health_map')}}</span>
            </a>

            <a class="py-3 px-9 bg-white bg-opacity-60 w-full transform transition-all duration-300 delay-200  cursor-pointer"
               :class="mobileMenuOpen ? '' : 'translate-x-full'"
               href="{{route('client.crisis-interventions')}}"
            >
                <span class="uppercase text-black">{{__('common.workshops')}}</span>
            </a>

            <a class="py-3 px-9 bg-white bg-opacity-60 w-full transform transition-all duration-300 delay-300 cursor-pointer"
               :class="mobileMenuOpen ? '' : 'translate-x-full'"
               href="{{route('client.workshops')}}"
            >
                <span class="uppercase text-black">{{__('common.crisis_interventions')}}</span>
            </a>

            @if($has_in_progress_prizegame)
                <a class="py-3 px-9 bg-white bg-opacity-60 w-full transform transition-all duration-300 delay-400 cursor-pointer"
                   :class="mobileMenuOpen ? '' : 'translate-x-full'"
                   href="{{route('client.prizegame.show')}}"
                >
                    <span class="uppercase text-black">{{__('common.prize_game')}}</span>
                </a>
            @endif

            @if(auth()->user()->companies()->first()->customer_satisfaction_index)
                <a class="home_button py-3 px-9 bg-white bg-opacity-60 w-full transform transition-all duration-300 delay-500 cursor-pointer text-left"
                   :class="mobileMenuOpen ? '' : 'translate-x-full'"
                   href="{{route('client.customer_satisfaction')}}"
                >
                    <span class="uppercase text-black">{{__('common.customer_satisfaction')}}</span>
                </a>
            @endif

            @if($has_usage_greaters_than_zero)
                <a class="home_button py-3 px-9 bg-white bg-opacity-60 w-full transform transition-all duration-300 delay-500 cursor-pointer text-left"
                    :class="mobileMenuOpen ? '' : 'translate-x-full'"
                    href="{{route('client.program_usage')}}"
                >
                    <span class="uppercase text-black">{{__('common.program_usage')}}</span>
                </a>
            @endif

            <a class="py-3 px-9 bg-white bg-opacity-60 w-full transform transition-all duration-300 delay-600 cursor-pointer text-left"
               :class="mobileMenuOpen ? '' : 'translate-x-full'"
               href="{{route('client.what-is-new.video', ['language_code' => app()->getLocale()])}}"
            >
                <span class="uppercase text-black">{{__('riport.news')}}</span>
            </a>

            {{-- <a class="py-3 px-9 bg-white bg-opacity-60 w-full transform transition-all duration-300 delay-600 cursor-pointer text-left"
               :class="mobileMenuOpen ? '' : 'translate-x-full'"
               href="{{route('client.volume-request')}}"
            >
                <span class="uppercase text-black">{{__('riport.volume_request')}}</span>
            </a> --}}

            <a class="py-3 px-9 bg-white bg-opacity-60 w-full transform transition-all duration-300 delay-700 cursor-pointer text-left"
               :class="mobileMenuOpen ? '' : 'translate-x-full'"
               href="{{route('client.new_password')}}"
            >
                <span class="uppercase text-black">{{__('common.change-password')}}</span>
            </a>

            <a class="py-3 px-9 bg-white bg-opacity-60 w-full transform transition-all duration-300 delay-800 cursor-pointer text-left"
               :class="mobileMenuOpen ? '' : 'translate-x-full'"
               href="{{route('logout')}}"
            >
                <span class="uppercase text-black">{{__('common.logout')}}</span>
            </a>

            @if(session('myAdminId'))
                <a class="py-3 px-9 bg-white bg-opacity-60 w-full transform transition-all duration-300 delay-900 cursor-pointer text-left"
                   :class="mobileMenuOpen ? '' : 'translate-x-full'"
                   onClick="backToAdmin()" href="#"
                >
                    <span class="uppercase text-black">{{__('common.back-to-admin')}}</span>
                </a>
            @endif
        </div>

        <div class="flex space-x-7 absolute px-10 sm:px-0">
            @foreach(Arr::except(config('client-languages'), app()->getLocale()) as $code => $language)
                <a href="{{route('client.custom_language', ['code' => $code])}}"
                   class="text-white text-opacity-70 transform transition-all duration-300 cursor-pointer delay-{{$loop->iteration * 100}}"
                   :class="languageSelectorOpen && !mobileMenuOpen ? '-translate-y-8' : ''"
                   x-cloak
                >
                    {{$language}}
                </a>
            @endforeach
        </div>

    @endif

    <div class="bg-yellow pt-9 px-10 flex justify-between w-full" style="position: inherit">
        <a href="{{route('client.customer_satisfaction')}}" class="flex @guest sm:-mb-7 @endguest">
            <img class="h-36 2xl:h-48 mr-1.5 mt-1" src="{{asset('assets/img/client/logo.svg')}}" alt="logo">
            <h1 class="text-green text-2xl font-semibold">EAP DASHBOARD</h1>
        </a>
        @if(auth()->check() && url()->current() != route('client.force-change-password'))
            <div class="flex justify-end items-start w-3/4">
                <div class="hidden sm:flex items-start justify-end space-x-7">
                    <div class="flex flex-wrap justify-end w-3/5">
                        <a href="{{ route('client.what-is-new.video', ['language_code' => app()->getLocale()])}}"
                           class="uppercase hover:underline mr-7 mb-2 font-bold text-[#1000c3] animate-pulse">
                            {{__('riport.news')}}
                        </a>

                        <a href="{{route('client.riport.show',  ['totalView' => 1])}}"
                           class="put-loader-on-click cursor-pointer text-green font-light uppercase hover:underline mr-7 mb-2 @if(strstr(url()->current(), '/riport')) underline @endif">
                            {{__('common.reports')}}
                        </a>

                        <a href="{{route('client.health-map')}}"
                           class="put-loader-on-click text-green font-light uppercase hover:underline mr-7 mb-2 @if(strstr(url()->current(), '/health-map')) underline @endif">
                            {{__('riport.health_map')}}
                        </a>

                        <a href="{{route('client.workshops')}}"
                           class="put-loader-on-click text-green font-light uppercase hover:underline mr-7 mb-2 @if(url()->current() == route('client.workshops')) underline @endif">
                            {{__('common.workshops')}}
                        </a>

                        <a href="{{route('client.crisis-interventions')}}"
                           class="put-loader-on-click text-green font-light uppercase hover:underline mr-7 mb-2 @if(url()->current() == route('client.crisis-interventions')) underline @endif">
                            {{__('common.crisis_interventions')}}
                        </a>

                        @if($has_in_progress_prizegame)
                            <a href="{{route('client.prizegame.show')}}"
                               class="text-green font-light uppercase hover:underline mr-7 mb-2 @if(strstr(url()->current(), '/prizegame')) underline @endif">
                                {{__('common.prize_game')}}
                            </a>
                        @endif

                        @if(auth()->user()->companies()->first()->customer_satisfaction_index)
                            <a href="{{route('client.customer_satisfaction')}}"
                               class="put-loader-on-click text-green font-light uppercase hover:underline mr-7 mb-2 @if(url()->current() == route('client.customer_satisfaction')) underline @endif">
                                {{__('common.customer_satisfaction')}}
                            </a>
                        @endif

                        @if($has_usage_greaters_than_zero)
                            <a href="{{route('client.program_usage')}}"
                                class="put-loader-on-click text-green font-light uppercase hover:underline mr-7 mb-2 @if(strstr(url()->current(), '/program-usage')) underline @endif">
                                {{__('common.program_usage')}}
                            </a>
                        @endif

                        <a href="{{route('client.volume-request')}}"
                           class="put-loader-on-click text-green font-light uppercase hover:underline mr-7 mb-2 @if(url()->current() == route('client.volume-request')) underline @endif">
                            {{__('riport.volume_request')}}
                        </a>

                        @if(auth()->user()->id == session('originalClient'))
                            <a href="{{route('client.new_password')}}"
                            class="put-loader-on-click text-green font-light uppercase hover:underline  mr-7 mb-2 @if(url()->current() == route('client.new_password')) underline @endif">
                                {{__('common.change-password')}}
                            </a>
                        @endif

                        @if(session('myAdminId'))
                            <a onClick="backToAdmin()" href="#"
                               class="put-loader-on-click text-green font-light uppercase hover:underline  mr-7 mb-2">
                                {{__('common.back-to-admin')}}
                            </a>
                        @endif
                    </div>
                    <div class="flex">
                        <a href="{{route('logout')}}"
                           class="put-loader-on-click 2xl:text-xl outline-none mx-auto uppercase font-light text-white px-16 py-3 bg-green rounded-full translation-all duration-300 hover:bg-white hover:text-yellow">
                            {{__('common.logout')}}
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
