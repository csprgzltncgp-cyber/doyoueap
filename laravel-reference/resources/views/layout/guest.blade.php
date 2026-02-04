
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
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

<div class="container-fluid p-0">
    <header class="w-100 pt-1">
        <div class="container-lg mx-auto h-100 pl-0">
            <div class="row ml-0">
                <div class="col-6">
                    <div class="d-flex">
                        <img src="/assets/img/cgp_logo_green.svg" style="width: 81px; height:80px" alt="" class="" id="logo">
                        <p class="m-0 p-0 text-uppercase mt-n1" style="font-size: 18px; color:rgb(0,87,95)">@yield('title')</p>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <div id="content" style="margin-bottom: 100px;">
        <div class="container-lg h-100" style="display: flow-root;">
            @yield('content')
        </div>
    </div>
</div>
</body>
</html>
