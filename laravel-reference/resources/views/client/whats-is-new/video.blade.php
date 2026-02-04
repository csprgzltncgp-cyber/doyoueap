<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{config('app.name')}}</title>

        <link rel="stylesheet" href="{{asset('assets/css/client/master.css?v=')}}{{time()}}">
    </head>
    <body class="antialiased bg-white">
        <div class="flex flex-col justify-center space-y-5 m-auto max-w-[1160px] min-h-screen">
            <div class="flex max-w-[1180px] mx-5 lg:mx-10">
                    <img class="h-28 mb-1.5" src="{{asset('assets/img/logo_black.svg')}}" alt="eaphu_logo">
            </div>
            <div class="flex justify-between mx-5 lg:mt-28">
                <div class="flex-1 w-full lg:mx-5 gap-14 flex flex-col">
                    <video
                        playsinline autoplay muted controls
                        class="rounded-xl w-full bg-white"
                        style="box-shadow: 0 0 10px rgba(0,0,0,0.2);"
                        x-on:ended="replayDisabled=false;"
                    >
                        <source src="{{asset('assets/img/client/what-is-new/digital-balance/video.mp4')}}" type="video/mp4">
                    </video>
                </div>
            </div>
        </div>

        <script>
            var video = document.getElementsByTagName('video')[0];

            video.onended = function(e) {
                setTimeout(() => {
                    window.location.href = "{{route('client.what-is-new.contact', ['language_code' => $language_code, 'lang' => $language_code])}}";
                }, 700);
            };
        </script>
    </body>
</html>
