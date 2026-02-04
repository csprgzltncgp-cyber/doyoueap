
<div class="case-list" data-href="{{route(\Auth::user()->type.'.cases.view',['id' => $case->id])}}" data-id="{{$case->id}}">
  <div >
    <h2>{{ $case->created_at}}</h2>
    @foreach($case->values as $value)
        @if ($case->case_type->value != 1 && $value->input && $value->input->id == 66)
            <!-- Skip specialization -->
        @else
            @if($value->input && $value->showAbleAfter3Months())
                <p>{{$value->input->translation ? $value->input->translation->value : null}}: {{$value->getValue()}}</p>
            @endif
        @endif
    @endforeach
    @if(\Auth::user()->type == 'expert')
      <p>{{__('common.expert-outsourced')}}: {{\Auth::user()->name}}</p>
    @else
      @if($case->case_accepted_expert())
        <p>{{__('common.expert-outsourced')}}: {{$case->case_accepted_expert()->name}}</p>
      @endif
    @endif
    <p>{{__('common.number-of-sessions')}}: {{sizeof($case->consultations)}}</p>
      @if(sizeof($case->consultations))
        @foreach($case->consultations as $key => $consultation)
          <p>{{__('common.date-and-time-of-session')}} {{$key+1}}{{__('common.date-and-time-of-session-after')}}: {{\Carbon\Carbon::parse($consultation->created_at)->format('Y-m-d')}}</p>
        @endforeach
      @endif

    @if(!empty($case->confirmed_at))
      <p>
          {{__('common.closed_at')}}: <span id="case-status">{{Carbon\Carbon::parse($case->confirmed_at)->format('Y-m-d')}}</span>
      </p>
    @endif
  </div>
</div>
