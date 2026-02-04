<script>
    function setPaid(id) {
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'POST',
            url: '{{route('admin.crisis.setCrisisPaid')}}',
            data: {
                id
            },
            success: function () {
                $(`.paid-${id}`).addClass('paid').attr('disabled', 'true');
            }
        });
    }
</script>
<div class="list-element col-12 crisis-admin-component">
    <span
            class="data mr-0
        @if($crisis_case->status == \App\Enums\CrisisCaseStatus::PRICE_ACCEPTED) active @endif
            @if($crisis_case->status == \App\Enums\CrisisCaseStatus::CLOSED) closed @endif">
        #{{$crisis_case->activity_id}} -
        {{$crisis_case->date}} -
        {{$crisis_case->company ? \Illuminate\Support\Str::limit($crisis_case->company->name, 25) : ''}}
        {{$crisis_case->user ? '- ' . $crisis_case->user->name : ''}}
    </span>
    @if(\Auth::user()->type == 'expert')
        <a class="edit-crisis btn-radius" style="--btn-margin-left: var(--btn-margin-x)"
           href="{{route('expert.crisis.edit',['id' => $crisis_case->id])}}">
           <img src="{{asset('assets/img/select.svg')}}" class="mr-1" style="height: 20px; width: 20px" alt="">
           {{__('crisis.select_button')}}
        </a>
    @else
        <a class="edit-crisis btn-radius" style="--btn-margin-left: var(--btn-margin-x)"
           href="{{route('admin.crisis.view', $crisis_case->id)}}">
           <img src="{{asset('assets/img/select.svg')}}" class="mr-1" style="height: 20px; width: 20px" alt="">
           {{__('crisis.select_button')}}
        </a>
        @if($crisis_case->status == \App\Enums\CrisisCaseStatus::CLOSED && !$crisis_case->crisis_intervention->free)
            <button onclick="setPaid({{$crisis_case->id}})"
                    class="pay-crisis  paid-{{$crisis_case->id}} @if($crisis_case->closed) paid @endif"
                    @if($crisis_case->closed) disabled @endif>
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="width:20px; height:20px" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </button>
        @endif
    @endif
    @if(count($crisis_case->crisis_case_events))
        @if($crisis_case->crisis_case_events->first()->event  == 'crisis_case_price_modified_by_expert' && \Auth::user()->type === 'admin')
            <span class="crisis-info crisis-price-changed"><svg xmlns="http://www.w3.org/2000/svg" class="mr-1"
                                                                style="width:20px; height:20px" fill="none"
                                                                viewBox="0 0 24 24" stroke="currentColor">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
</svg>{{__('crisis.price_change_expert')}}</span>
        @endif
        @if($crisis_case->crisis_case_events->first()->event  == 'crisis_case_price_modified_by_admin' && \Auth::user()->type === 'expert')
            <span class="crisis-info crisis-price-changed"><svg xmlns="http://www.w3.org/2000/svg" class="mr-1"
                                                                style="width:20px; height:20px" fill="none"
                                                                viewBox="0 0 24 24" stroke="currentColor">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
</svg>{{__('crisis.price_change_admin')}}</span>
        @endif
        @if($crisis_case->crisis_case_events->first()->event == 'crisis_case_accepted_by_admin' && \Auth::user()->type === 'expert')
            <span class="crisis-info crisis-price-accepted">{{__('crisis.price_accepted')}}</span>
        @endif
        @if($crisis_case->crisis_case_events->first()->event == 'crisis_case_denied_by_expert' && \Auth::user()->type === 'admin')
            <span class="crisis-info crisis-denied"><svg xmlns="http://www.w3.org/2000/svg" class="mr-1"
                                                         style="width:20px; height:20px" fill="none" viewBox="0 0 24 24"
                                                         stroke="currentColor">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
</svg>{{__('crisis.crisis_denied')}}</span>
        @endif
    @endif
</div>
