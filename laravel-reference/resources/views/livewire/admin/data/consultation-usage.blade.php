<div class="col-12 mb-5">
    <!-- Used consultations -->

    <div class="col-12 mb-3 pl-0">
        <h1>{{__('data.consultation_usage')}}</h1>
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

    @if($show_data)
        <div class="d-flex w-100 justify-content-center">
            <div class="d-flex w-100 flex-column mb-5 justify-content-center align-items-center">
                <h1 class="mb-5">{{__('data.total_usage')}}:</h1>

                <div class="flex-d w-75 justify-content-center align-items-center" style="font-family: CalibriI; font-weight: normal;">
                    <div class="d-flex flex-row justify-content-center align-items-center">
                        <div class="d-flex justify-content-center" style="width:10%; font-size:24px;">{{__('data.type_cgp')}}</div>
                        <div class="mx-2" style="background-color: rgb(222,240,241); border-radius: 30px; height:15px; width:70%">
                            <div style="color:white; background-color: rgb(0,87,95); border-radius: 30px; height:15px; width:{{$consultation_datas['percentage_sum']['cgp']}}%">
                                &nbsp;
                            </div>
                        </div>
                        <div class="d-flex justify-content-center" style="width:10%; font-size:24px">
                            {{$consultation_datas['percentage_sum']['cgp']}}%
                        </div>
                    </div>

                    <div class="d-flex flex-row mt-3 justify-content-center align-items-center">
                        <div class="d-flex justify-content-center" style="width:10%; font-size:24px">{{__('data.type_affiliate')}}</div>
                        <div class="mx-2" style="background-color: rgb(222,240,241); border-radius: 30px; height:15px; width:70%">
                            <div style="color:white; background-color: rgb(0,87,95); border-radius: 30px; height:15px; width:{{$consultation_datas['percentage_sum']['affiliate']}}%">
                                &nbsp;
                            </div>
                        </div>
                        <div class="d-flex justify-content-center" style="width:10%; font-size:24px">
                            {{$consultation_datas['percentage_sum']['affiliate']}}%
                        </div>
                    </div>
                </div>

                <h1 class="mt-5">{{__('data.usage_by_number_of_consultation')}}:</h1>

                <div class="w-75" style="font-family: CalibriI; font-weight: normal;">

                    <div class="w-100 d-flex justify-content-center align-items-center mt-4">
                        <h4>{{__('data.type_cgp')}}</h4>
                    </div>

                    <div>
                        @foreach ($consultation_datas['averages']['cgp'] as $consultation_num => $average)
                            <div class="d-flex flex-row justify-content-center align-items-center">
                                <div class="d-flex justify-content-center" style="width:10%; text-align: center; font-size:24px">{{ $consultation_num }}</div>
                                <div class="mx-2" style="background-color: rgb(222,240,241); border-radius: 30px; height:15px; width:70%">
                                    <div style="color:white; background-color: rgb(0,87,95); border-radius: 30px; height:15px; width:{{$average/$consultation_num*100}}%">
                                        &nbsp;
                                    </div>
                                </div>
                                <div class="d-flex justify-content-center" style="width:10%; font-size:24px">
                                    {{round($average/$consultation_num*100, 1)}}%
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="w-100 d-flex justify-content-center align-items-center mt-4">
                        <h4 class="mt-3">{{__('data.type_affiliate')}}</h4>
                    </div>

                    <div>
                        @foreach ($consultation_datas['averages']['affiliate'] as $consultation_num => $average)
                            <div class="d-flex flex-row justify-content-center align-items-center">
                                <div class="d-flex justify-content-center" style="width:10%; text-align: center; font-size:24px">{{ $consultation_num }}</div>
                                <div class="mx-2" style="background-color: rgb(222,240,241); border-radius: 30px; height:15px; width:70%">
                                    <div style="color:white; background-color: rgb(0,87,95); border-radius: 30px; height:15px; width:{{$average/$consultation_num*100}}%">
                                        &nbsp;
                                    </div>
                                </div>
                                <div class="d-flex justify-content-center" style="width:10%; font-size:24px">
                                    {{round($average/$consultation_num*100, 1)}}%
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>
    @endif
    <!-- Used consultations -->
</div>
