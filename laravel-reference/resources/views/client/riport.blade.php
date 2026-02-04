@extends('layout.client.master', ['bg' => 'riport',  'height' => 50])

@section('extra_js')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const eapOnlineLoginCount = document.getElementById('eap-online-login-count');
            const eapOnlineAllLoginCount = document.getElementById('eap-online-all-login-count');

            @if (isset($normal_riport_data))
                if(eapOnlineLoginCount && eapOnlineAllLoginCount){
                    document.getElementById('number-card-holder').innerHTML += `
                    <div class="h-3"></div>

                    <x-client.riport.case-number-card
                        current-number="${eapOnlineLoginCount.value}"
                        all-number="{{collect($normal_riport_data['values']['cumulated']['eap_logins'])->first()}}"
                        text="{{__('eap-online.riports.login_statistics_front')}}"
                        infoText="{{$normal_riport_data['values']['cumulated']['text']}}"
                        quarter="{{request('quarter') ?? get_last_quarter()}}"
                        id="{{uniqid()}}"
                    />
                    `;
                }
            @endif
        });
    </script>
@endsection

@section('content')
    @if(isset($normal_riport_data) && count($normal_riport_data['connected_companies']) > 1)
        <div class="flex justify-center py-2 space-x-5 text-white uppercase bg-white bg-opacity-80">
            <a  class="cursor-pointer text-purple put-loader-on-click @if($totalView)underline @endif"
                href="{{route('client.riport.show', ['country' => null, 'quarter' => request('quarter'), 'totalView' => true])}}"
                >
                {{__('riport.total')}}
        </a>
        </div>

        <x-client.riport.connected-companies
            :connectedCompanies="$normal_riport_data['connected_companies']"
            :currentCompany="$company"
            :total="$totalView"
            route="client.riport.show"
        />
    @endif

    @if(Auth::user()->all_country && isset($normal_riport_data) && !$totalView)
        <div class="flex flex-wrap justify-center py-2 space-x-5 text-white uppercase bg-green-light">
            @foreach(collect($normal_riport_data['countries'])->sortBy('name') as $country)
                @if(isset(request()->from) && isset(request()->to))
                    <a
                        href="{{route('client.riport.show', ['country' => $country['id'], 'quarter' => request('quarter'), 'totalView' => false])}}"
                        class="put-loader-on-click cursor-pointer @if(!$totalView && $normal_riport_data['current_country']['id']== $country['id'])underline @endif"
                    >
                        {{$country['name']}}
                    </a>
                @else
                    <a href="{{route('client.riport.show', ['country' => $country['id'], 'quarter' => $normal_riport_data['quarter'], 'totalView' => false])}}"
                       class="put-loader-on-click cursor-pointer @if(!$totalView && $normal_riport_data['current_country']['id'] == $country['id'])underline @endif">
                        {{$country['name']}}
                    </a>
                @endif
            @endforeach
        </div>
    @endif

    <div class="bg-white bg-opacity-80 py-16 flex justify-center items-center">
        <div class="w-4/5 relative">
            <div class="h-2.5 bg-purple rounded-lg w-full"></div>
            <div class="w-full h-full absolute top-0 items-start grid grid-cols-4">
                @if($normal_riport_data)
                    @for($i = 1; $i <= get_last_quarter(); $i++)
                        @if($normal_riport_data['quarter'] == $i)
                            @if(has_riport_in_quarter($i))
                                <div x-data="{ tooltip: false }" class="flex flex-col items-center justify-between -mt-5 group">
                                    <div x-on:mouseover="tooltip = true" x-on:mouseleave="tooltip = false" class="h-8 w-8 rounded-full bg-purple mb-2 mt-2.5 flex justify-center items-center">
                                        <div class="w-6 h-6 rounded-full bg-white border border-purple"></div>
                                    </div>
                                    <p class="text-purple">{{$normal_riport_data['from']->format('Y') . ' - ' . __('riport.'. $i .'_quarter')}}</p>
                                    <div x-show="tooltip"
                                        x-transition:enter="transition ease-out duration-300"
                                        x-transition:enter-start="opacity-0 scale-90"
                                        x-transition:enter-end="opacity-100 scale-100"
                                        x-transition:leave="transition ease-in duration-300"
                                        x-transition:leave-start="opacity-100 scale-100"
                                        x-transition:leave-end="opacity-0 scale-90"
                                        class="flex flex-col bg-purple rounded-lg text-white font-bold p-3">
                                        @foreach (company_monthly_riport_active($company, $i) as $riport)
                                            <div class="flex flex-row justify-between items-center ">
                                                {{$riport['date']}}
                                                @if($riport['active'])
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                @else
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <div class="flex flex-col items-center justify-between -mt-5 group">
                                    <div class="h-8 w-8 rounded-full bg-purple mb-2 mt-2.5 flex justify-center items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </div>
                                    <p class="text-purple">{{$normal_riport_data['from']->format('Y') . ' - ' . __('riport.'. $i .'_quarter')}}</p>
                                </div>
                            @endif
                        @else
                            @if(has_riport_in_quarter($i))
                                <a x-data="{ tooltip: false }" href="{{route('client.riport.show', ['quarter' => $i, 'country' => $normal_riport_data['current_country']['id'] ?? null, 'totalView' => $totalView])}}"
                                    class="flex flex-col items-center justify-between -mt-5" x-data="{clicked: false}" x-on:click="clicked = true">
                                    <div x-on:mouseover="tooltip = true" x-on:mouseleave="tooltip = false"
                                        class="h-8 w-8 rounded-full bg-purple mb-2 mt-2.5  flex justify-center items-center relative group" >
                                        <div class="w-6 h-6 rounded-full bg-white absolute top-1 border border-purple"></div>
                                        <div class="w-6 h-6 rounded-full bg-purple absolute top-1 group-hover:opacity-50 text-white transition-all duration-300 flex justify-center items-center">
                                            <svg x-show="clicked" class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <p class="text-purple">{{$normal_riport_data['from']->format('Y') . ' - ' . __('riport.'. $i .'_quarter')}}</p>
                                    <div x-show="tooltip"
                                        x-transition:enter="transition ease-out duration-300"
                                        x-transition:enter-start="opacity-0 scale-90"
                                        x-transition:enter-end="opacity-100 scale-100"
                                        x-transition:leave="transition ease-in duration-300"
                                        x-transition:leave-start="opacity-100 scale-100"
                                        x-transition:leave-end="opacity-0 scale-90"
                                        class="flex flex-col bg-purple rounded-lg text-white font-bold p-3">
                                        @foreach (company_monthly_riport_active($company, $i) as $riport)
                                            <div class="flex flex-row justify-between">
                                                {{$riport['date']}}
                                                @if($riport['active'])
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                @else
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </a>
                            @else
                                <div class="flex flex-col items-center justify-between -mt-5 group">
                                    <div class="h-8 w-8 rounded-full bg-purple mb-2 mt-2.5 flex justify-center items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </div>
                                    <p class="text-purple">{{$normal_riport_data['from']->format('Y') . ' - ' . __('riport.'. $i .'_quarter')}}</p>
                                </div>
                            @endif
                        @endif
                    @endfor
                @endif
            </div>
        </div>
    </div>

    @if($normal_riport_data)
        <div class="mt-20 flex flex-col space-y-3 w-3/4 lg:w-3/5 mx-auto pb-20" id="number-card-holder">
            <x-client.riport.case-number-card-big
                text="{{__('riport.cumulated_numbers')}}:"
                value="{{$normal_riport_data['values']['cumulated']['all'] + collect(data_get($normal_riport_data['values']['case_numbers'], 'in_progress'))->sum()}}"
                info="{{$normal_riport_data['values']['cumulated']['text']}}"
            />

            <div class="h-3"></div>
            
            <x-client.riport.case-number-card
                current-number="{{collect($normal_riport_data['values']['case_numbers']['closed'])->sum() + collect($normal_riport_data['values']['case_numbers']['in_progress'])->last() }}"
                all-number="{{collect($normal_riport_data['values']['cumulated']['closed'])->first() + collect($normal_riport_data['values']['case_numbers']['in_progress'])->last() }}"
                text="{{__('riport.closed_cases')}}"
                infoText="{{$normal_riport_data['values']['cumulated']['text']}}"
                cardInfoText="{{__('riport.closed_cases_info')}}"
                quarter="{{request('quarter') ?? get_last_quarter()}}"
                id="{{uniqid()}}"
            />

            <x-client.riport.case-number-card
                current-number="{{collect($normal_riport_data['values']['case_numbers']['interrupted'])->sum()}}"
                all-number="{{collect($normal_riport_data['values']['cumulated']['interrupted'])->first()}}"
                text="{{__('riport.interrupted_cases')}}"
                infoText="{{$normal_riport_data['values']['cumulated']['text']}}"
                cardInfoText="{{__('riport.interrupted_cases_info')}}"
                quarter="{{request('quarter') ?? get_last_quarter()}}"
                id="{{uniqid()}}"
                defaultCumulateHover="true"
            />

            <x-client.riport.case-number-card
                current-number="{{collect($normal_riport_data['values']['case_numbers']['client_unreachable'])->sum()}}"
                all-number="{{collect($normal_riport_data['values']['cumulated']['client_unreachable'])->first()}}"
                text="{{__('riport.client_unreachable_cases')}}"
                infoText="{{$normal_riport_data['values']['cumulated']['text']}}"
                cardInfoText="{{__('riport.client_unreachable_cases_info')}}"
                quarter="{{request('quarter') ?? get_last_quarter()}}"
                id="{{uniqid()}}"
            />

            {{-- Hide consultation numbers for Telus countries, except LPP SA (843) --}}
            @if(!in_array($normal_riport_data['current_country']['id'], config('consultations-count-disabled-countries')) || $company->id === 843)
                <x-client.riport.case-number-card
                        current-number="{{collect($normal_riport_data['values']['consultations']['count'])->sum() + collect($normal_riport_data['values']['ongoing_consultations']['count'])->last()}}"
                        all-number="{{collect($normal_riport_data['values']['cumulated']['consultations'])->sum() + collect($normal_riport_data['values']['ongoing_consultations']['count'])->last()}}"
                        text="{{__('riport.consultations')}}"
                        infoText="{{$normal_riport_data['values']['cumulated']['text']}}"
                        quarter="{{request('quarter') ?? get_last_quarter()}}"
                        id="{{uniqid()}}"
                />
            @endif

            <div class="h-3"></div>
            
            {{-- Show onsite consultation numbers for:
                - Google Switzerland (215)
                - Tesco Hungary (1255)
                - Tesco Slovakia (1254)
                - Tesco Czech Republic (1253)
            --}}
            @if(data_get($normal_riport_data['values']['cumulated'], 'onsite_consultations') && collect($normal_riport_data['values']['cumulated']['onsite_consultations'])->first() > 0 
            && data_get($normal_riport_data['values'], 'onsite_consultation_site_breakdown_text')
            && in_array($company->id, [215, 1255, 1254, 1253]))
                <x-client.riport.case-number-card
                    current-number="{{collect($normal_riport_data['values']['onsite_consultations']['count'])->sum()}}"
                    all-number="{{collect($normal_riport_data['values']['cumulated']['onsite_consultations'])->first()}}"
                    text="{{__('riport.onsite_consultations_number')}}"
                    cardInfoText="{{$normal_riport_data['values']['onsite_consultation_site_breakdown_text']}}"
                    infoText="{{$normal_riport_data['values']['cumulated']['text']}}"
                    quarter="{{request('quarter') ?? get_last_quarter()}}"
                    id="{{uniqid()}}"
                />
            @endif

            <div class="h-3"></div>

            @if(collect($normal_riport_data['values']['cumulated']['workshop'])->first() > 0)
                <x-client.riport.case-number-card
                        current-number="{{collect($normal_riport_data['values']['workshop']['participants_number'])->sum()}}"
                        all-number="{{collect($normal_riport_data['values']['cumulated']['workshop'])->first()}}"
                        text="{{__('riport.workshop_participants')}}"
                        infoText="{{$normal_riport_data['values']['cumulated']['text']}}"
                        quarter="{{request('quarter') ?? get_last_quarter()}}"
                        id="{{uniqid()}}"
                />
            @endif

            @if(collect($normal_riport_data['values']['cumulated']['crisis'])->first() > 0)
                <x-client.riport.case-number-card
                        current-number="{{collect($normal_riport_data['values']['crisis']['participants_number'])->sum()}}"
                        all-number="{{collect($normal_riport_data['values']['cumulated']['crisis'])->first()}}"
                        text="{{__('riport.crisis_participants')}}"
                        infoText="{{$normal_riport_data['values']['cumulated']['text']}}"
                        quarter="{{request('quarter') ?? get_last_quarter()}}"
                        id="{{uniqid()}}"
                />
            @endif

            @if(collect($normal_riport_data['values']['cumulated']['orientation'])->first() > 0)
                <x-client.riport.case-number-card
                        current-number="{{collect($normal_riport_data['values']['orientation']['participants_number'])->sum()}}"
                        all-number="{{collect($normal_riport_data['values']['cumulated']['orientation'])->first()}}"
                        text="{{__('riport.orientation_participants')}}"
                        infoText="{{$normal_riport_data['values']['cumulated']['text']}}"
                        quarter="{{request('quarter') ?? get_last_quarter()}}"
                        id="{{uniqid()}}"
                />
            @endif

            @if(collect($normal_riport_data['values']['cumulated']['health_day'])->first() > 0)
                <x-client.riport.case-number-card
                        current-number="{{collect($normal_riport_data['values']['health_day']['participants_number'])->sum()}}"
                        all-number="{{collect($normal_riport_data['values']['cumulated']['health_day'])->first()}}"
                        text="{{__('riport.health_day_participants')}}"
                        infoText="{{$normal_riport_data['values']['cumulated']['text']}}"
                        quarter="{{request('quarter') ?? get_last_quarter()}}"
                        id="{{uniqid()}}"
                />
            @endif

            @if(collect($normal_riport_data['values']['cumulated']['expert_outplacement'])->first() > 0)
                <x-client.riport.case-number-card
                        current-number="{{collect($normal_riport_data['values']['expert_outplacement']['participants_number'])->sum()}}"
                        all-number="{{collect($normal_riport_data['values']['cumulated']['expert_outplacement'])->first()}}"
                        text="{{__('riport.expert_outplacement_participants')}}"
                        infoText="{{$normal_riport_data['values']['cumulated']['text']}}"
                        quarter="{{request('quarter') ?? get_last_quarter()}}"
                        id="{{uniqid()}}"
                />
            @endif

            @if(collect($normal_riport_data['values']['cumulated']['prizegame'])->first() > 0)
                <x-client.riport.case-number-card
                    current-number="{{collect($normal_riport_data['values']['prizegame']['participants_number'])->sum()}}"
                    all-number="{{collect($normal_riport_data['values']['cumulated']['prizegame'])->first()}}"
                    text="{{__('riport.prizegame_participants')}}"
                    infoText="{{$normal_riport_data['values']['cumulated']['text']}}"
                    quarter="{{request('quarter') ?? get_last_quarter()}}"
                    id="{{uniqid()}}"
                />
            @endif
        </div>
    @else
        <div class="flex justify-center items-center p-10 mt-20 bg-white bg-opacity-80 text-purple font-bold uppercase">
            {{__('riport.no_available_data')}}
        </div>
    @endif
@endsection

@section('extra_content')
    {{--INPRGRESS CASES--}}
    {{-- (10 >= intval(date('d')) && 1 <= intval(date('d'))) --}}
    @if(!empty($in_progress_numbers))
        <div class="w-full bg-purple py-20">
            <div class="w-4/5 md:w-3/5 mx-auto flex flex-col justify-center items-center space-y-16">
                <h1 class="font-bold text-2xl uppercase text-white text-center">{{__('riport.in_progress_cases')}} - {{now()->format('Y.m.d H:i')}}</h1>
                <div class="w-full flex flex-wrap justify-center gap-10">
                    @foreach($in_progress_numbers as $type => $count)
                        @switch($type)
                            @case(1)
                                <div class="w-72 flex flex-col items-center space-y-3 text-back text-2xl bg-white py-10 rounded-lg relative">
                                    <div class="absolute top-5 right-5 bg-purple text-white text-base px-4 py-0.5 animate-pulse">
                                        {{__('riport.live')}}
                                    </div>
                                    <img src="{{asset('assets/img/client/riport/psychology-black.svg')}}" class="h-16" alt="psychology">
                                    <p>{{ucfirst(__('riport.welcome.page2.psychology'))}}: <span class="font-bold">{{$count}}</span></p>
                                </div>
                                @break
                            @case(2)
                                <div class="w-72 flex flex-col items-center space-y-3 text-back text-2xl bg-white py-10 rounded-lg relative">
                                    <div class="absolute top-5 right-5 bg-purple text-white text-base px-4 py-0.5 animate-pulse">
                                        {{__('riport.live')}}
                                    </div>
                                    <img src="{{asset('assets/img/client/riport/law-black.svg')}}" class="h-16" alt="law">
                                    <p>{{ucfirst(__('riport.law'))}}: <span class="font-bold">{{$count}}</span></p>
                                </div>
                                @break
                            @case(3)
                                <div class="w-72 flex flex-col items-center space-y-3 text-back text-2xl  bg-white py-10 rounded-lg relative">
                                    <div class="absolute top-5 right-5 bg-purple text-white text-base px-4 py-0.5 animate-pulse">
                                        {{__('riport.live')}}
                                    </div>
                                    <img src="{{asset('assets/img/client/riport/finance-black.svg')}}" class="h-16" alt="finance">
                                    <p>{{ucfirst(__('riport.welcome.page3.finance'))}}: <span class="font-bold">{{$count}}</span></p>
                                </div>
                                @break
                            @case(7)
                                <div class="w-72 flex flex-col items-center space-y-3 text-back text-2xl  bg-white py-10 rounded-lg relative">
                                    <div class="absolute top-5 right-5 bg-purple text-white text-base px-4 py-0.5 animate-pulse">
                                        {{__('riport.live')}}
                                    </div>
                                    <img src="{{asset('assets/img/client/riport/health-coaching-black.svg')}}" class="h-16" alt="finance">
                                    <p>{{ucfirst(__('common.health-coaching'))}}: <span class="font-bold">{{$count}}</span></p>
                                </div>
                                @break
                        @endswitch
                    @endforeach
                </div>
            </div>
        </div>
    @endif
    {{--INPRGRESS CASES--}}

    {{--NORMAL RIPORT--}}
    @if($normal_riport_data && array_key_exists('record', $normal_riport_data['values']))
        <div class="w-full bg-green-light bg-opacity-20 py-20">
            <div class="w-4/5 md:w-3/5 mx-auto flex flex-col justify-center items-center space-y-16">
                <h1 class="font-bold text-2xl uppercase text-black text-center">{{__('riport.record')}}:</h1>
                <div class="w-full flex justify-center">
                    <div class="w-64 flex flex-col items-center space-y-3 text-green-light text-xl">
                        @if($normal_riport_data['values']['record']['problem_type']->id == 1)
                            <img src="{{asset('assets/img/client/riport/psychology-green.svg')}}" class="h-14"
                                 alt="psychology">
                        @elseif($normal_riport_data['values']['record']['problem_type']->id == 11)
                            <img src="{{asset('assets/img/client/riport/coaching-green.svg')}}" class="h-14"
                                alt="coaching">
                        @elseif($normal_riport_data['values']['record']['problem_type']->id == 2)
                            <img src="{{asset('assets/img/client/riport/law-green.svg')}}" class="h-14"
                                 alt="law">
                        @elseif($normal_riport_data['values']['record']['problem_type']->id == 3)
                            <img src="{{asset('assets/img/client/riport/finance-green.svg')}}" class="h-14"
                                 alt="finance">
                        @else
                            <img src="{{asset('assets/img/client/riport/health-green.svg')}}" class="h-14"
                                 alt="health">
                        @endif
                        <p>{{__('riport.record_problem_type')}}: <span
                                    class="font-bold">{{Str::lower($normal_riport_data['values']['record']['problem_type']->translation->value)}}</span>
                        </p>
                    </div>
                    <div class="w-64 flex flex-col items-center space-y-3 text-green-light text-xl">
                        @if($normal_riport_data['values']['record']['gender']->id == 9)
                            <img src="{{asset('assets/img/client/riport/male-green.svg')}}" class="h-14"
                                 alt="finance">
                        @else
                            <img src="{{asset('assets/img/client/riport/female-green.svg')}}" class="h-14"
                                 alt="finance">
                        @endif
                        <p>{{__('riport.record_gender')}}: <span
                                    class="font-bold">{{Str::lower($normal_riport_data['values']['record']['gender']->translation->value)}}</span>
                        </p>
                    </div>
                    <div class="w-64 flex flex-col items-center space-y-3 text-green-light text-xl">
                        <img src="{{asset('assets/img/client/riport/cake-green.svg')}}" class="h-14" alt="finance">
                        <p>{{__('riport.record_age')}}:
                            <span
                                    class="font-bold">{{Str::lower($normal_riport_data['values']['record']['age']->translation->value)}}</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="w-full pb-28">
        @if($normal_riport_data)
            {{--SIMPLE TYPES--}}
            @if(array_key_exists('problem_type', $normal_riport_data['values']))
                <x-client.riport.simple-type name="{{__('riport.problem_type')}}:"
                                             :values="$normal_riport_data['values']['problem_type']"
                                             :images="[
            asset('assets/img/client/riport/psychology.svg'),
            asset('assets/img/client/riport/coaching.svg'),
            asset('assets/img/client/riport/finance.svg'),
            asset('assets/img/client/riport/law.svg'),
            asset('assets/img/client/riport/health.svg'),
        ]"/>
            @endif

            @if(array_key_exists('is_crisis', $normal_riport_data['values']) && count($normal_riport_data['values']['is_crisis']) > 1)
                <x-client.riport.simple-type name="{{__('riport.is_crisis')}}:"
                                             :values="$normal_riport_data['values']['is_crisis']"
                                             :images="[
            asset('assets/img/client/crisis_card_icon_black.svg'),
        ]"/>
            @endif

            @if(array_key_exists('problem_details', $normal_riport_data['values']))
                <x-client.riport.problem-details name="{{__('riport.problem_details')}}:"
                                             :values="$normal_riport_data['values']['problem_details']"
                                             :images="[
           asset('assets/img/client/riport/target.svg'),
        ]"/>
            @endif

            @if(array_key_exists('gender', $normal_riport_data['values']))
                <x-client.riport.simple-type name="{{__('riport.gender')}}:"
                                             :values="$normal_riport_data['values']['gender']" :images="[
            asset('assets/img/client/riport/male.svg'),
            asset('assets/img/client/riport/female.svg'),
        ]"/>
            @endif

            @if(array_key_exists('employee_or_family_member', $normal_riport_data['values']))
                <x-client.riport.simple-type name="{{__('riport.employee_or_family_member')}}:"
                                             :values="$normal_riport_data['values']['employee_or_family_member']"
                                             :images="[
            asset('assets/img/client/riport/employee.svg'),
            asset('assets/img/client/riport/family_member.svg'),
        ]"/>
            @endif

            @if(array_key_exists('age', $normal_riport_data['values']))
                <x-client.riport.simple-type name="{{__('riport.age')}}:" :values="$normal_riport_data['values']['age']"
                                             :images="[
            asset('assets/img/client/riport/cake.svg'),
        ]"/>
            @endif

            @if(array_key_exists('type_of_problem', $normal_riport_data['values']))
                <x-client.riport.simple-type name="{{__('riport.type_of_problem')}}:"
                                             :values="$normal_riport_data['values']['type_of_problem']"
                                             :images="[
            asset('assets/img/client/riport/sofa.svg'),
            asset('assets/img/client/riport/phone.svg'),
            asset('assets/img/client/riport/mouse.svg'),
            asset('assets/img/client/riport/monitor.svg'),
        ]"/>
            @endif

            @if(array_key_exists('language', $normal_riport_data['values']))
                <x-client.riport.simple-type name="{{__('riport.language')}}:"
                                             :values="$normal_riport_data['values']['language']" :images="[
            asset('assets/img/client/riport/languages.svg'),
        ]"/>
            @endif

            @if(array_key_exists('place_of_receipt', $normal_riport_data['values']))
                <x-client.riport.simple-type name="{{__('riport.place_of_receipt')}}:"
                                             :values="$normal_riport_data['values']['place_of_receipt']" :images="[
            asset('assets/img/client/riport/callcenter.svg'),
            asset('assets/img/client/riport/monitor.svg'),
            asset('assets/img/client/riport/mobil.svg'),
            asset('assets/img/client/riport/mouse.svg'),
        ]"/>
            @endif

            @if(array_key_exists('source', $normal_riport_data['values']))
                <x-client.riport.simple-type name="{{__('riport.source')}}:"
                                             :values="$normal_riport_data['values']['source']" :images="[
            asset('assets/img/client/riport/source.svg'),
        ]"/>
            @endif

            @if(array_key_exists('valeo_workplace_1', $normal_riport_data['values']))
            <x-client.riport.simple-type name="{{__('riport.valeo_workplace_1')}}:"
                                         :values="$normal_riport_data['values']['valeo_workplace_1']"/>
            @endif

            @if(array_key_exists('valeo_workplace_2', $normal_riport_data['values']))
            <x-client.riport.simple-type name="{{__('riport.valeo_workplace_2')}}:"
                                         :values="$normal_riport_data['values']['valeo_workplace_2']"/>
            @endif

            @if(array_key_exists('hydro_workplace', $normal_riport_data['values']))
            <x-client.riport.simple-type name="{{__('riport.hydro_workplace')}}:"
                                         :values="$normal_riport_data['values']['hydro_workplace']"/>
            @endif

            @if(array_key_exists('pse_workplace', $normal_riport_data['values']))
            <x-client.riport.simple-type name="{{__('riport.pse_workplace')}}:"
                                         :values="$normal_riport_data['values']['pse_workplace']"/>
            @endif

            @if(array_key_exists('michelin_workplace', $normal_riport_data['values']))
            <x-client.riport.simple-type name="{{__('riport.michelin_workplace')}}:"
                                         :values="$normal_riport_data['values']['michelin_workplace']"/>
            @endif

            @if(array_key_exists('sk_battery_workplace', $normal_riport_data['values']))
            <x-client.riport.simple-type name="{{__('riport.sk_battery_workplace')}}:"
                                         :values="$normal_riport_data['values']['sk_battery_workplace']"/>
            @endif

            @if(array_key_exists('grupa_workplace', $normal_riport_data['values']))
            <x-client.riport.simple-type name="{{__('riport.grupa_workplace')}}:"
                                         :values="$normal_riport_data['values']['grupa_workplace']"/>
            @endif

            @if(array_key_exists('robert_bosch_workplace', $normal_riport_data['values']))
            <x-client.riport.simple-type name="{{__('riport.robert_bosch_workplace')}}:"
                                         :values="$normal_riport_data['values']['robert_bosch_workplace']"/>
            @endif

            @if(array_key_exists('gsk_workplace', $normal_riport_data['values']))
            <x-client.riport.simple-type name="{{__('riport.gsk_workplace')}}:"
                                         :values="$normal_riport_data['values']['gsk_workplace']"/>
            @endif

            @if(array_key_exists('johnson_and_johnson_workplace', $normal_riport_data['values']))
            <x-client.riport.simple-type name="{{__('riport.johnson_and_johnson_workplace')}}:"
                                         :values="$normal_riport_data['values']['johnson_and_johnson_workplace']"/>
            @endif

            @if(array_key_exists('syngenta_workplace', $normal_riport_data['values']))
            <x-client.riport.simple-type name="{{__('riport.syngenta_workplace')}}:"
                                         :values="$normal_riport_data['values']['syngenta_workplace']"/>
            @endif

            @if(array_key_exists('nestle_workplace', $normal_riport_data['values']))
            <x-client.riport.simple-type name="{{__('riport.nestle_workplace')}}:"
                                         :values="$normal_riport_data['values']['nestle_workplace']"/>
            @endif

            @if(array_key_exists('mahle_pl_workplace', $normal_riport_data['values']))
            <x-client.riport.simple-type name="{{__('riport.mahle_pl_workplace')}}:"
                                         :values="$normal_riport_data['values']['mahle_pl_workplace']"/>
            @endif

            @if(array_key_exists('lpp_workplace', $normal_riport_data['values']))
            <x-client.riport.simple-type name="{{__('riport.lpp_workplace')}}:"
                                         :values="$normal_riport_data['values']['lpp_workplace']"/>
            @endif

            @if(array_key_exists('amrest_workplace', $normal_riport_data['values']))
            <x-client.riport.simple-type name="{{__('riport.amrest_workplace')}}:"
                                         :values="$normal_riport_data['values']['amrest_workplace']"/>
            @endif

            @if(array_key_exists('kuka_workplace', $normal_riport_data['values']))
            <x-client.riport.simple-type name="{{__('riport.kuka_workplace')}}:"
                                         :values="$normal_riport_data['values']['kuka_workplace']"/>
            @endif
            {{--SIMPLE TYPES--}}

            {{--COMBINED TYPES--}}
            @if(array_key_exists('gender_x_problem_type', $normal_riport_data['values']) && count($normal_riport_data['values']['age_x_problem_type']))
                <div class="w-full bg-green-light bg-opacity-20 py-20 mt-28">
                    <div class="w-4/5 md:w-3/5 mx-auto flex flex-col justify-center items-center space-y-20">
                        <h1 class="font-bold text-2xl uppercase text-black text-center">{{__('riport.gender_x_problem_type')}}
                            :</h1>
                        <div class="grid gap-20"
                             style="grid-template-columns: repeat({{min(count($normal_riport_data['values']['age_x_problem_type']),4)}}, minmax(0, 1fr));"
                        >
                            @foreach($normal_riport_data['values']['gender_x_problem_type'] as $name => $values)
                                <x-client.riport.horizontal-percentage-gender name="{{$name}}" :values="$values"/>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
            @if(array_key_exists('age_x_problem_type', $normal_riport_data['values']) && count($normal_riport_data['values']['age_x_problem_type']))
                <div class="w-full bg-purple bg-opacity-20 py-20">
                    <div class="w-4/5 md:w-3/5 mx-auto flex flex-col justify-center items-center space-y-20">
                        <h1 class="font-bold text-2xl uppercase text-black text-center">{{__('riport.age_x_problem_type')}}
                            :</h1>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-10"
                             style="grid-template-columns: repeat({{min(count($normal_riport_data['values']['age_x_problem_type']),4)}}, minmax(0, 1fr));"
                        >
                            @php
                                $random_problem_types = $normal_riport_data['values']['age_x_problem_type']->keys()->random(count($normal_riport_data['values']['age_x_problem_type']) - 1);
                            @endphp

                            @foreach($normal_riport_data['values']['age_x_problem_type'] as $name => $values)
                                <x-client.riport.horizontal-percentage-age
                                        name="{{$name}}"
                                        defaultHover="{{$random_problem_types->contains($name)}}"
                                        :values="$values"
                                />
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
            {{--COMBINED TYPES--}}
        @endif
        {{--NORMAL RIPORT--}}

        @if($normal_riport_data)
            {{--EAP ONLIE RIPORT--}}
            @livewire('client.riport.eap-online-riport-component', [
                'country_id' => $normal_riport_data['current_country']['id'],
                'totalView' => $totalView,
                'quarter' => request('quarter') ?? get_last_quarter()
            ])
            {{-- EAP ONLIE RIPORT--}}
        @endif

        <div class="my-20 sm:w-4/5 px-10 sm:px-0 mx-auto text-center">
            {{__('riport.statistics_are_rounded')}}
        </div>

        <div class="w-full flex justify-center items-center space-x-5 mb-40 relative">
            @if(isset($normal_riport_data))
                <button type="button"
                    onclick="Livewire.emit('openModal', 'client.riport.download-modal', {{json_encode(['type' => 'normal_riport', 'country' => $normal_riport_data['current_country'], 'currentQuarter' => request('quarter') ?? get_last_quarter(), 'totalView' => $totalView])}})"
                    class="transition duration-300 ease-in-out border rounded-full px-10 py-2 uppercase  hover:bg-opacity-20 hover:bg-black border-black">
                    {{__('riport.download')}}
                </button>
            @endif

            @if($company->eap_riports->where('is_active', 1)->count() && $normal_riport_data)
                <button type="button"
                    onclick="Livewire.emit('openModal', 'client.riport.download-modal', {{json_encode(['type' => 'eap_riport', 'country' => $normal_riport_data['current_country'], 'currentQuarter' => request('quarter') ?? get_last_quarter(), 'totalView' => $totalView])}})"
                    class="transition duration-300 ease-in-out border rounded-full px-10 py-2 uppercase  hover:bg-opacity-20 hover:bg-black border-black">
                    {{__('eap-online.riports.download')}}
                </button>
            @endif
        </div>
@endsection
