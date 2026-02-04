<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{config('app.name')}}</title>

        <link rel="stylesheet" href="{{asset('assets/css/client/master.css?v=')}}{{time()}}">
        <style>
            .anim{
                animation: popOut 1s 1s ease-in-out forwards;
            }

            @keyframes popOut {
                0% {
                    opacity: 0;
                    transform: translateY(100px)
                }
                30%{
                    opacity: 0;
                }
                70%{
                    transform: translateY(-10px)
                }
                100% {
                    opacity: 1;
                    transform: translateY(0)
                }
            }
        </style>
    </head>
    <body class="antialiased bg-white">
        <div class="flex flex-col items-center justify-center min-h-screen w-screen">
            <div
                class="flex flex-col mx-auto w-full items-center md:items-start px-2.5 max-w-[510px] mt-10 md:mt-0">
                <a href="{{route('client.what-is-new.video', ['language_code' => app()->getLocale()])}}">
                    <img
                        class="w-28 pl-3 md:pl-0 anim z-0 cursor-pointer"
                        style="margin-left:-7px; opacity:0;"
                        src="{{asset('assets/img/client/what-is-new/digital-balance/logo.gif')}}"
                        alt="digital-blalance-logo"
                    />
                </a>
                <img  class="w-24 mb-5 z-10" src="{{asset('assets/img/client/what-is-new/digital-balance/cgp_logo.png')}}" alt="logo">
            </div>
            <div class="flex mx-auto w-full justify-center px-2.5 max-w-[837px]">
                <div style="grid-template-columns: repeat(4, minmax(0, 1fr));"
                    class="flex flex-col space-y-2.5 md:grid md:gap-1.5 md:space-y-0 justify-start mb-10">
                    @foreach (config('client-languages') as $code => $name)
                    <a class="cursor-pointer flex justify-center items-center px-20 md:px-10 py-3 md:py-1.5 rounded-xl md:rounded flex-5 bg-black transition-all duration-300 bg-opacity-10 hover:bg-opacity-20 mx-0.5 md:mx-0"
                        href="{{route('client.what-is-new.select-language-process', ['language_code' => $code])}}">
                        <p class="text-base md:text-xs cursor-pointer text-black text-opacity-40">
                            {{$name}}
                        </p>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>
    </body>
</html>
