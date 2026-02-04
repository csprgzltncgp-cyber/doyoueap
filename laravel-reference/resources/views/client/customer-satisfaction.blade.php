@extends('layout.client.master', ['bg' =>'dashboard'])

@section('content')

    <div x-data="{pei: {{$calculated_indexes[0]['country_id']}}, loading: false, hover: true, onscreen:false}" class="relative">
        <x-client.riport.connected-companies
            :connectedCompanies="$connected_companies"
            :currentCompany="$current_company"
            route="client.customer_satisfaction"
        />

        @if(Auth::user()->all_country)
            <div class="flex flex-wrap justify-center py-2 space-x-5 text-white uppercase bg-green-light">
                @foreach($calculated_indexes->sortByDesc('country_name') as $index)
                    <span class="cursor-pointer"
                          x-on:click="loading = true; setTimeout(()=> {pei = {{$index['country_id']}}; loading = false; }, 700);"
                          :class="pei === {{$index['country_id']}} ? 'underline' : ''">
                        {{$index['country_name']}}
                    </span>
                @endforeach
            </div>
        @endif
        <div class="bg-purple text-white p-10">
            <p class="w-3/5 break-words">{{__('common.customer_satisfaction_text')}}</p>
        </div>
        <div class="h-auto bg-white bg-opacity-60 flex flex-col justify-center items-center p-20 absolute w-full left-0">
            @foreach($calculated_indexes as $index)
                <div class="w-full"
                     x-cloak
                     x-show="pei === {{$index['country_id']}} && !loading"
                >
                    @if($index['value'] > 0)
                        <div class="w-full relative mb-5">
                            <div class="flex justify-center items-center z-50 origin-left h-16 absolute" style="top: -50px; margin-left: {{$index['value'] * 10}}%;"
                                x-cloak
                                x-show="hover && onscreen && !loading"
                                x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0 scale-90"
                                x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-300"
                                x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-90"
                            >
                                <img class="h-16" src="{{asset('assets/img/client/msg-bub-small.svg')}}" alt="msg-bub" style="max-width: fit-content !important; transform: translateX(-30px);">
                                <span class="text-white w-full text-center z-50 absolute font-bold left-1/2 transform -translate-x-1/2 px-5" style="padding-bottom: 5px; font-size: 40px; transform: translateX(-93px);">
                                   {{str_replace('.', ',',$index['value'])}}
                                </span>
                            </div>
                            <img style="transform: translateX(-100%); margin-left: {{$index['value'] * 10}}%;"
                                 class="h-16 origin-right cursor-pointer"
                                 src="{{asset('assets/img/client/like.svg')}}" alt="like"
                                 x-intersect.once.full="onscreen = true; setTimeout(()=> {hover = false;}, 1200)"
                                 x-on:mouseover="hover = true"
                                 x-on:mouseout="hover = false;"
                            >
                        </div>
                    @endif
                    <div class="w-full bg-white rounded-full h-2.5 mb-5">
                        <div class="bg-purple h-2.5 rounded-full"
                             style="width: {{$index['value'] == 0 ? 100 :$index['value'] * 10}}%"></div>
                    </div>
                    <div class="w-full flex justify-between text-purple text-2xl px-5 font-bold">
                        @for($i = 1; $i <= 10; $i++)
                            <span>{{$i}}</span>
                        @endfor
                    </div>
                </div>
            @endforeach
            <svg x-cloak x-show="loading" class="inline-block animate-spin ml-6 h-10 w-10 text-purple"
                 xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor"
                      d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>
    </div>
@endsection
