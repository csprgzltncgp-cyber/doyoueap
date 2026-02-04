@section('extra_js')
    <script>
        @if(session()->has('view_exception_error'))
            @if(Auth::user()->type == "admin")
                Swal.fire({
                    title: "ERROR WHEN OPENING THE PAGE!",
                    html: `<div class="mt-3"><h4>Message:</h4> "{{session()->get('view_exception_error')['message']}}" <br> <h4>File:</h4> {{session()->get('view_exception_error')['file']}}<br> <h4>Line:</h4> {{session()->get('view_exception_error')['line']}}</div><br> <h4>USER:</h4> {{session()->get('view_exception_error')['user']}}</div>`,
                    icon: 'error'
                });
            @else
                Swal.fire({
                    title: "ERROR WHEN OPENING THE PAGE!",
                    text: "Please contact support for help!",
                    icon: 'error'
                });
            @endif
        @endif
    </script>
@endsection
@yield('variables')
        <!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="x-ua-compatible" content="IE=EmulateIE8"/>
    <title>
        @if(Auth::user()->type == "admin")
            Admin Dashboard
        @elseif(Auth::user()->type == "operator")
            Operator Dashboard
        @elseif(Auth::user()->type == "expert")
            Expert Dashboard
        @elseif(Auth::user()->type == "client")
            Client Dashboard
        @elseif(Auth::user()->type == "account_admin")
            Account Admin Dashboard
        @elseif(Auth::user()->type == "financial_admin")
            Financial Admin Dashboard
        @elseif(Auth::user()->type == "eap_admin")
            EAP Admin Dashboard
        @elseif(Auth::user()->type == "supervisor_admin")
            Supervisor Admin Dashboard
        @endif
    </title>
    <link rel="stylesheet" href="{{asset('assets/css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/header.css')}}?v={{time()}}">
    <link rel="stylesheet" href="{{asset('assets/css/master.css')}}?v={{time()}}">
    <link rel="stylesheet" href="{{asset('assets/css/menu.css')}}?v={{time()}}">
    <link rel="stylesheet" href="{{asset('assets/css/components.css')}}?v={{time()}}">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9.7.1/dist/sweetalert2.all.min.js"></script>
    <script src="https://use.fontawesome.com/e016d2454d.js"></script>
    @livewireStyles
    @yield('extra_css')
    <meta name="csrf-token" content="{{csrf_token()}}">

    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#7d7d7d">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="theme-color" content="#ffffff">
</head>
<body>
@if(session('myAdminId'))
    <p id="logged-in-as">Bejelentkezve, mint: {{\Auth::user()->name}}.</p>
@endif
@if(\Auth::user()->type == 'operator' && $accounts = \Auth::user()->hasConnectedAccounts())
    @if(sizeof($accounts) > 1)
        <div id="multiple-accounts">
            @foreach($accounts->sortBy('countryWithOutScope.code') as $account)
                <button @if(\Auth::user()->id == $account->id) class="active"
                        @else onClick="loginAsOperator({{$account->id}}, this)" @endif >{{$account->countryWithOutScope->code}}</button>
            @endforeach
        </div>
    @endif
@endif
@if(in_array(Auth::user()->type, ['admin', 'production_admin', 'production_translating_admin', 'account_admin', 'financial_admin', 'eap_admin', 'affiliate_search_admin']) && $accounts = \Auth::user()->hasConnectedAccounts())
    @if(sizeof($accounts) > 1)
        <div id="multiple-accounts">
            @foreach($accounts->sortBy('id') as $account)
                <button @if(\Auth::user()->id == $account->id) class="active"
                        @else onClick="loginAsAdmin({{$account->id}}, '{{$account->type}}')" @endif >{{Str::replace('_', ' ', $account->type)}}</button>
            @endforeach
        </div>
    @endif
@endif

@if(session('myAdminId'))
    @if(\Auth::user()->type == 'client' && $accounts = \Auth::user()->hasConnectedClientAccounts())
        @if(sizeof($accounts) > 1)
            <div id="multiple-accounts">
                @foreach($accounts->sortBy('countryWithOutScope.code') as $account)
                    <button @if(\Auth::user()->id == $account->id) class="active"
                            @else onClick="loginAsClient({{$account->id}}, this)" @endif >{{$account->countryWithOutScope->code}}</button>
                @endforeach
            </div>
        @endif
    @endif
@endif
<div class="container-fluid p-0">
    <header class="w-100 pt-1">
        <div class="container-lg mx-auto h-100 pl-0">
            <div class="row ml-0">
                <div class="col-6">
                    <a href="{{route(\Auth::user()->type.'.dashboard')}}" class="d-flex">
                        <img src="/assets/img/cgp_logo_green.svg" style="width: 81px; height:80px" alt="" class="" id="logo">
                        <p class="m-0 p-0 text-uppercase mt-n1" style="font-size: 18px; color:rgb(0,87,95)">@yield('title')</p>
                    </a>
                </div>
            </div>
        </div>
    </header>
    <div id="content" style="margin-bottom: 100px;">
        <div class="container-lg h-100" style="display: flow-root;">
            @component('components.cgp_menu',['display' => isset($display) && $display == 'block' ? 1 : 0])
            @endcomponent
        </div>
        <div class="container-lg h-100" style="display: flow-root;">
            @yield('content')
        </div>
    </div>
</div>
@yield('modal')
<script
        src="https://code.jquery.com/jquery-3.4.1.min.js"
        integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
        crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
        integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1"
        crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
        crossorigin="anonymous"></script>
<script src="/assets/js/master.js?v={{time()}}" charset="utf-8"></script>
@livewireScripts
@livewire('livewire-ui-modal')
@yield('extra_js')
@stack('livewire_js')
@if(isset($user_notifications))
    <script>
        @if(!session()->get('myAdminId'))
            $(function () {
                notificationModal(@json($user_notifications));
            });
        @endif

        $(function () {
            notificationModal(@json($user_notifications));
        });

        function notificationModal(notifications) {
            if (Object.keys(notifications).length) {
                const first_key = Object.keys(notifications)[0];
                const text = notifications[first_key];
                Swal.fire({
                    title: '{{__("common.system-message")}}!',
                    text: text,
                    imageUrl: '/assets/img/info.png',
                    imageHeight: 78,
                    confirmButtonText: '{{__("common.system-message-accept")}}',
                }).then((result) => {
                    //ajax hívás
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: 'POST',
                        url: '/ajax/notification-seen/' + first_key,
                        success: function (data) {
                            if (data.status == 0) {
                                delete notifications[first_key];
                                notificationModal(notifications);
                            }
                        }
                    });
                });
            }
        }
    </script>
@endif
</body>
</html>
