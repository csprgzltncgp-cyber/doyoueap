<div>
    @if(isset($riportData))
        <div class="w-full bg-center bg-cover bg-eap-riport pt-20">
            <div class="sm:w-4/5 mx-auto" style="max-width: 2060px">
                <div class="bg-purple bg-opacity-60 py-16 flex justify-center items-center">
                    <div class="w-4/5 text-center">
                        <h1 class="text-white text-2xl font-bold uppercase">{{__('eap-online.riports.client_title')}}: {{__('riport.'. $riportData['from']->quarter .'_quarter')}}</h1>
                    </div>
                </div>

                <div class="bg-white bg-opacity-80 py-16 flex justify-center items-center">
                    <div class="w-4/5 relative">
                        <div class="h-2.5 bg-purple rounded-lg w-full"></div>
                        <div class="w-full h-full absolute top-0 grid grid-cols-4">
                            @for($i = 1; $i <= get_last_quarter(); $i++)
                                @if($riportData['quarter'] == $i)
                                    @if(has_eap_riport_in_quarter($i))
                                        <div class="flex flex-col items-center justify-between -mt-5 group">
                                            <div class="h-8 w-8 rounded-full bg-purple mb-2 mt-2.5 flex justify-center items-center">
                                                <div class="w-6 h-6 rounded-full bg-white border border-purple"></div>
                                            </div>
                                            <p class="text-purple">{{__('riport.'. $i .'_quarter')}}</p>
                                        </div>
                                    @else
                                        <div class="flex flex-col items-center justify-between -mt-5 group">
                                            <div class="h-8 w-8 rounded-full bg-purple mb-2 mt-2.5 flex justify-center items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </div>
                                            <p class="text-purple">{{__('riport.'. $i .'_quarter')}}</p>
                                        </div>
                                    @endif
                                @else
                                    @if(has_eap_riport_in_quarter($i))
                                        <button wire:click="setCurrentInterval({{$i}})"
                                            class="flex flex-col items-center justify-between -mt-5">
                                            <div class="h-8 w-8 rounded-full bg-purple mb-2 mt-2.5  flex justify-center items-center relative group">
                                                <div class="w-6 h-6 rounded-full bg-white absolute top-1 border border-purple"></div>
                                                <div class="w-6 h-6 rounded-full bg-purple absolute top-1 group-hover:opacity-50 transition-all duration-300"></div>
                                            </div>
                                            <p class="text-purple">{{__('riport.'. $i .'_quarter')}}</p>
                                        </button>
                                    @else
                                        <div class="flex flex-col items-center justify-between -mt-5 group">
                                            <div class="h-8 w-8 rounded-full bg-purple mb-2 mt-2.5 flex justify-center items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </div>
                                            <p class="text-purple">{{__('riport.'. $i .'_quarter')}}</p>
                                        </div>
                                    @endif
                                @endif
                            @endfor
                        </div>
                    </div>
                </div>

                <div class="mt-20 flex flex-col space-y-3 w-3/4 lg:w-3/5 mx-auto pb-20">
                    <livewire:client.riport.eap-online-riport-number-big-component
                            :text="__('eap-online.riports.all_registration_statistics')"
                            :value="$riportData['values']['all_registers']"
                            :quarter="$riportData['quarter']"
                            wire:key="{{uniqid()}}"
                    />

                    @if($riportData['values']['all_logins'] > 0 || $riportData['values']['logins'])
                        <livewire:client.riport.eap-online-riport-number-component
                                :text="__('eap-online.riports.login_statistics')"
                                :value="$riportData['values']['logins']"
                                :allValue="$riportData['values']['all_logins']"
                                valueId="eap-online-login-count"
                                allValueId="eap-online-all-login-count"
                                wire:key="{{uniqid()}}"
                        />
                    @endif
                </div>
            </div>
        </div>

        @if(collect(\Illuminate\Support\Arr::flatten($riportData['values']['articles']))->sum() > count($riportData['values']['articles']))
            <div class="w-full mx-auto pt-28">
                <div class="flex flex-col justify-center items-center w-full">
                    <div class="flex justify-center space-x-2">
                        <img src="{{asset('assets/img/client/eap-riport/articles.svg')}}" alt="articles"
                             class="h-12"
                        >
                    </div>
                    <p class="font-bold text-2xl mt-3 mb-10 uppercase">
                        {{__('eap-online.riports.article_statistics')}}:</p>

                    @foreach($riportData['values']['articles'] as $name => $values)
                        @php
                            if(((int) $percentage = calculate_percentage($values['count'], $values['total_count'])) <= 0){
                                continue;
                            }
                        @endphp
                        <x-client.riport.vertical-percentage wire:key="{{uniqid()}}" name="{{$name}}"
                                                             :percentage="$percentage"/>
                    @endforeach
                </div>
            </div>
        @endif

        @if(collect(\Illuminate\Support\Arr::flatten($riportData['values']['videos']))->sum() > count($riportData['values']['videos']))
            <div class="w-full mx-auto pt-28">
                <div class="flex flex-col justify-center items-center w-full">
                    <div class="flex justify-center space-x-2">
                        <img src="{{asset('assets/img/client/eap-riport/videos.svg')}}" alt="videos"
                             class="h-12"
                        >
                    </div>
                    <p class="font-bold text-2xl mt-3 mb-10 uppercase">
                        {{__('eap-online.riports.video_statistics')}}:</p>

                    @foreach($riportData['values']['videos'] as $name => $values)
                        @php
                            if(((int) $percentage = calculate_percentage($values['count'], $values['total_count'])) <= 0){
                                continue;
                            }
                        @endphp
                        <x-client.riport.vertical-percentage wire:key="{{uniqid()}}" name="{{$name}}"
                                                             :percentage="$percentage"/>
                    @endforeach
                </div>
            </div>
        @endif

        @if(collect(\Illuminate\Support\Arr::flatten($riportData['values']['podcasts']))->sum() > count($riportData['values']['podcasts']))
            <div class="w-full mx-auto pt-28">
                <div class="flex flex-col justify-center items-center w-full">
                    <div class="flex justify-center space-x-2">
                        <img src="{{asset('assets/img/client/eap-riport/podcast.svg')}}" alt="podcast"
                             class="h-16"
                        >
                    </div>
                    <p class="font-bold text-2xl mt-3 mb-10 uppercase">
                        {{__('eap-online.riports.podcast_statistics')}}:</p>

                    @foreach($riportData['values']['podcasts'] as $name => $values)
                        @php
                            if(((int) $percentage = calculate_percentage($values['count'], $values['total_count'])) <= 0){
                                continue;
                            }
                        @endphp
                        <x-client.riport.vertical-percentage wire:key="{{uniqid()}}" name="{{$name}}"
                                                             :percentage="$percentage"/>
                    @endforeach
                </div>
            </div>
        @endif

        @if(collect(\Illuminate\Support\Arr::flatten($riportData['values']['self_help']))->sum() > count($riportData['values']['self_help']))
            <div class="w-full mx-auto pt-28">
                <div class="flex flex-col justify-center items-center w-full">
                    <div class="flex justify-center space-x-2">
                        <img src="{{asset('assets/img/client/eap-riport/selfhelp.svg')}}" alt="selfhelp"
                             class="h-14"
                        >
                    </div>
                    <p class="font-bold text-2xl mt-3 mb-10 uppercase">
                        {{__('eap-online.riports.self_help_statistics')}}:</p>

                    @foreach($riportData['values']['self_help'] as $name => $values)
                    @php
                        if(((int) $percentage = calculate_percentage($values['count'], $values['total_count'])) <= 0){
                            continue;
                        }
                    @endphp
                        <x-client.riport.vertical-percentage wire:key="{{uniqid()}}" name="{{$name}}"
                                                             :percentage="$percentage"/>
                    @endforeach
                </div>
            </div>
        @endif

        @if(collect(\Illuminate\Support\Arr::flatten($riportData['values']['assessment']))->sum() > count($riportData['values']['assessment']))
            <div class="w-full mx-auto pt-28">
                <div class="flex flex-col justify-center items-center w-full">
                    <div class="flex justify-center space-x-2">
                        <img src="{{asset('assets/img/client/eap-riport/assessement.svg')}}" alt="assessments"
                             class="h-16"
                        >
                    </div>
                    <p class="font-bold text-2xl mt-3 mb-10 uppercase">
                        {{__('eap-online.riports.assessment_statistics')}}:</p>

                    @foreach($riportData['values']['assessment'] as $name => $values)
                    @php
                        if(((int) $percentage = calculate_percentage($values['count'], $values['total_count'])) <= 0){
                            continue;
                        }
                    @endphp
                        <x-client.riport.vertical-percentage wire:key="{{uniqid()}}" name="{{$name}}"
                                                             :percentage="$percentage"/>
                    @endforeach
                </div>
            </div>
        @endif
    @endif
</div>
