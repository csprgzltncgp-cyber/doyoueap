
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{config('app.name')}}</title>

        <link rel="stylesheet" href="{{asset('assets/css/client/master.css?v=')}}{{time()}}">
    </head>
    <body class="antialiased bg-white">
        <div class="flex flex-col justify-center space-y-5 m-auto my-10 sm:my-0 max-w-[1160px] min-h-screen">
            <div class="flex max-w-[1180px] mx-5 lg:mx-10">
                    <img class="h-28 mb-1.5" src="{{asset('assets/img/logo_black.svg')}}" alt="eaphu_logo">
            </div>
            <div class="mx-auto flex flex-col sm:flex-row justify-center gap-10 lg:mt-28">
                <img class="w-[248px] h-[248px] sm:w-[348px] sm:h-[348px]" src="{{asset('assets/img/client/what-is-new/digital-balance/logo.gif')}}" />

                <div class="flex flex-col gap-5 justify-between max-w-[270px]">
                    <h1 class="text-3xl font-bold font-oswald uppercase">
                        {{__('what-is-new.headline')}}
                    </h1>
                    <p>
                        <span class="font-bold">{{__('what-is-new.contact')}}</span>
                        <a href="mailto:{{$email}}">{{$email}}</a>
                    </p>

                    <div class="flex flex-col gap-5">
                        <a
                            href="{{route('client.what-is-new.video', ['language_code' => $language_code])}}"
                            class="text-center disabled:opacity-50 disabled:pointer-events-none transition duration-300 ease-in-out border-2 border-[#1000c3] rounded-full px-10 py-2 uppercase font-bold hover:bg-[#1000c3] hover:bg-opacity-20 text-[#1000c3]">
                            {{__('what-is-new.watch_video')}}
                        </a>

                        <a
                            @guest
                            href="{{route('client.login',  ['lang' => $language_code])}}"
                            @else
                            href="{{route('client.riport.show', ['totalView' => 1])}}"
                            @endguest
                            class="text-center disabled:opacity-50 disabled:pointer-events-none transition duration-300 ease-in-out border-2 border-[#1000c3] rounded-full px-10 py-2 uppercase font-bold hover:bg-[#1000c3] hover:bg-opacity-20 text-[#1000c3]">
                            {{__('what-is-new.go_to_reports')}}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
