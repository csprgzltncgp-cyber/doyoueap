<div id="menu">
    <div class="d-flex align-items-center" onClick="showMenu(this)">
        <button type="button" class="m-0" style="display:{{$display == 0 ? 'block' : 'none'}};" id="menu-button"
                name="button">{{__('common.menu')}}</button>
    </div>
    <div id="menu-list-holder">
        <ul style="display:{{$display == 1 ? 'block' : 'none'}};">
            @if(Auth::user()->type == 'admin')
                <li><a href="{{route('admin.cases.closed')}}">{{__('common.admin-closed-cases')}}</a></li>
                <li><a href="{{route('admin.cases.in_progress')}}">{{__('common.ongoing_cases')}}</a></li>
                <li><a href="{{route('admin.companies.list')}}">{{__('common.list_of_companies')}}</a></li>
                <li><a href="{{route('admin.companies.permissions.list')}}">{{__('common.list_of_permissions')}}</a></li>
                <li><a href="{{route('admin.cities.list')}}">{{__('common.list_of_cities')}}</a></li>
                <li><a href="{{route('admin.admins.list')}}">{{__('common.list_of_admins')}}</a></li>
                <li><a href="{{route('admin.operators.list')}}">{{__('common.list_of_operators')}}</a></li>
                <li><a href="{{route('admin.experts.list')}}">{{__('common.list_of_experts')}}</a></li>
                <li><a href="{{route('admin.assets.list')}}">{{__('common.assets')}}</a></li>
                <li><a href="{{route('admin.eap-online.actions')}}">EAP online</a></li>
                <li><a href="{{route('admin.eap-online.riports.create')}}">EAP online riport</a></li>
            @elseif(Auth::user()->type == 'expert')
                <li><a href="{{route('expert.cases.in_progress')}}">{{__('common.cases-in-progress')}}</a></li>
                <li><a href="{{route('expert.password_change')}}">{{__('common.change-password')}}</a></li>
            @elseif(Auth::user()->type == 'operator')
                @php
                    $unread_eap_mails = \App\Models\EapOnline\EapMailNotification::query()->whereHas('eap_mail', function ($query) {
                       return $query->where('country_id', Auth::user()->country->id);
                   })->where('type', 'new_mail_operator')->count();
                @endphp
                <li class="d-flex justify-content-between align-items-center">
                    <a href="{{route('operator.eap-online.mails.list')}}">{{__('eap-online.mails.menu')}}</a>
                    @if(!empty($unread_eap_mails) && $unread_eap_mails > 0)
                        <span style="width: 36px; height: 36px; padding: 8px; text-align: center; background: #E9811B; border-radius: 50%; float: right;"
                              class="unread_counter">{{$unread_eap_mails}}</span>
                    @endif
                </li>
                <li><a href="{{route('operator.cases.filter')}}">{{__('common.filter-cases')}}</a></li>
                <li><a href="{{route('operator.cases.in_progress')}}">{{__('common.cases-in-progress')}}</a></li>
            @endif
            <li><a href="{{route(\Auth::user()->type.'.dashboard')}}">{{__('common.back-to-homepage')}}</a></li>
            <li><a href="{{route('logout')}}">{{__('common.logout')}}</a></li>
        </ul>
        <button id="arrow" style="display:{{$display == 1 ? 'block' : 'none'}};" onClick="menuHide(this)">
            <svg class="fa-rotate-180" xmlns="http://www.w3.org/2000/svg" style="height: 30px" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
            </svg>
        </button>
    </div>
</div>
