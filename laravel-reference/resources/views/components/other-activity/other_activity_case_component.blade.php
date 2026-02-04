@props([
    'other_activity'
])

<script>
    function setPaid(id) {
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'POST',
            url: '{{route('admin.other-activities.set-paid')}}',
            data: {
                id
            },
            success: function () {
                $(`.paid-${id}`).addClass('paid').attr('disabled', 'true');
            }
        });
    }
</script>

<div class="list-element col-12">
    <span class="data mr-0
    @if($other_activity->status == \App\Enums\OtherActivityStatus::STATUS_IN_PROGRESS) active @endif
    @if($other_activity->status == \App\Enums\OtherActivityStatus::STATUS_CLOSED) closed @endif
    ">
        {{$other_activity->activity_id}} -
        {{$other_activity->date}} -
        {{optional($other_activity->company)->name}} -
        {{optional($other_activity->user)->name}}
    </span>
    @php(session(['list_url' => \Illuminate\Support\Facades\Request::url()]))
    @if(\Illuminate\Support\Facades\Auth::user()->type == 'expert')
        <a class="edit-workshop btn-radius" style="--btn-margin-left: 15px;"
           href="{{route('expert.other-activities.show', ['id' => $other_activity->id])}}">
           <img class="mr-1" style="width:20px;" src="{{asset('assets/img/select.svg')}}">
           {{__('workshop.select_button')}}
        </a>
    @else
        <a href="{{ route('admin.other-activities.show', ['id' => $other_activity->id]) }}" class="edit-workshop btn-radius"
            style="--btn-margin-left: 15px;">
            <img class="mr-1" style="width:20px;" src="{{asset('assets/img/select.svg')}}">
            {{__('workshop.select_button')}}
        </a>
        @if(!empty($other_activity->company_price) && !empty($other_activity->company_currency) && $other_activity->status == \App\Enums\OtherActivityStatus::STATUS_CLOSED)
            <button onclick="setPaid({{$other_activity->id}})"
                    class="pay-workshop paid-{{$other_activity->id}} @if($other_activity->paid) paid @endif">
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="width:20px; height:20px" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </button>
        @endif
    @endif

    @if($other_activity->event)
        @if($other_activity->event->type  == \App\Models\OtherActivityEvent::TYPE_OTHER_ACTIVITY_PRICE_MODIFIED_BY_EXPERT && \Auth::user()->type == 'admin')
            <span class="workshop-info workshop-price-changed"><svg xmlns="http://www.w3.org/2000/svg" class="mr-1"
                                                                    style="width:20px; height:20px" fill="none"
                                                                    viewBox="0 0 24 24" stroke="currentColor">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
</svg>{{__('workshop.price_change_expert')}}</span>
        @endif

        @if($other_activity->event->type  == \App\Models\OtherActivityEvent::TYPE_OTHER_ACTIVITY_PRICE_MODIFIED_BY_ADMIN && \Auth::user()->type == 'expert')
            <span class="workshop-info workshop-price-changed"><svg xmlns="http://www.w3.org/2000/svg" class="mr-1"
                                                                    style="width:20px; height:20px" fill="none"
                                                                    viewBox="0 0 24 24" stroke="currentColor">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
</svg>{{__('workshop.price_change_admin')}}</span>
        @endif

        @if($other_activity->event->type == \App\Models\OtherActivityEvent::TYPE_OTHER_ACTIVITY_ACCEPTED_BY_ADMIN && \Auth::user()->type == 'expert')
            <span class="workshop-info workshop-price-accepted">{{__('workshop.price_accepted')}}</span>
        @endif

        @if($other_activity->event->type  == \App\Models\OtherActivityEvent::TYPE_OTHER_ACTIVITY_DENIED_BY_EXPERT && \Auth::user()->type == 'admin')
            <span class="workshop-info workshop-denied"><svg xmlns="http://www.w3.org/2000/svg" class="mr-1"
                                                             style="width:20px; height:20px" fill="none"
                                                             viewBox="0 0 24 24" stroke="currentColor">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
</svg>{{__('workshop.workshop_denied')}}</span>
        @endif
    @endif
</div>
