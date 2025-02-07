<?php

return [
    // WorkOS API Key
    "api_key" => env("WORKOS_API_KEY"),

    // WorkOS Client ID
    "client_id" => env("WORKOS_CLIENT_ID"),

    "redirect_uri" => env("WORKOS_REDIRECT_URI", "/auth/workos/callback"),

    // WorkOS base API URL
    "api_base_url" => env("WORKOS_API_BASE_URL"),

    "middleware" => ['web'],

    'routes' => [
        'login' => '/auth/workos',
        'callback' => '/auth/workos/callback',
    ]
];
