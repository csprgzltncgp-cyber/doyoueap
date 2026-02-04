
<div class="case-list" data-href="{{route(\Auth::user()->type.'.cases.view',['id' => $case->id])}}" data-id="{{$case->id}}">
  <div >
    <h2>{{isset($case->date[0])? $case->date[0]->value : ''}}</h2>
    <p>{{__('common.caseId')}}: {{$case->case_identifier}}</p>
      @foreach($case->values as $value)
      @if($value->input)
        <p>{{$value->input->translation ? $value->input->translation->value : null}}: {{$value->getValue()}}</p>
      @endif
    @endforeach
    @if($case->case_accepted_expert())
      <p>{{__('common.expert-outsourced')}}: {{$case->case_accepted_expert()->name}}</p>
    @endif
  </div>
</div>
