<div class="w-100 mb-5">
    <div class="col-12 mb-3">
        <h1>{{__('data.invoiced_consultations')}}</h1>
        <div class="d-flex flex-row">
            <div class="d-flex flex-row" style="color: rgb(89, 198, 198);">
                <span class="d-flex mr-1 align-items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="width:20px; height:20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                      </svg>
                      {{__('data.sort')}}:
                </span>
                <select wire:model="filter" wire:change="get_data()" class="border-0" style="color: rgb(89, 198, 198); outline: none">
                    <option value="all" selected>{{__('data.all')}}</option>
                    <option value="country">{{__('data.countries')}}</option>
                </select>
            </div>
            <div class="d-flex flex-row ml-2" style="color: rgb(89, 198, 198);">
                <span class="d-flex mr-1 align-items-center">
                    {{__('data.year')}}:
                </span>
                <select wire:model="year" wire:change="get_data()" class="border-0" style="color: rgb(89, 198, 198); outline: none">
                    @foreach(\Carbon\CarbonPeriod::create('2022-01-01', '1 year', \Carbon\Carbon::now()->startOfYear()->format('Y-m-d')) as $date)
                        <option value="{{$date->format('Y')}}">{{$date->format('Y')}}</option>
                    @endforeach
                    <option value="all_years">{{__('data.all_years')}}</option>
                </select>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="case-list-in w-100 group @if($show_data) active @endif" wire:click="show_data()">
            {{__('data.show_data')}}
            <button class="caret-left float-right">
                <svg id="ecountries" xmlns="http://www.w3.org/2000/svg"
                    style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    stroke-width="2" class="@if($show_data) rotated-icon @endif">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
        </div>
        <div wire:loading class="w-100 justify-content-center">
            <img class="spinner" src="{{asset('assets/img/spinner.svg')}}" alt="spinner">
        </div>
        @if($show_data)
            @if(count($datas) == 0)
                <div wire:loading.remove class="w-100">
                    <div class="d-flex w-100 justify-content-center justify-content-center mt-3">
                        {{__('data.no_data')}}
                    </div>
                </div>
            @else
                <div wire:loading.remove class="w-100">
                    <div class="d-flex flex-column w-100 justify-content-center align-items-center mb-5 p-0">
                        <div class="d-flex flex-row justify-content-center mb-3" style="width:90%">
                            <div class="d-flex w-50 justify-content-center justify-content-center mb-2">
                                <h1>{{__('data.invoiced_consultation_number_by_month')}}</h1>
                            </div>
                            <div class="d-flex w-50 justify-content-center justify-content-center mb-2">
                                <h1>{{__('data.invoiced_consultation_total_by_month')}}</h1>
                            </div>
                        </div>
                        <div class="d-flex flex-row justify-content-center" style="width:90%">
                            <div class="w-100">
                                @if ($filter == 'country')
                                    @foreach ($datas as $data)
                                        <div class="w-100" style="margin-bottom: 100px">
                                            <div class="w-100 d-flex justify-content-center align-items-center">
                                                <h1>{{$data->country}}</h1>
                                            </div>
                                            @foreach ($data->period as $month => $numbers)
                                                <div class="w-100 mb-5" style="font-family: CalibriI; font-weight: normal;">
                                                    <div class="w-100 d-flex justify-content-center align-items-center mt-3">
                                                        <h4>{{ ($year == 'all_years') ? $numbers->period_num : __('data.months_array')[($numbers->period_num - 1)] }}</h4>
                                                    </div>
                                                    <div class="d-flex flex-row w-100">
                                                        <div class="d-flex w-50 flex-row justify-content-center align-items-center">
                                                            <div class="d-flex justify-content-start" style="width:16%;">
                                                                {{ $numbers->count }} db
                                                            </div>
                                                            <div class="mx-2 d-flex justify-content-end" style="background-color: rgb(222,240,241);
                                                            border-top-left-radius: 30px;
                                                            border-bottom-left-radius: 30px;
                                                            height:12px;
                                                            width:90%">
                                                                <div style="color:white; background-color: rgb(0,87,95);
                                                                border-top-left-radius: 30px;
                                                                border-bottom-left-radius: 30px;
                                                                height:12px; width:{{(collect($data->period)->max('count')) ? $numbers->count/collect($data->period)->max('count') * 100 : 0 }}%">
                                                                    &nbsp;
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex w-50 flex-row justify-content-center align-items-center">
                                                            <div class="mx-2" style="background-color: rgb(222,240,241);
                                                            border-top-right-radius: 30px;
                                                            border-bottom-right-radius: 30px;
                                                            height:12px; width:90%">
                                                                <div style="color:white; background-color: rgb(0,87,95);
                                                                border-top-right-radius: 30px;
                                                                border-bottom-right-radius: 30px;
                                                                height:12px; width:{{(collect($data->period)->max('amount')) ? $numbers->amount/collect($data->period)->max('amount') * 100 : 0 }}%">
                                                                    &nbsp;
                                                                </div>
                                                            </div>
                                                            <div class="d-flex justify-content-center" style="width:16%; font-size:16px">
                                                                {{number_format($numbers->amount, 0, ',', ' ')}} €
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endforeach
                                @else
                                    @foreach ($datas as $period => $numbers)
                                        <div class="w-100 mb-5" style="font-family: CalibriI; font-weight: normal;">
                                            <div class="w-100 d-flex justify-content-center align-items-center mt-3">
                                                <h4>{{ ($year == 'all_years') ? $numbers->period_num : __('data.months_array')[($numbers->period_num - 1)] }}</h4>
                                            </div>
                                            <div class="d-flex flex-row w-100">
                                                <div class="d-flex w-50 flex-row justify-content-center align-items-center">
                                                    <div class="d-flex justify-content-start" style="width:16%;">
                                                        {{ $numbers->count }} db
                                                    </div>
                                                    <div class="mx-2 d-flex justify-content-end" style="background-color: rgb(222,240,241);
                                                    border-top-left-radius: 30px;
                                                    border-bottom-left-radius: 30px;
                                                    height:12px;
                                                    width:80%">
                                                        <div style="color:white; background-color: rgb(0,87,95);
                                                        border-top-left-radius: 30px;
                                                        border-bottom-left-radius: 30px;
                                                        height:12px; width:{{$numbers->count/collect($this->datas)->max('count') * 100 }}%">
                                                            &nbsp;
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="d-flex w-50 flex-row justify-content-center align-items-center">
                                                    <div class="mx-2" style="background-color: rgb(222,240,241);
                                                    border-top-right-radius: 30px;
                                                    border-bottom-right-radius: 30px;
                                                    height:12px; width:80%">
                                                        <div style="color:white; background-color: rgb(0,87,95);
                                                        border-top-right-radius: 30px;
                                                        border-bottom-right-radius: 30px;
                                                        height:12px; width:{{$numbers->amount/collect($this->datas)->max('amount') * 100 }}%">
                                                            &nbsp;
                                                        </div>
                                                    </div>
                                                    <div class="d-flex justify-content-center" style="width:16%; font-size:16px">
                                                        {{number_format($numbers->amount, 0, ',', ' ')}} €
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endif
    </div>
</div>
