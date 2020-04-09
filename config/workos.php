<?php

use WorkOS\Laravel\WorkOSServiceProvider;

return [
    // WorkOS API Key
    "api_key" => env("WORKOS_API_KEY"),

    // WorkOS Project ID
    "project_id" => env("WORKOS_PROJECT_ID"),

    // WorkOS base API URL
    "api_base_url" => null
];
