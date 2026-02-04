<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Client Dashboard</title>
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#7d7d7d">

    <link rel="stylesheet" href="{{asset('assets/css/client/master.css')}}?v={{time()}}">

    @livewireStyles

    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="theme-color" content="#ffffff">
    <meta name="csrf-token" content="{{csrf_token()}}">
    @yield('extra_css')
</head>
<body>
@if(session('myAdminId'))
    @if(\Auth::user()->type == 'client' && $accounts = \Auth::user()->hasConnectedClientAccounts())
        @if(sizeof($accounts) > 1)
            <div class="bg-purple text-white w-full flex space-x-5 justify-center p-1.5 z-40" id="multiple-accounts">
                @foreach($accounts->sortBy('countryWithOutScope.code') as $account)
                    <button @if(\Auth::user()->id == $account->id) class="font-bold"
                            @else onClick="loginAsClient({{$account->id}}, this)" @endif >{{$account->countryWithOutScope->code}}</button>
                @endforeach
            </div>
        @endif
    @endif
@endif

{{-- DELOITTE SPECIFIC LOGIN BETWEEN COMPANIES --}}
@php
    $deloitte_users = [];
    \App\Models\Company::query()->where('name', 'like', '%Deloitte%')->each(function($company) use (&$deloitte_users){
        $deloitte_users[$company->name] = $company->clientUsers()->pluck('user_id')->toArray();
    });

    $current_company = collect($deloitte_users)->filter(function($company) {
        return in_array(auth()->id(), $company);
    })->keys()->first();
@endphp

@if(!empty($current_company) && session('allDeloitteClient'))
    <div class="bg-purple text-white w-full flex space-x-5 justify-center p-1.5 z-40" id="multiple-accounts">
        @foreach($deloitte_users as $company_name => $user_ids)
            @if($company_name == $current_company)
                <button  class="font-bold">{{$company_name}}</button>
            @else
                <button onClick="loginAsDeloitteClient({{array_values($user_ids)[0]}})">{{$company_name}}</button>
            @endif
        @endforeach
    </div>
@endif
{{-- DELOITTE SPECIFIC LOGIN BETWEEN COMPANIES --}}

<div class="w-screen h-full bg-cover bg-{{$bg ?? 'login'}} bg-center flex flex-col justify-between"
     style="min-height: {{$height ?? '100'}}vh">
    <div id="master-container" class="w-screen pt-10 relative pb-96">
        <div class="sm:w-4/5 mx-auto" style="max-width: 2060px">
            <x-client.navigation/>
            @yield('content')
        </div>
    </div>
    @if(!strstr(url()->current(), '/prizegame') && !strstr(url()->current(), '/riport') && !strstr(url()->current(), '/health-map'))
        <x-client.footer text_color="white"/>
    @endif
</div>

@yield('extra_content')

@if(strstr(url()->current(), '/prizegame') || strstr(url()->current(), '/riport') || strstr(url()->current(), '/health-map') )
    <div class="my-10">
        <x-client.footer text_color="black"/>
    </div>
@endif
@livewireScripts
@livewire('livewire-ui-modal')
@yield('extra_js')
<script src="{{asset('js/client/master.js')}}?v={{time()}}"></script>
</body>
</html>
