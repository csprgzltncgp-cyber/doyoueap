@props([
    'leftTitle' => null,
    'rightTitle' => null,
    'rightSecondData' => null,
    'color' => 'rgb(0,87,95)',
    'title',
    'leftData',
    'leftPercentage',
    'rightData',
    'rightPercentage',
])

<div class="w-100">
    @if($leftTitle && $rightTitle)
    <div class="d-flex flex-row justify-content-center mb-3">
        <div class="d-flex w-50 justify-content-center justify-content-center mb-2">
            <h1>{{$leftTitle}}</h1>
        </div>
        <div class="d-flex w-50 justify-content-center justify-content-center mb-2">
            <h1>{{$rightTitle}}</h1>
        </div>
    </div>
    @endif
    <div class="d-flex flex-row justify-content-center">
        <div class="w-100">
            <div class="w-100 mb-5" style="font-family: CalibriI; font-weight: normal;">
                <div class="w-100 d-flex justify-content-center align-items-center mt-3">
                    <h4 class="text-center">{{$title}}</h4>
                </div>

                <div class="d-flex flex-row w-100">
                    <div class="d-flex w-50 flex-row justify-content-center align-items-center">
                        <div class="d-flex justify-content-start" style="width:16%;">
                            {{$leftData}} €
                        </div>
                        <div class="mx-2 d-flex justify-content-end" style="background-color: rgb(222,240,241);
                        border-top-left-radius: 30px;
                        border-bottom-left-radius: 30px;
                        height:12px;
                        width:80%">
                            <div style="color:white; background-color: {{$color}};
                            border-top-left-radius: 30px;
                            border-bottom-left-radius: 30px;
                            height:12px; width:{{$leftPercentage}}%">
                                &nbsp;
                            </div>
                        </div>
                    </div>
                    <div class="d-flex w-50 flex-row justify-content-center align-items-center">
                        <div class="mx-2" style="background-color: rgb(222,240,241);
                        border-top-right-radius: 30px;
                        border-bottom-right-radius: 30px;
                        height:12px; width:80%; position: relative;">
                            <div style="
                                color:white; background-color: {{$color}};
                                border-top-right-radius: 30px; border-bottom-right-radius: 30px;
                                height:12px; width:{{$rightPercentage}}%
                            ">
                                &nbsp;
                            </div>

                            @if($rightSecondData)
                                <span class="mt-1">{{__('data.consultations_tooltip')}} {{$rightSecondData}} db</span>
                            @endif
                        </div>
                        <div class="d-flex justify-content-center" style="width:16%; font-size:16px">
                            {{$rightData}} €
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
