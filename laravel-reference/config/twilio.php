<?php

return [
    'twilio' => [
        'sid' => env('TWILIO_ACCOUNT_SID'),
        'auth_token' => env('TWILIO_AUTH_TOKEN'),
        'key' => env('TWILIO_API_KEY_SID'),
        'secret' => env('TWILIO_API_KEY_SECRET'),
    ],
];
