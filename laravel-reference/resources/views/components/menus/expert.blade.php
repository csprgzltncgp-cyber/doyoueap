<li><a href="{{route('expert.cases.in_progress')}}">{{__('common.cases-in-progress')}}</a></li>
<li><a href="{{route('expert.invoices.main')}}">{{__('common.invoices')}}</a></li>
{{-- Only show Onsite Consultation menu for `Naveet Dowson` --}}
@if(auth()->id() == 1392)
    <li><a href="{{route('expert.onsite-consultation.index')}}">{{__('common.onsite-consultation')}}</a></li>
@endif
{{-- Only show Onsite Consultation menu for `Naveet Dowson` --}}
<li><a href="{{route('expert.password_change')}}">{{__('common.change-password')}}</a></li>
<li><a href="{{route('expert.workshops.list')}}">{{__('common.all_workshop')}}</a></li>
<li><a href="{{route('expert.crisis.list')}}">{{__('common.all_crisis')}}</a></li>
<li><a href="{{route('expert.other-activities.index')}}">{{__('other-activity.all')}}</a></li>
<li><a href="{{route('expert.live-webinar.index')}}">{{__('eap-online.live-webinars.menu')}}</a></li>
<li><a href="{{route('expert.profile')}}">{{__('common.profile')}}</a></li>


@if(show_currency_change_menu())
    <li><a href="{{route('expert.currency-change.index')}}">{{__('currency-change.menu')}}</a></li>
@endif
