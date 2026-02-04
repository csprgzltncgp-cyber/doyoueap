@props([
    'workshop',
    'index'
])

<div class="feedback-element case-list-in col-12 group d-flex justify-content-between mb-3" onClick="toggleList({{$index}}, this, event, 'feedback-element')">
    <div class="col-10">
        @if($workshop['overall'] < 3)
            <svg xmlns="http://www.w3.org/2000/svg" style="height: 20px; width: 20px; margin-bottom: 1px;   color: rgb(219,11,32);" class="mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
        @endif

        #{{$workshop['case']->activity_id}} - {{optional($workshop['case']->company)->name}} - {{$workshop['case']->topic}} - {{optional($workshop['case']->user)->name}} - {{$workshop['case']->date}}, {{$workshop['case']->start_time}} - {{$workshop['case']->end_time}}
    </div>
    <button class="caret-left float-right">
        <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
        </svg>
    </button>
</div>

<div class="feedback-element col-12 d-none" data-country="{{$index}}">
    <div class="d-flex justify-content-between">
        <p class="mb-1" style="font-family: CalibriI; font-weight: normal;">{{__('workshop.question_1')}}</p> <span style="font-size: 25px; font-family: CalibriI; font-weight: normal;">{{$workshop['question_1']}}</span>
    </div>
    <div class="d-flex justify-content-between">
        <p class="mb-1" style="font-family: CalibriI; font-weight: normal;">{{__('workshop.question_2')}}</p> <span style="font-size: 25px; font-family: CalibriI; font-weight: normal;">{{$workshop['question_2']}}</span>
    </div>
    <div class="d-flex justify-content-between">
        <p class="mb-1" style="font-family: CalibriI; font-weight: normal;">{{__('workshop.question_3')}}</p> <span style="font-size: 25px; font-family: CalibriI; font-weight: normal;">{{$workshop['question_3']}}</span>
    </div>
    <div class="d-flex justify-content-between">
        <p class="mb-1" style="font-family: CalibriI; font-weight: normal;">{{__('workshop.question_4')}}</p> <span style="font-size: 25px; font-family: CalibriI; font-weight: normal;">{{$workshop['question_4']}}</span>
    </div>
    <div class="d-flex justify-content-between">
        <p class="mb-1" style="font-family: CalibriI; font-weight: normal;">{{__('workshop.question_5')}}</p> <span style="font-size: 25px; font-family: CalibriI; font-weight: normal;">{{$workshop['question_5']}}</span>
    </div>
    <br>
    <div class="d-flex justify-content-between">
        <p class="mb-1">{{__('workshop.overall_feedback')}}:</p> <span style="font-size: 25px;">{{$workshop['overall']}}</span>
    </div>
</div>
