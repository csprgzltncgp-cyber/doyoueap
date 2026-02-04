<div class="d-flex" id="answer-{{$answer->id}}-holder">
    <input class="col-7 mr-3" type="text"
           placeholder="{{__('eap-online.quizzes.answer_placeholder')}}"
           name="questions[{{$question->id}}][answers][{{$answer->id}}][title]"
           value="{{$answer->get_translation($language)}}"
    >
    <input class="col-7 mr-3" type="hidden"
           name="questions[{{$question->id}}][answers][{{$answer->id}}][id]"
           value="{{$answer->id}}"
    >
    <div class="col-2 mr-3 d-flex flex-column justify-content-center pb-3">
        <label class="container"
               id="customer-satisfaction-not-possible">{{__('prizegame.pages.correct')}}
            <input type="radio"
                   name="questions[{{$question->id}}][correct]"
                   value="{{$answer->id}}"
                   @if($answer->correct) checked @endif>
            <span class="checkmark"></span>
        </label>
    </div>

    <div class="col-2 d-flex flex-column justify-content-center pb-3">
        <button type="button" class="btn-radius" style="--btn-margin-bottom: 0px;"
                onclick="deleteAnswer({{$answer->id}}, true)">
            <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                 style="height: 20px; margin-bottom: 3px"
                 fill="none"
                 viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round"
                      stroke-width="2"
                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
            <span>{{__('common.delete')}}</span></button>
    </div>
</div>
