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
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],
    'img_model' => [
        'url' => env('IMG_MODEL_URL', 'localhost'),
    ],
    'sensor_model' => [
        'url' => env('SENSOR_MODEL_URL', 'localhost'),
    ],
    'antares' => [
        'url' => env('ANTARES_URL'),
        'controller' => env('ANTARES_DEVICE_CONTROLLER'),
        'access_key' => env('ANTARES_ACCESS_KEY'),
    ],
    'device' => [
        'nyala' => env('RELAY_NYALA'),
        'mati' => env('RELAY_MATI'),
        'pupuk' => env('TIPE_PUPUK'),
        'air' => env('TIPE_AIR'),
    ],
    'imagekit' =>[
        'private' => env('IMAGEKIT_PRIVATE'),
        'public' => env('IMAGEKIT_PUBLIC'),
        'url' => env('IMAGEKIT_URL'),
    ]
];
