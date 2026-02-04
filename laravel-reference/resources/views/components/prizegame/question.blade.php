<div id="question-{{$question->id}}-holder" class="mb-3"
    digit_id="{{$question->digit->id}}">
   <span class="number placeholder">{{$question->digit->value}}</span>
   <div class="d-flex flex-column mt-3">
       <div class="d-flex">
           <input class="col-7 mr-3" type="text"
                  placeholder="{{__('eap-online.quizzes.question')}}"
                  name="questions[{{$question->id}}][title]"
                  value="{{$question->get_translation($language)}}"
           >
           <input class="col-7 mr-3" type="hidden"
                  name="questions[{{$question->id}}][id]"
                  value="{{$question->id}}"
           >
           <div class="col-2 d-flex flex-column justify-content-center">
               <span class="delete-quiz-section">
                   <button type="button" class="btn-radius" style="--btn-margin-bottom: 0px;"
                           onclick="deleteQuestion({{$question->id}}, true)">
                       <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                            style="height: 20px; margin-bottom: 3px"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                       <path stroke-linecap="round"
                                             stroke-linejoin="round"
                                             stroke-width="2"
                                             d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                   </svg>
                       <span>
                           {{__('common.delete')}}
                       </span>
                   </button>
               </span>
           </div>
       </div>
       <div id="question-{{$question->id}}-answers"
            class="d-flex flex-column answer-holder">
           @foreach($question->answers as $answer)
            @component('components.prizegame.answer', ['answer' => $answer, 'question' => $question, 'language' => $language])@endcomponent
           @endforeach
       </div>
   </div>
   <div class="d-flex mb-3" style="cursor: pointer"
        onclick="addAnswer({{$question->id}})">
       <span class="new-quiz-section mr-3">+</span>
       <span>{{__('eap-online.quizzes.answer_placeholder')}}</span>
   </div>
</div>
