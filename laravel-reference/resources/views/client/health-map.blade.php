@extends('layout.client.master', ['bg' => 'health-map', 'height' => 50])

@section('extra_js')
    <script>
        (function () {
            document.querySelectorAll('.age-text').forEach(function (item) {
                item.style.marginRight = `-${(item.getBoundingClientRect().width / 2) + 0.5}px`;
            });

            let circles = document.querySelectorAll('.circle');

            circles.forEach(function (item) {
                circles.forEach(function (item2) {
                    if ((item.getBoundingClientRect().x === item2.getBoundingClientRect().x) &&
                        (item.getBoundingClientRect().y === item2.getBoundingClientRect().y) &&
                        item.getAttribute('data-id') !== item2.getAttribute('data-id') &&
                        !item.classList.contains('moved')
                    ) {
                        item.classList.add('moved');
                    }
                });
            });

            circles.forEach(function (item) {
                if (item.classList.contains('moved')) {
                    if (item.classList.contains('bg-purple')) {
                        item.style.transform = `translate(-${(item.getBoundingClientRect().width / 2) + 40}px, -${(item.getBoundingClientRect().height / 2)}px)`;
                    } else {
                        item.style.transform = `translate(-${(item.getBoundingClientRect().width / 2) - 40}px, -${(item.getBoundingClientRect().height / 2)}px)`;
                    }
                } else {
                    item.style.transform = `translate(-${(item.getBoundingClientRect().width / 2) + 0.5}px, -${(item.getBoundingClientRect().height / 2)}px)`;
                }
            });
        })();
    </script>
@endsection

@section('content')
   <x-client.riport.connected-companies
        :connectedCompanies="$connected_companies"
        :currentCompany="$current_company"
        route="client.health-map"
    />

    @if(Auth::user()->all_country && isset($health_map_data))
        <div class="flex flex-wrap justify-center py-2 space-x-5 text-white uppercase bg-green-light">
            @foreach(collect($health_map_data['countries'])->sortBy('name') as $country)
                @if(isset(request()->from) && isset(request()->to))
                    <a href="{{route('client.health-map', ['country' => $country['id']])}}"
                       class="cursor-pointer @if($health_map_data['current_country']['id']== $country['id'])underline @endif">
                        {{$country['name']}}
                    </a>

                @else
                    <a href="{{route('client.health-map', ['country' => $country['id']])}}"
                       class="cursor-pointer @if($health_map_data['current_country']['id']== $country['id'])underline @endif">
                        {{$country['name']}}
                    </a>
                @endif
            @endforeach
        </div>
    @endif

    <div class="flex items-center justify-center py-12 text-white bg-purple bg-opacity-70">
        <p class="w-3/4 break-words">{{__('riport.health_map_desc')}}</p>
    </div>

    <div class="bg-white bg-opacity-80 py-16 flex justify-center items-center">
        <div class="w-4/5 relative">
            <div class="h-2.5 bg-purple rounded-lg w-full"></div>
            <div class="w-full h-full absolute top-0 flex items-start grid grid-cols-4">
                @if(isset($health_map_data) && !empty($health_map_data))
                    @for($i = 1; $i <= $health_map_data['quarter']; $i++)
                            <div class="flex flex-col items-center justify-between -mt-5">
                                <div class="h-8 w-8 rounded-full bg-purple mb-2 mt-2.5 flex justify-center items-center">
                                    <div class="w-6 h-6 rounded-full bg-white border border-purple"></div>
                                </div>
                                <p class="text-purple">{{__('riport.'. $i .'_quarter')}}</p>
                            </div>
                    @endfor
                @endif
            </div>
        </div>
    </div>

    @if(isset($health_map_data) && !empty($health_map_data))
        <div class="mx-auto mt-0.6 mb-20 flex flex-col relative" style="max-width: 2060px">
            <x-client.health-map.health-map-row
                    :light="true"
                    :circles="$health_map_data['circles']"
                    problem_type_id="1"
                    text="{{__('riport.welcome.page2.psychology')}}"
            />

            <x-client.health-map.health-map-row
                    :circles="$health_map_data['circles']"
                    problem_type_id="3"
                    text="{{__('riport.welcome.page3.finance')}}"
            />

            <x-client.health-map.health-map-row
                    :light="true"
                    :circles="$health_map_data['circles']"
                    problem_type_id="2"
                    text=" {{__('riport.law')}}"
            />

            <x-client.health-map.health-map-row
                    :circles="$health_map_data['circles']"
                    problem_type_id="7"
                    text="{{__('common.health-coaching')}}"
            />
            <div class="grid grid-cols-7 gap-1 w-full relative mt-10" style="height: 10px">
                <div class="bg-white w-full rounded-l-full"></div>
                <div class="bg-white w-full"></div>
                <div class="bg-white w-full"></div>
                <div class="bg-white w-full"></div>
                <div class="bg-white w-full"></div>
                <div class="bg-white w-full"></div>
                <div class="bg-white w-full rounded-r-full"></div>
            </div>

            <div class="grid grid-cols-7 gap-1 w-full mt-5 text-white text-lg md:text-2xl flex justify-evenly uppercase">
                <div class="w-full flex justify-end">
                    <p class="age-text" id="age_11">{{__('riport.under_19')}}</p>
                </div>
                <div class="w-full flex justify-end">
                    <p class="age-text" id="age_12">{{__('riport.from_20_to_29')}}</p>
                </div>
                <div class="w-full flex justify-end">
                    <p class="age-text" id="age_13">{{__('riport.from_30_to_39')}}</p>
                </div>
                <div class="w-full flex justify-end">
                    <p class="age-text" id="age_14">{{__('riport.from_40_to_49')}}</p>
                </div>
                <div class="w-full flex justify-end">
                    <p class="age-text" id="age_15">{{__('riport.from_50_to_59')}}</p>
                </div>
                <div class="w-full flex justify-end">
                    <p class="age-text" id="age_16">{{__('riport.above_60')}}</p>
                </div>
                <div class="w-full flex justify-end "></div>
            </div>
        </div>
    @endif
@endsection
