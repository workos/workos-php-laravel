<?php

declare(strict_types=1);

use WorkOS\Laravel\Services\WorkOSService;

if (! function_exists('workos')) {
    /**
     * Access the WorkOS Manager.
     *
     * @return \WorkOS\Laravel\WorkOSManager
     */
    function workos()
    {
        return app(WorkOSService::class);
    }
}
