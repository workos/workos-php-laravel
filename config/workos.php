<?php

return [
    // WorkOS API Key
    "api_key" => env("WORKOS_API_KEY"),

    // WorkOS Client ID
    "client_id" => env("WORKOS_CLIENT_ID"),

    // WorkOS base API URL
    "api_base_url" => null,

    // Credentials to authenticate with instead of the API key, such as
    // \WorkOS\WorkloadIdentity\Credentials. Either a \WorkOS\Credentials
    // instance or a callable that returns one. An object or a closure breaks
    // `php artisan config:cache`; these forms do not: a static method
    // ([SomeClass::class, 'make']), 'SomeClass@make', or the name of an
    // invokable class. The last two are built by the container.
    // When this is set, api_key is not required and is never sent.
    "credentials" => null
];
