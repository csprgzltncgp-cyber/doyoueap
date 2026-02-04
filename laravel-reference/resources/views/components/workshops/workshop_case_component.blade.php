<script>
    function setPaid(id) {
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'POST',
            url: '{{route('admin.workshops.setWorkshopPaid')}}',
            data: {
                id
            },
            success: function () {
                $(`.paid-${id}`).addClass('paid').attr('disabled', 'true');
            }
        });
    }
</script>
<div class="list-element col-12 workshop-admin-component">
    <span
            class="mr-0
        data
        @if($workshop_case->status == \App\Enums\WorkshopCaseStatus::PRICE_ACCEPTED) active @endif
            @if($workshop_case->status == \App\Enums\WorkshopCaseStatus::CLOSED) closed @endif">
        #{{$workshop_case->activity_id}} -
        {{$workshop_case->date}} -
        {{$workshop_case->company ? \Illuminate\Support\Str::limit($workshop_case->company->name, 25) : ''}}
        {{$workshop_case->user ? '- ' . $workshop_case->user->name : ''}}
    </span>
    @if(\Auth::user()->type == 'expert')
        <a class="edit-workshop btn-radius" style="--btn-margin-left:var(--btn-margin-x)" href="{{route('expert.workshops.edit',['id' => $workshop_case->id])}}">
            <img src="{{asset('assets/img/select.svg')}}" class="mr-1" style="height: 20px; width: 20px" alt="">
            {{__('workshop.select_button')}}
        </a>
    @else
        <a class="edit-workshop btn-radius" style="--btn-margin-left:var(--btn-margin-x)" href="{{route(auth()->user()->type . '.workshops.view',['id' => $workshop_case->id])}}">
            <img src="{{asset('assets/img/select.svg')}}" class="mr-1" style="height: 20px; width: 20px" alt="">
            {{__('workshop.select_button')}}
        </a>
        @if($workshop_case->status == \App\Enums\WorkshopCaseStatus::CLOSED && !optional($workshop_case->workshop)->free )
            <button onclick="setPaid({{$workshop_case->id}})"
                    class="pay-workshop paid-{{$workshop_case->id}} @if(optional($workshop_case->workshop)->free || $workshop_case->closed) paid @endif"
                    @if($workshop_case->closed) disabled @endif>
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="width:20px; height:20px" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </button>
        @endif
    @endif
    @if(count($workshop_case->workshop_case_events))
        @if($workshop_case->workshop_case_events->first()->event  == 'workshop_case_price_modified_by_expert' && \Auth::user()->type == 'admin')
            <span class="workshop-info workshop-price-changed"><svg xmlns="http://www.w3.org/2000/svg" class="mr-1"
                                                                    style="width:20px; height:20px" fill="none"
                                                                    viewBox="0 0 24 24" stroke="currentColor">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
</svg>{{__('workshop.price_change_expert')}}</span>
        @endif
        @if($workshop_case->workshop_case_events->first()->event  == 'workshop_case_price_modified_by_admin' && \Auth::user()->type == 'expert')
            <span class="workshop-info workshop-price-changed"><svg xmlns="http://www.w3.org/2000/svg" class="mr-1"
                                                                    style="width:20px; height:20px" fill="none"
                                                                    viewBox="0 0 24 24" stroke="currentColor">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
</svg>{{__('workshop.price_change_admin')}}</span>
        @endif
        @if($workshop_case->workshop_case_events->first()->event == 'workshop_case_accepted_by_admin' && \Auth::user()->type == 'expert')
            <span class="workshop-info workshop-price-accepted">{{__('workshop.price_accepted')}}</span>
        @endif
        @if($workshop_case->workshop_case_events->first()->event == 'workshop_case_denied_by_expert' && \Auth::user()->type == 'admin')
            <span class="workshop-info workshop-denied"><svg xmlns="http://www.w3.org/2000/svg" class="mr-1"
                                                             style="width:20px; height:20px" fill="none"
                                                             viewBox="0 0 24 24" stroke="currentColor">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
</svg>{{__('workshop.workshop_denied')}}</span>
        @endif
    @endif
</div>
