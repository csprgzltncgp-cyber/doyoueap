<div class="col-12 mb-3 p-0">
    <div class="d-flex flex-row mb-3">
        <div class="d-flex flex-row" style="color: rgb(89, 198, 198);">
            <span class="d-flex mr-1 align-items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="width:20px; height:20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                  </svg>
                {{__('data.year')}}:
            </span>
            <select wire:model="filter_year" class="border-0" style="color: rgb(89, 198, 198); outline: none">
                @foreach(\Carbon\CarbonPeriod::create('2022-01-01', '1 year', \Carbon\Carbon::now()->startOfYear()->format('Y-m-d')) as $date)
                    <option value="{{$date->format('Y')}}">{{$date->format('Y')}}</option>
                @endforeach
                <option value="">{{__('data.all_years')}}</option>
            </select>
        </div>
        <div class="d-flex flex-row ml-4" style="color: rgb(89, 198, 198);">
            <span class="d-flex mr-1 align-items-center">
                {{__('data.months')}}:
            </span>
            <select wire:model="filter_month" class="border-0" style="color: rgb(89, 198, 198); outline: none">
                <option value="">{{__('data.select_month')}}</option>
                @foreach (__('data.months_array') as $index => $month_name)
                    <option value="{{$index+1}}">{{$month_name}}</option>
                @endforeach
            </select>
        </div>
    </div>

    @if ($name == 'Lifeworks')
        <div class="case-list-in col-12 group {{$show_data ? 'active' : ''}}" wire:key="lifeworks-base-incoming-outgoing">
            <div class="d-flex flex-row w-100 justify-content-center align-items-center">
                <span>{{$name}}</span>

                <livewire:admin.data.incoming-outgoing-invoices.lifeworks-upload />

                <button class="caret-left float-right" wire:click="$set('show_data', {{!$show_data}})">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2"
                        class="{{$show_data ? 'rotated-icon' : ''}}"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
            </div>
        </div>
    @else
        <div class="case-list-in col-12 group {{$show_data ? 'active' : ''}}" wire:key="lifeworks-base-incoming-outgoing" wire:click="$set('show_data', {{!$show_data}})">
            <span>{{$name}}</span>

            <button class="caret-left float-right">
                <svg xmlns="http://www.w3.org/2000/svg"
                    style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    stroke-width="2"
                    class="{{$show_data ? 'rotated-icon' : ''}}"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
        </div>
    @endif

    <div wire:loading class="w-100 justify-content-center">
        <img class="spinner" src="{{asset('assets/img/spinner.svg')}}" alt="spinner">
    </div>

    @if($show_data && $data)
    <div class="mb-5">
        <x-dashboard-data.two-side-progress
            color="#a33095"
            :leftTitle="__('data.outgoing_invoices')"
            :rightTitle="__('data.incoming_invoices')"
            :title="__('data.total')"
            :leftData="$data['outgoing_total']"
            :leftPercentage="$data['incoming_percentage']"
            :rightData="$data['incoming_total_amount']"
            :rightSecondData="$data['incoming_total_qty']"
            :rightPercentage="$data['outgoing_percentage']"
        />
    </div>

    @foreach ($data['countries'] as $country_id => $country_data)
    <div class="case-list-in col-12 group  {{in_array($country_id, $show_country) ? 'active' : ''}}" wire:key="cgp-{{$country_id}}-incoming-outgoing" wire:click="update_show_countries('{{$country_id}}')">
        @if($country_data['problem'])
        <svg xmlns="http://www.w3.org/2000/svg" style="height: 20px; width: 20px; margin-bottom: 1px;   color: rgb(219,11,32);" class="mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
        </svg>
        @endif
        <span>{{array_search($country_id, $countries)}}</span>

        <button class="caret-left float-right">
            <svg xmlns="http://www.w3.org/2000/svg"
                style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                stroke-width="2"
                class="{{in_array($country_id, $show_country) ? 'rotated-icon' : ''}}"
            >
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>
    </div>

    @if(in_array($country_id, $show_country))
    <div class="mb-5">
    <x-dashboard-data.two-side-progress
        color="#a33095"
        :leftTitle="__('data.outgoing_invoices')"
        :rightTitle="__('data.incoming_invoices')"
        :title="array_search($country_id, $countries)"
        :leftData="$country_data['outgoing_total']"
        :leftPercentage="$country_data['incoming_percentage']"
        :rightData="$country_data['incoming_total_amount']"
        :rightSecondData="$country_data['incoming_total_qty']"
        :rightPercentage="$country_data['outgoing_percentage']"
        />
    </div>

    @foreach ($data['countries'][$country_id]['companies'] as $company_id => $company_data)
        <x-dashboard-data.two-side-progress
            :title="array_search($company_id, $companies)"
            :leftData="$company_data['outgoing_total']"
            :leftPercentage="$company_data['incoming_percentage']"
            :rightData="$company_data['incoming_total_amount']"
            :rightSecondData="$company_data['incoming_total_qty']"
            :rightPercentage="$company_data['outgoing_percentage']"/>
    @endforeach
    @endif
    @endforeach
    @endif
</div>
