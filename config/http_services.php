<?php
return [
    'path_api_base'=> env('APP_URL'),
    'recaptcha' => env('GOOGLE_KEY_RECAPTCHA_SITE'),
    'post_recaptcha' => env('GOOGLE_URL_RECAPTCHA'),
    'pkey_recaptcha' => env('GOOGLE_KEY_RECAPTCHA_KEY'),
    "google_proxy" => env("GOOGLE_PROXY"),
];
