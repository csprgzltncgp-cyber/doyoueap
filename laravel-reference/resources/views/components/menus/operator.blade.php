@php
    $unread_eap_mails = \App\Models\EapOnline\EapMailNotification::query()->whereHas('eap_mail', function ($query) {
        $query->where('country_id', \Illuminate\Support\Facades\Auth::user()->country->id);
        $query->whereNull('deleted_at');
    })->where('type', 'new_mail_operator')->count();
@endphp

<li><a href="{{route('operator.cases.new')}}">{{__('common.new-case')}}</a></li>
<li><a target="_blank" href="https://cl18.webspacecontrol.com:2096">Email</a></li>
<li class="d-flex justify-content-between align-items-center">
    <a href="{{route('operator.eap-online.mails.list')}}">{{__('eap-online.mails.menu')}}</a>

    @if(!empty($unread_eap_mails) && $unread_eap_mails > 0)
        <span class="ml-2" style="width: 36px; height: 36px; padding: 8px; text-align: center; background: #E9811B; border-radius: 50%; float: right;" class="unread_counter">{{$unread_eap_mails}}</span>
    @endif
</li>

<li><a href="{{route('operator.cases.filter')}}">{{__('common.filter-cases')}}</a></li>
<li><a href="{{route('operator.cases.in_progress')}}">{{__('common.cases-in-progress')}}</a></li>
<li><a href="{{route('operator.experts.index')}}">{{__('common.list-of-experts')}}</a></li>
