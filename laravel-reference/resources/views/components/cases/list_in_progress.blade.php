<div class="row col-12 case-list-in-progress @if(isset($class)) {{$class}} @endif" data-country= {{$case->country_id}}>
    <p style="background: -webkit-gradient(left top, right top, color-stop(0%, rgba(195,203,207,1)), color-stop(50%, rgba(195,202,207,1)), color-stop(50.1%, rgba(226,239,241,1)), color-stop(100%, rgba(226,239,241,1)));
            background: -webkit-linear-gradient(left, rgba(195,203,207,1) 0%, rgba(195,202,207,1) {{$case->percentage - .1}}%, rgba(226,239,241,1) {{$case->percentage}}%, rgba(226,239,241,1) 100%);
            background: -o-linear-gradient(left, rgba(195,203,207,1) 0%, rgba(195,202,207,1) {{$case->percentage - .1}}%, rgba(226,239,241,1) {{$case->percentage}}%, rgba(226,239,241,1) 100%);
            background: -ms-linear-gradient(left, rgba(195,203,207,1) 0%, rgba(195,202,207,1) {{$case->percentage - .1}}%, rgba(226,239,241,1) {{$case->percentage}}%, rgba(226,239,241,1) 100%);
            background: linear-gradient(to right, rgba(195,203,207,1) 0%, rgba(195,202,207,1) {{$case->percentage - -1}}%, rgba(226,239,241,1) {{$case->percentage}}%, rgba(226,239,241,1) 100%);
            filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#c3cbcf', endColorstr='#e2eff1', GradientType=1 );">
        {{$case->case_identifier}}
        - {{$case->values->where('case_input_id', 1)->first()->value}}
        @if(Auth::user()->type != 'expert')
            - {{$case->company != null ? $case->company->name : ''}}
        @endif
        - {{$case->case_accepted_expert() ? $case->case_accepted_expert()->name : ''}}
        - {{$case->case_type != null ? $case->case_type->getValue() : null}}
        @if(Auth::user()->type == 'expert')
            - {{$case->case_client_name != null ? $case->case_client_name->getValue() : null}}
        @endif
    </p>

    <a class="btn-radius" href="{{route(\Auth::user()->type.'.cases.view',['id' => $case->id])}}">
        <img src="{{asset('assets/img/select.svg')}}" class="mr-1" style="height: 20px; width: 20px" alt="">
        {{__('common.select')}}
    </a>

    @if(\Auth::user()->type == 'admin')
        <button class="delete-button-from-list btn-radius" style="--btn-min-width: var(--btn-func-width)" onClick="deleteCase({{$case->id}}, this)">
            <svg xmlns="http://www.w3.org/2000/svg" class="mr-0" style="height: 20px; width: 20px;" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
        </button>
    @endif

    @php
        $diff = 0;
        $over_5_days_without_consultation = 0;
        $case_consultations = $case->consultations;

        $c = $case_consultations->sortBy('consultations.id')->first();
        $now = \Carbon\Carbon::now()->setTimezone('Europe/Budapest');
        if($c){
          $date = \Carbon\Carbon::parse($c->created_at,'Europe/Budapest');
          $diff = $now->diffInDays($date);
        }
        $is_case_accepted = null;
        if($case->experts->first()){
          $expert = $case->experts->first()->pivot;
          $start = \Carbon\Carbon::parse($expert->created_at,'Europe/Budapest');
          $resolution = \Carbon\CarbonInterval::hour();
          $hours = $start->diffFiltered($resolution, function ($date) {
            return $date->isWeekday();
          }, $now);
          if($hours >= 24 && $expert->accepted == App\Enums\CaseExpertStatus::ASSIGNED_TO_EXPERT->value){
            $is_case_accepted = App\Enums\CaseExpertStatus::ASSIGNED_TO_EXPERT->value;
          }
        }
        if($case->employee_contacted_at && !sizeof($case_consultations)){
          $employee_contacted_at = \Carbon\Carbon::parse($case->employee_contacted_at,'Europe/Budapest');
          $over_5_days_without_consultation = $now->diffInDays($employee_contacted_at) >= 4 ? 1 : 0;
        }
        $case_type = $case->case_type->value;
    @endphp

    @if(!$case->case_accepted_expert() && $case->experts->where('pivot.accepted', App\Enums\CaseExpertStatus::REJECTED->value)->first())
        <p class="not-accepted">
            <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="height:20px; width:20px" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg> {{__('common.rejected-case')}}!
        </p>
    @elseif(\Auth::user()->type == 'expert' && $case->isCloseable()['closeable'])
        <p class="_3month">{{__('common.can-be-locked')}}</p>
    @elseif($diff >= 30)
        @if($diff >= 60)
            <p class="_3month">{{__('common.3rd-month')}}</p>
        @else
            <p class="_2month">{{__('common.2nd-month')}}</p>
        @endif
    @endif
    @if($case->getRawOriginal('status') == 'interrupted' && (\Auth::user()->type == 'expert' || \Auth::user()->type == 'admin' || Auth::user()->type == 'eap_admin'))
        <p class="interrupted">{{__('common.interrupted')}}</p>
    @endif
    @if($is_case_accepted == App\Enums\CaseExpertStatus::ASSIGNED_TO_EXPERT->value)
        <p class="_3month">
            <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="height:20px; width:20px" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg> {{__('common.24th-hour')}}</p>
    @endif
    @if($over_5_days_without_consultation)
        <p class="_3month">
            <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="height:20px; width:20px" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg> {{__('common.5th-day')}}</p>
    @endif
    @if($case->getRawOriginal('status') == 'client_unreachable')
        <p class="_3month">
            <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="height:20px; width:20px" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg> {{__('common.client-is-unreachable')}}!
        </p>
    @endif
    @if(!count($case->experts))
        <p class="_3month">
            <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="height:20px; width:20px" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg> {{__('common.no-expert-selected')}}</p>
    @endif

    @if($case->eap_consultation_deleted)
        <p class="_3month">
            <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="height:20px; width:20px" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            {{__('eap-online.video_therapy.eap_consultation_deleted')}}!
            @if($remaining_consultations = $case->permissionCount - $case->consultations->count() > 0)
                {{__('eap-online.video_therapy.remaining-consultations', ['attribute' => $remaining_consultations])}}
            @endif
        </p>
    @endif
    <!-- Additinal information required is true(576) WPO contract holder cases -->
    @if(optional($case->values->where('case_input_id', 97)->first())->value == 576)
        <p class="_wpo-info-required">
            <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="height:20px; width:20px" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            {{__('common.additional_information_required')}}!
        </p>
    @endif
</div>

