<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $live_webinar->topic }} – Zoom Webinar</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            background: #f0f0f0;
        }
    </style>
</head>
<body>
    <div id="zmmtg-root"></div>
    <div id="aria-notify-area"></div>
    <div id="live-webinar-start"></div>
    <div id="zoom-error" style="display:none;text-align:center;color:#b91c1c;font-weight:bold;margin-top:1rem;"></div>
    <script id="zoom-start-config" type="application/json">
        {!! json_encode($config, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}
    </script>
    <script src="{{ mix('js/live-webinar-start.js') }}" defer></script>
</body>
</html>
