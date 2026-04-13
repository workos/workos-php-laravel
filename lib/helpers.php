<?php

declare(strict_types=1);

use WorkOS\WorkOS;

if (! function_exists('workos')) {
    /**
     * Access the configured WorkOS client.
     *
     * @return \WorkOS\WorkOS
     */
    function workos()
    {
        return app(WorkOS::class);
    }
}
