<div class="case-list-in-progress @if(isset($class)) {{$class}} @endif" data-country = {{$case->country_id}}>
    <p style="background: -webkit-gradient(left top, right top, color-stop(0%, rgba(195,203,207,1)), color-stop(50%, rgba(195,202,207,1)), color-stop(50.1%, rgba(226,239,241,1)), color-stop(100%, rgba(226,239,241,1)));
background: -webkit-linear-gradient(left, rgba(195,203,207,1) 0%, rgba(195,202,207,1) {{$case->percentage - .1}}%, rgba(226,239,241,1) {{$case->percentage}}%, rgba(226,239,241,1) 100%);
background: -o-linear-gradient(left, rgba(195,203,207,1) 0%, rgba(195,202,207,1) {{$case->percentage - .1}}%, rgba(226,239,241,1) {{$case->percentage}}%, rgba(226,239,241,1) 100%);
background: -ms-linear-gradient(left, rgba(195,203,207,1) 0%, rgba(195,202,207,1) {{$case->percentage - .1}}%, rgba(226,239,241,1) {{$case->percentage}}%, rgba(226,239,241,1) 100%);
background: linear-gradient(to right, rgba(195,203,207,1) 0%, rgba(195,202,207,1) {{$case->percentage - -1}}%, rgba(226,239,241,1) {{$case->percentage}}%, rgba(226,239,241,1) 100%);
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#c3cbcf', endColorstr='#e2eff1', GradientType=1 );">
    {{$case->text_date}} - {{$case->text_company_name}} - {{$case->text_expert_name}} - {{$case->text_case_type}} - {{$case->text_case_location}} - {{$case->text_client_name}}
</p>
    <a class="btn-radius" href="{{route(\Auth::user()->type.'.cases.view',['id' => $case->id])}}">{{__('common.select')}}</a>
    @if($case->experts->first() && $case->experts->first()->pivot->accepted == App\Enums\CaseExpertStatus::REJECTED->value)
      <p class="not-accepted"> <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="height:20px; width:20px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
</svg> {{__('common.rejected-case')}}!</p>
    @elseif(
      $case->closable
      &&
      (\Auth::user()->type == 'admin' || \Auth::user()->type == 'operator'|| Auth::user()->type == 'eap_admin')
    )
      <p class="closeable">{{__('common.can-be-locked')}}</p>
    @elseif($case->closable && \Auth::user()->type == 'expert')
      <p class="closeable">{{__('common.awaiting-approval')}}</p>
    @elseif($case->diff >= 30)
      <p class="_2month">{{__('common.2nd-month')}}</p>
      @if($case->diff >= 60)
        <p class="_3month">{{__('common.3rd-month')}}</p>
      @endif
    @endif
    @if($case->getRawOriginal('status') == 'interrupted' && (\Auth::user()->type == 'expert' || \Auth::user()->type == 'admin' || Auth::user()->type == 'eap_admin'))
      <p class="interrupted">{{__('common.interrupted')}}</p>
    @endif
    @if($case->is_case_accepted == -1)
      <p class="_3month"><svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="height:20px; width:20px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
</svg> {{__('common.24th-hour')}}</p>
    @endif
    @if($case->over_5_days_without_consultation)
      <p class="_3month"><svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="height:20px; width:20px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
</svg> {{__('common.5th-day')}}</p>
    @endif
    @if($case->getRawOriginal('status') == 'client_unreachable')
      <p class="_3month"><svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="height:20px; width:20px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
</svg> {{__('common.client-is-unreachable')}}!</p>
    @endif
    @if(\Auth::user()->type == 'admin' || Auth::user()->type == 'eap_admin')
      <button class="delete-button-from-list" onClick="deleteCase({{$case->id}}, this)"><i class="fas fa-trash-alt"></i> {{__('common.delete')}}</button>
    @endif
</div>
