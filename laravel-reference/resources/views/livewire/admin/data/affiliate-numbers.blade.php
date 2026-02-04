<div class="col-12">
    <!-- Affiliates invoice and consultation totals and percentages -->
    <div class="col-12 mb-3 pl-0">
        <h1>{{__('data.affiliate_numbers')}} ({{$quarter_text}})</h1>
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
                </select>
            </div>
            <div class="d-flex flex-row ml-4" style="color: rgb(89, 198, 198);">
                <span class="d-flex mr-1 align-items-center">
                    {{__('data.months')}}:
                </span>
                <select wire:model="month" wire:change="get_data()" class="border-0" style="color: rgb(89, 198, 198); outline: none">
                    <option value="">{{__('data.select_month')}}</option>
                    @foreach (__('data.months_array') as $index => $month_name)
                        <option value="{{$index+1}}">{{$month_name}}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

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
        <div wire:loading.remove class="col-12 mb-5 p-0">
            <div class="d-flex flex-row w-100">
                <div class="d-flex w-50 justify-content-center justify-content-center mb-2"><h1>{{__('data.invoice_totals')}}</h1></div>
                <div class="d-flex w-50 justify-content-center justify-content-center mb-2"><h1>{{__('data.consultation_totals')}}</h1></div>
                <div class="d-flex w-50 justify-content-center justify-content-center mb-2"><h1>{{__('data.hourly_rate')}}</h1></div>
            </div>
            <div class="d-flex flex-row w-100 justify-content-center">
                <div class="w-100" style="font-family: CalibriI; font-weight: normal;">
                    @if ($filter == 'country')
                        @foreach ($affiliate_totals as $index => $groups)
                            <div class="w-100 d-flex justify-content-center align-items-center mt-3">
                                <h4>{{$groups->first()->country}}</h4>
                            </div>
                            @foreach ($groups->sortByDesc('grand_total') as $affiliate)
                                <div class="d-flex flex-row justify-content-center align-items-center">
                                    <div class="d-flex flex-row justify-content-start" style="width:31%; text-align: center; font-size:14px;
                                    @if(count($groups) >= 10 && in_array($affiliate->name, $groups->sortByDesc('grand_total')->take(10)->pluck('name')->toArray()))
                                        font-family: CalibriB;
                                    @endif">
                                        <div class="d-flex justify-content-start mr-1" style="width:20%;">{{ $loop->index+1 }}.</div>
                                        <div class="d-flex justify-content-start" style="width:80%;"
                                        title="{{ $affiliate->name }}">
                                            {{ Str::limit($affiliate->name,10) }}
                                        </div>
                                    </div>
                                    <div class="mx-2" style="background-color: rgb(222,240,241); border-radius: 30px; height:12px; width:40%">
                                        <div style="color:white; background-color: rgb(0,87,95); border-radius: 30px; height:12px; width:{{ ($groups->max('grand_total') > 0) ? $affiliate->grand_total/$groups->max('grand_total') * 100 : 0}}%">
                                            &nbsp;
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-center" style="width:18%; font-size:16px">
                                        {{number_format($affiliate->grand_total, 0, ',', ' ')}} €
                                    </div>
                                </div>
                            @endforeach
                            <div class="d-flex flex-row justify-content-center align-items-center mx-1 p-1" style="background: rgb(222, 240, 241);">
                                <div class="w-100 text-center" style="font-family: CalibriB;">
                                    {{ $affiliate_total_count->where('country', $groups->first()->country)->first()['sum'] }} €
                                </div>
                            </div>
                        @endforeach
                    @else
                        @foreach ($affiliate_totals as $index => $affiliate)
                            <div class="d-flex flex-row justify-content-center align-items-center">
                                <div class="d-flex flex-row justify-content-start" style="width:31%; text-align: center; font-size:14px;
                                @if(in_array($affiliate->name, collect($affiliate_totals)->sortByDesc('grand_total')->take(10)->pluck('name')->toArray())) font-family: CalibriB; @endif">
                                    <div class="d-flex justify-content-start mr-1" style="width:20%;">{{ $loop->index+1 }}.</div>
                                    <div class="d-flex justify-content-start" style="width:80%;"
                                    title="{{ $affiliate->name }}">
                                        {{ Str::limit($affiliate->name,10) }}
                                    </div>
                                </div>
                                <div class="mx-2" style="background-color: rgb(222,240,241); border-radius: 30px; height:12px; width:40%">
                                    <div style="color:white; background-color: rgb(0,87,95); border-radius: 30px; height:12px; width:{{(Arr::first($affiliate_totals)->grand_total > 0) ? $affiliate->grand_total/Arr::first($affiliate_totals)->grand_total * 100 : 0}}%">
                                        &nbsp;
                                    </div>
                                </div>
                                <div class="d-flex justify-content-center" style="width:18%; font-size:16px">
                                    {{number_format($affiliate->grand_total, 0, ',', ' ')}} €
                                </div>
                            </div>
                        @endforeach
                        <div class="d-flex flex-row justify-content-center align-items-center mx-1 p-1" style="background: rgb(222, 240, 241);">
                            <div class="w-100 text-center" style="font-family: CalibriB;">
                                {{ $affiliate_total_count }} €
                            </div>
                        </div>
                    @endif
                </div>
                <div class="w-100" style="font-family: CalibriI; font-weight: normal;">
                    @if ($filter == 'country')
                        @foreach ($consultation_totals as $index => $groups)
                            <div class="w-100 d-flex justify-content-center align-items-center mt-3">
                                <h4>{{$groups->first()->country}}</h4>
                            </div>
                            @foreach ($groups->sortByDesc('total_consultations') as $affiliate)
                                <div class="d-flex flex-row justify-content-center align-items-center">
                                    <div class="d-flex flex-row justify-content-start" style="width:31%; text-align: center; font-size:14px;
                                    @if(count($groups) >= 10 && in_array($affiliate->name, $groups->sortByDesc('grand_total')->take(10)->pluck('name')->toArray()))
                                        font-family: CalibriB;
                                    @endif">
                                        <div class="d-flex justify-content-start mr-1" style="width:20%;">{{ $loop->index+1 }}.</div>
                                        <div class="d-flex justify-content-start" style="width:80%;"
                                        title="{{ $affiliate->name }}">
                                            {{ Str::limit($affiliate->name,10) }}
                                        </div>
                                    </div>
                                    <div class="mx-2" style="background-color: rgb(222,240,241); border-radius: 30px; height:12px; width:40%">
                                        <div style="color:white; background-color: rgb(0,87,95); border-radius: 30px; height:12px; width:{{ ($groups->max('total_consultations') > 0) ? $affiliate->total_consultations/$groups->max('total_consultations') * 100 : 0}}%">
                                            &nbsp;
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-center" style="width:18%; font-size:16px">
                                        {{number_format($affiliate->total_consultations, 0, ',', ' ')}} db
                                    </div>
                                </div>
                            @endforeach
                            <div class="d-flex flex-row justify-content-center align-items-center mx-1 p-1" style="background: rgb(222, 240, 241);">
                                <div class="w-100 text-center" style="font-family: CalibriB;">
                                    {{ $consultation_total_count->where('country', $groups->first()->country)->first()['sum'] }} db
                                </div>
                            </div>
                        @endforeach
                    @else
                        @foreach ($consultation_totals as $affiliate)
                            <div class="d-flex flex-row justify-content-center align-items-center">
                                <div class="d-flex flex-row justify-content-start" style="width:31%; text-align: center; font-size:14px;
                                @if(in_array($affiliate->name, collect($affiliate_totals)->sortByDesc('grand_total')->take(10)->pluck('name')->toArray())) font-family: CalibriB; @endif">
                                    <div class="d-flex justify-content-start mr-1" style="width:20%;">{{ $loop->index+1 }}.</div>
                                    <div class="d-flex justify-content-start" style="width:80%;"
                                    title="{{ $affiliate->name }}">
                                        {{ Str::limit($affiliate->name,10) }}
                                    </div>
                                </div>
                                <div class="mx-2" style="background-color: rgb(222,240,241); border-radius: 30px; height:12px; width:40%">
                                    <div style="color:white; background-color: rgb(0,87,95); border-radius: 30px; height:12px; width:{{ (Arr::first($consultation_totals)->total_consultations > 0) ? $affiliate->total_consultations/Arr::first($consultation_totals)->total_consultations * 100 : 0}}%">
                                        &nbsp;
                                    </div>
                                </div>
                                <div class="d-flex justify-content-center" style="width:18%; font-size:16px">
                                    {{number_format($affiliate->total_consultations, 0, ',', ' ')}} db
                                </div>
                            </div>
                        @endforeach
                        <div class="d-flex flex-row justify-content-center align-items-center mx-1 p-1" style="background: rgb(222, 240, 241);">
                            <div class="w-100 text-center" style="font-family: CalibriB;">
                                {{ $consultation_total_count }} db
                            </div>
                        </div>
                    @endif
                </div>
                <div class="w-100" style="font-family: CalibriI; font-weight: normal;">
                    @if ($filter == 'country')
                        @foreach ($hourly_rates as $index => $groups)
                            <div class="w-100 d-flex justify-content-center align-items-center mt-3">
                                <h4>{{$groups->first()->country}}</h4>
                            </div>
                            @foreach ($groups->sortBy('hourly_rate') as $affiliate)
                                <div class="d-flex flex-row justify-content-center align-items-center">
                                    <div class="d-flex flex-row justify-content-start" style="width:31%; text-align: center; font-size:14px;
                                    @if(count($groups) >= 10 && in_array($affiliate->name, $groups->sortByDesc('grand_total')->take(10)->pluck('name')->toArray()))
                                        font-family: CalibriB;
                                    @endif">
                                        <div class="d-flex justify-content-start mr-1" style="width:20%;">{{ $loop->index+1 }}.</div>
                                        <div class="d-flex justify-content-start" style="width:80%;"
                                        title="{{ $affiliate->name }}">
                                            {{ Str::limit($affiliate->name,10) }}
                                        </div>
                                    </div>
                                    <div class="mx-2" style="background-color: rgb(222,240,241); border-radius: 30px; height:12px; width:40%">
                                        <div style="color:white; background-color: rgb(0,87,95); border-radius: 30px; height:12px; width:{{ ($groups->max('hourly_rate') > 0) ? $affiliate->hourly_rate/$groups->max('hourly_rate') * 100 : 0}}%">
                                            &nbsp;
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-center" style="width:18%; font-size:16px">
                                        {{number_format($affiliate->hourly_rate, 0, ',', ' ')}} €
                                    </div>
                                </div>
                            @endforeach
                            <div class="d-flex flex-row justify-content-center align-items-center mx-1 p-1" style="background: rgb(222, 240, 241);">
                                <div class="w-100 text-center" style="font-family: CalibriB;">
                                    {{ $horuly_rate_total_count->where('country', $groups->first()->country)->first()['sum'] }} €
                                </div>
                            </div>
                        @endforeach
                    @else
                        @foreach ($hourly_rates as $affiliate)
                            <div class="d-flex flex-row justify-content-center align-items-center">
                                <div class="d-flex flex-row justify-content-start" style="width:31%; text-align: center; font-size:14px;
                                @if(in_array($affiliate->name, collect($affiliate_totals)->sortByDesc('grand_total')->take(10)->pluck('name')->toArray())) font-family: CalibriB; @endif">
                                    <div class="d-flex justify-content-start mr-1" style="width:20%;">{{ $loop->index+1 }}.</div>
                                    <div class="d-flex justify-content-start" style="width:80%;"
                                    title="{{ $affiliate->name }}">
                                        {{ Str::limit($affiliate->name,10) }}
                                    </div>
                                </div>
                                <div class="mx-2" style="background-color: rgb(222,240,241); border-radius: 30px; height:12px; width:40%">
                                    <div style="color:white; background-color: rgb(0,87,95); border-radius: 30px; height:12px; width:{{(Arr::last($hourly_rates)->hourly_rate > 0) ? $affiliate->hourly_rate/Arr::last($hourly_rates)->hourly_rate * 100 : 0}}%">
                                        &nbsp;
                                    </div>
                                </div>
                                <div class="d-flex justify-content-center" style="width:18%; font-size:16px">
                                    {{number_format($affiliate->hourly_rate, 0, ',', ' ')}} €
                                </div>
                            </div>
                        @endforeach
                        <div class="d-flex flex-row justify-content-center align-items-center mx-1 p-1" style="background: rgb(222, 240, 241);">
                            <div class="w-100 text-center" style="font-family: CalibriB;">
                                {{ $horuly_rate_total_count }} €
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
    <!-- Affiliates invoice and consultation totals and percentages -->
</div>
