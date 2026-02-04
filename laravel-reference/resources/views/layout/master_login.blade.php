<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>
        @if(strpos(url()->current(), 'admin/login'))
            Admin Dashboard
        @elseif(strpos(url()->current(), 'operator/login'))
            Operator Dashboard
        @elseif(strpos(url()->current(), 'expert/login'))
            Expert Dashboard
        @elseif(strpos(url()->current(), 'client/login'))
            Client Dashboard
        @elseif(strpos(url()->current(), 'account_admin/login'))
            Account Admin Dashboard
        @elseif(strpos(url()->current() , "financial_admin/login"))
            Financial Admin Dashboard
        @elseif(strpos(url()->current() , "eap_admin/login"))
            EAP Admin Dashboard
        @endif
    </title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
          integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="/assets/css/header.css?v={{time()}}">
    <link rel="stylesheet" href="/assets/css/master.css?v={{time()}}">

    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#7d7d7d">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="theme-color" content="#ffffff">
    @yield('extra_css')
</head>
<body>
<div class="container-fluid p-0">
    <div class="d-flex justify-content-center">
        <header class="pt-1">
            <div class="container mx-auto h-100">
                <div class="row">
                    <div class="col-12 col-md-12 pl-0">
                        <div class="d-flex">
                            <img src="/assets/img/cgp_logo_green.svg" style="width: 81px; height:80px" alt="" class="" id="logo">
                            <p class="m-0 p-0 text-uppercase mt-n1" style="font-size: 18px; color:rgb(0,87,95)">@yield('title')</p>
                        </div>
                    </div>
                </div>
            </div>
        </header>
    </div>
    <div id="content" style="height:100%">
        <div class="container h-100 mx-auto d-flex flex-column align-items-center justify-content-around">
            @yield('content')
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
        crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
        integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1"
        crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
        crossorigin="anonymous"></script>
<script src="/assets/js/master.js?v={{time()}}" charset="utf-8"></script>
@yield('extra_js')
</body>
</html>
