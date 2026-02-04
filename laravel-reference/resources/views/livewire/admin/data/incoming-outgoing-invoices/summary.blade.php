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

    <div class="case-list-in col-12 group {{$show_data ? 'active' : ''}}" wire:key="lifeworks-base-incoming-outgoing" wire:click="$set('show_data', {{!$show_data}})">
        <span>{{__('data.total')}}</span>

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

    <div wire:loading class="w-100 justify-content-center">
        <img class="spinner" src="{{asset('assets/img/spinner.svg')}}" alt="spinner">
    </div>

    @if($show_data)
    @foreach($data as $date => $summaries)
    <div class="mb-5">
        @if($loop->first)
        <x-dashboard-data.two-side-summary-progress
            color="#a33095"
            :leftTitle="__('data.outgoing_invoices')"
            :rightTitle="__('data.incoming_invoices')"
            :title="$date"
            :leftData="$summaries['outgoing_total']"
            :leftPercentage="$summaries['outgoing_percentage']"
            :rightData="$summaries['incoming_total']['amount']"
            :rightPercentage="$summaries['incoming_percentage']"
            :rightCount="$summaries['incoming_total']['consultations']"
        />
        @else
        <x-dashboard-data.two-side-summary-progress
            color="#a33095"
            :title="$date"
            :leftData="$summaries['outgoing_total']"
            :leftPercentage="$summaries['outgoing_percentage']"
            :rightData="$summaries['incoming_total']['amount']"
            :rightPercentage="$summaries['incoming_percentage']"
            :rightCount="$summaries['incoming_total']['consultations']"
        />
        @endif
    </div>
    @endforeach
    @endif
</div>
