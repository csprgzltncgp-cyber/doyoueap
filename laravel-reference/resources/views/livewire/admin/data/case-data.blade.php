<div class="col-12">
    <div class="w-100 d-flex align-items-end mb-3 p-0">
        <h1 class="m-0">
            {{__('data.case_datas_by_contract_holders')}} ({{$date_intervals['from']}} - {{$date_intervals['to']}})
        </h1>
    </div>

    <div class="col-12 mb-5 p-0">
        <div class="case-list-in col-12 group
            @if(in_array( 'contract_holder', $show_data)) active @endif"
            wire:click="get_data('contract_holder')"
            wire:key="item-contract_holder">
            {{__('data.show_data')}}
            <button class="caret-left float-right">
                <svg xmlns="http://www.w3.org/2000/svg"
                    style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    stroke-width="2"
                    class="@if(in_array( 'contract_holder', $show_data)) rotated-icon @endif">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
        </div>
        <div wire:loading wire:target="get_data('contract_holder')" class="w-100 justify-content-center">
            <img class="spinner" src="{{asset('assets/img/spinner.svg')}}" alt="spinner">
        </div>
        @if ($show_data && in_array( 'contract_holder', $show_data))
            <div wire:loading.remove wire:target="get_data('contract_holder')">
                @if ($contract_holder_data || !in_array($contract_holder, $show_data))
                    @foreach ($contract_holder_data as $contract_holder => $data)
                        <div class="case-list-in col-12 group
                        @if(in_array( $contract_holder, $show_data)) active @endif"
                        wire:click="show_data('{{$contract_holder}}')">
                            {{ ($contract_holder == 'all_contact_holder') ? 'TOTAL' : $contract_holder }}
                            <button class="caret-left float-right">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="2"
                                    class="@if(in_array( $contract_holder, $show_data)) rotated-icon @endif">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                        </div>
                        @if ($show_data && in_array( $contract_holder, $show_data))
                            <div wire:key="item-contract_holder-list">
                                @foreach ($data['cases'] as $interval => $datas)
                                    <div wire:key="item-contract_holder-list_{{$interval}}" class="case-list-in col-12 group ml-2
                                    @if(in_array( $contract_holder.'-'.$interval, $show_data)) active @endif"
                                    wire:click="show_data('{{$contract_holder.'-'.$interval}}')"
                                    onclick="case_interval('interval-{{$interval}}-{{Str::replace(' ', '',$contract_holder)}}')">
                                        {{__('data.'.$interval)}}
                                        <button class="caret-left float-right">
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                stroke-width="2"
                                                class="@if(in_array( $contract_holder.'-'.$interval, $show_data)) rotated-icon @endif">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        </button>
                                    </div>
                                    @if ($show_data && in_array( $contract_holder.'-'.$interval, $show_data))
                                        <div>
                                            @foreach ($datas as $date => $problem_types)
                                                <div wire:key="item-contract_holder-list_{{$interval}}_{{$date}}" class="case-list-in col-12 group ml-3
                                                @if(in_array( $contract_holder.'-'.$interval.'-'.$date, $show_data)) active @endif"
                                                wire:click="show_data('{{$contract_holder.'-'.$interval.'-'.$date}}')">
                                                    {{$date}}
                                                    <button class="caret-left float-right">
                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                            style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                            stroke-width="2"
                                                            class="@if(in_array( $contract_holder.'-'.$interval.'-'.$date, $show_data)) rotated-icon @endif">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                                        </svg>
                                                    </button>
                                                </div>
                                                @if ($show_data && in_array( $contract_holder.'-'.$interval.'-'.$date, $show_data))
                                                    <div>
                                                        @foreach ($problem_types as $category => $values)
                                                            <div wire:key="item-contract_holder-list_{{$interval}}_{{$date}}_{{$category}}" class="case-list-in col-12 group ml-4
                                                            @if(in_array( $contract_holder.'-'.$interval.'-'.$date.'-'.$category, $show_data)) active @endif"
                                                            wire:click="show_data('{{$contract_holder.'-'.$interval.'-'.$date.'-'.$category}}')">
                                                                {{__('data.'.$category)}}
                                                                <button class="caret-left float-right">
                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                        style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                                        stroke-width="2"
                                                                        class="@if(in_array( $contract_holder.'-'.$interval.'-'.$date.'-'.$category, $show_data)) rotated-icon @endif">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                                                    </svg>
                                                                </button>
                                                            </div>
                                                            @if ($show_data && in_array( $contract_holder.'-'.$interval.'-'.$date.'-'.$category, $show_data))
                                                                <div>
                                                                    @foreach ($values as $id => $value)
                                                                        <div class="case-list-in group ml-5">
                                                                            @if($category == 'problem_type')
                                                                                {{\App\Models\Permission::query()->where('id',$id)->first()->translation->value}}: {{$value}}
                                                                            @elseif($category == 'consultation_type' || $category =='ages')
                                                                                {{\App\Models\CaseInputValue::query()->where('id',$id)->first()->translation->value}}: {{$value}}
                                                                            @else
                                                                                {{__('data.status_names.'.$id)}} : {{$value}}
                                                                            @endif
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    @endforeach
                @else
                    <p class="ml-2">{{__('riport.no_available_data')}}</p>
                @endif
            </div>
        @endif
    </div>

    <div class="w-100 d-flex align-items-end mb-3 p-0">
        <div class="col-12 pl-0">
            <h1>
                {{__('data.case_datas_by_countries')}} ({{$date_intervals['from']}} - {{$date_intervals['to']}})
            </h1>
            <div class="d-flex flex-row">
                <div class="d-flex flex-row" style="color: rgb(89, 198, 198);">
                    <span class="d-flex mr-1 align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="width:20px; height:20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                          </svg>
                          {{__('data.sort')}}:
                    </span>
                    <select wire:model="filter" wire:change="get_data('countries')" class="border-0" style="color: rgb(89, 198, 198); outline: none">
                        <option value="all" selected>{{__('data.all')}}</option>
                        <option value="months">{{__('data.months')}}</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 mb-5 p-0">
        <div class="case-list-in col-12 group mt-3
            @if(in_array( 'countries', $show_data)) active @endif"
            wire:click="get_data('countries'); 'countries"
            wire:key="item-countries">
            {{__('data.show_data')}}
            <button class="caret-left float-right">
                <svg xmlns="http://www.w3.org/2000/svg"
                    style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    stroke-width="2"
                    class="@if(in_array( 'countries', $show_data)) rotated-icon @endif">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
        </div>

        <div wire:loading wire:target="get_data('countries')" class="w-100 justify-content-center">
            <img class="spinner" src="{{asset('assets/img/spinner.svg')}}" alt="spinner">
        </div>

        @if ($show_data && in_array( 'countries', $show_data))
            <div wire:loading.remove wire:target="get_data('countries')">
                @if ($country_case_data)
                    @foreach ($country_case_data as $country => $data)
                        <div wire:key="item-countries-list_{{$country}}" class="case-list-in col-12 group ml-2
                        @if(in_array( $country, $show_data)) active @endif"
                        wire:click="show_data('{{$country}}')">
                            {{ ($country == 'all_country') ? 'TOTAL' : $country }}
                            <button class="caret-left float-right">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="2"
                                    class="@if(in_array( $country, $show_data)) rotated-icon @endif">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                        </div>
                        @if ($show_data && in_array( $country, $show_data))
                            <div>
                                @if ($filter == 'months')
                                    @foreach ($data['cases'] as $date => $values)
                                        <div>
                                            <div wire:key="item-countries-list_{{$date}}" class="case-list-in col-12 group ml-3
                                            @if(in_array( $country.'-'.$date, $show_data)) active @endif"
                                            wire:click="show_data('{{$country.'-'.$date}}')">
                                                {{$date}}
                                                <button class="caret-left float-right">
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                        stroke-width="2"
                                                        class="@if(in_array( $country.'-'.$date, $show_data)) rotated-icon @endif">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                                    </svg>
                                                </button>
                                            </div>
                                            @if ($show_data && in_array( $country.'-'.$date, $show_data))
                                                <div>
                                                    @foreach ($values as $category => $values)
                                                        <div wire:key="item-countries-list_{{$category}}" class="case-list-in col-12 group ml-3
                                                        @if(in_array( $country.'-'.$category, $show_data)) active @endif"
                                                        wire:click="show_data('{{$country.'-'.$category}}')">
                                                            {{__('data.'.$category)}}
                                                            <button class="caret-left float-right">
                                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                                    style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                                    stroke-width="2"
                                                                    class="@if(in_array( $country.'-'.$category, $show_data)) rotated-icon @endif">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                                                </svg>
                                                            </button>
                                                        </div>
                                                        @if ($show_data && in_array( $country.'-'.$category, $show_data))
                                                            <div>
                                                                @foreach ($values as $id => $value)
                                                                    @if ($id != 'ongoing')
                                                                        <div class="case-list-in group ml-4">
                                                                            @if($category == 'problem_type')
                                                                                {{\App\Models\Permission::query()->where('id',$id)->first()->translation->value}}: {{$value}}
                                                                            @elseif($category == 'consultation_type' || $category =='ages')
                                                                                {{\App\Models\CaseInputValue::query()->where('id',$id)->first()->translation->value}}: {{$value}}
                                                                            @else
                                                                                {{__('data.status_names.'.$id)}} : {{$value}}
                                                                            @endif
                                                                        </div>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                @else
                                    @foreach ($data as $category => $values)
                                        <div wire:key="item-countries-list_{{$category}}" class="case-list-in col-12 group ml-3
                                        @if(in_array( $country.'-'.$category, $show_data)) active @endif"
                                        wire:click="show_data('{{$country.'-'.$category}}')">
                                            {{__('data.'.$category)}}
                                            <button class="caret-left float-right">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                    stroke-width="2"
                                                    class="@if(in_array( $country.'-'.$category, $show_data)) rotated-icon @endif">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                                </svg>
                                            </button>
                                        </div>
                                        @if ($show_data && in_array( $country.'-'.$category, $show_data))
                                            <div>
                                                @foreach ($values as $id => $value)
                                                    @if ($id != 'ongoing')
                                                        <div class="case-list-in group ml-4">
                                                            @if($category == 'problem_type')
                                                                {{\App\Models\Permission::query()->where('id',$id)->first()->translation->value}}: {{$value}}
                                                            @elseif($category == 'consultation_type' || $category =='ages')
                                                                {{\App\Models\CaseInputValue::query()->where('id',$id)->first()->translation->value}}: {{$value}}
                                                            @else
                                                                {{__('data.status_names.'.$id)}} : {{$value}}
                                                            @endif
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        @endif
                                    @endforeach
                                @endif
                            </div>
                        @endif
                    @endforeach
                @else
                    <p class="ml-2">{{__('riport.no_available_data')}}</p>
                @endif
            </div>
        @endif
    </div>
</div>
