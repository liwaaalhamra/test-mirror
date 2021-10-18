<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, Mandrill, and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'ses' => [
        'key'    => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => 'us-east-1',
    ],

    'arabs' => [
        'base_url'    => env('ARABS_PAYMENT_BASE_URL'),
        'username' => env('ARABS_PAYMENT_USERNAME'),
        'password' => env('ARABS_PAYMENT_PASSWORD'),
    ],
    'restaurant_call_minutes' => env('RESTAURANT_CALL_MINUTES'),
    'supplier_call_minutes' => env('SUPPLIER_CALL_MINUTES'),
    'restaurant_rejection_minutes' => env('RESTAURANT_REJECTION_MINUTES'),
    'supplier_rejection_minutes' => env('SUPPLIER_REJECTION_MINUTES'),
    'restaurant_delivery_minutes' => env('RESTAURANT_DELIVERY_MINUTES'),
    'supplier_delivery_minutes' => env('SUPPLIER_DELIVERY_MINUTES'),

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT'),
    ],

    'facebook' => [
        'client_id' => env('FACEBOOK_CLIENT_ID'),
        'client_secret' => env('FACEBOOK_CLIENT_SECRET'),
        'redirect' => env('FACEBOOK_REDIRECT'),
    ],

];
