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

    <link rel="stylesheet" href="{{asset('assets/css/client/master.css?v=')}}{{time()}}">

    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="theme-color" content="#ffffff">
    @yield('extra_css')
</head>
<body>
<div class="w-screen bg-cover bg-{{$bg ?? 'login'}} bg-center flex flex-col items-center pb-5" style="min-height: 100vh;">
    <div class="w-screen pt-10 relative sm:pb-0 sm:mb-52">
        <div class="sm:w-4/5 mx-auto" style="max-width: 2060px">
            <x-client.navigation/>
            <div class="hidden sm:block h-7 w-3/5 bg-yellow">

            </div>
            <div class="flex w-full items-end flex flex-col">
                @yield('content')
            </div>
        </div>
    </div>
</div>
<script src="{{asset('js/client/master.js')}}"></script>
</body>
</html>
