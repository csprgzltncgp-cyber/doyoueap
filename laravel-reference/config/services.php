<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
     */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'exchangertates' => [
        'api' => env('EXCHANGERATES_KEY', ''),
    ],

    'pusher' => [
        'eap-chat' => [
            'beams_instance_id' => env('PUSHER_BEAMS_INSTANCE_ID_EAP_CHAT'),
            'beams_secret_key' => env('PUSHER_BEAMS_SECRET_KEY_EAP_CHAT'),
        ],
        'my-eap' => [
            'beams_instance_id' => env('PUSHER_BEAMS_INSTANCE_ID_MY_EAP'),
            'beams_secret_key' => env('PUSHER_BEAMS_SECRET_KEY_MY_EAP'),
        ],
    ],

    'vonage' => [
        'key' => env('VONAGE_KEY'),
        'secret' => env('VONAGE_SECRET'),
        'sms_from' => env('VONAGE_SMS_FROM'),
    ],

    'zoom' => [
        'base_url' => env('ZOOM_BASE_URL', 'https://api.zoom.us/v2'),
        'sdk_key' => env('ZOOM_SDK_KEY'),
        'sdk_secret' => env('ZOOM_SDK_SECRET'),
        'account_id' => env('ZOOM_ACCOUNT_ID'),
        'client_id' => env('ZOOM_CLIENT_ID'),
        'client_secret' => env('ZOOM_CLIENT_SECRET'),
        'sdk_log_level' => env('ZOOM_SDK_LOG_LEVEL', 'info'),
        'live_webinar_template_id' => env('ZOOM_LIVE_WEBINAR_TEMPLATE_ID'),
    ],

    'vimeo' => [
        'upload_options' => [
            'privacy' => array_filter([
                'view' => env('VIMEO_PRIVACY_VIEW'),
                'embed' => env('VIMEO_PRIVACY_EMBED'),
            ]),
        ],
    ],
];
