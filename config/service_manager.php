<?php

return [
    "template_id" => env('SERVICE_MANAGER_TEMPLATE_ID'),
    "api_base" => env('SERVICE_MANAGER_BASE_API'),
    "api" => [
        "auth" => env('SERVICE_MANAGER_AUTH'),
        "token" => env('SERVICE_MANAGER_TOKEN'),
        "template" => env('SERVICE_MANAGER_TEMPLATE'),
        "create" => env('SERVICE_MANAGER_CREATE'),
        "file" => env('SERVICE_MANAGER_FILE'),
        "save" => env('SERVICE_MANAGER_SAVE')
    ],
    "credential"=> [
        "username" => env('SERVICE_MANAGER_USER'),
        "password" => env('SERVICE_MANAGER_PASS')
    ],
    "mail" => [
        "default" => env('MAIL_TESTER_DEFAULT')
    ],
    "mail_generic" => env("SERVICE_MANAGER_USER_DEFAULT")

];
