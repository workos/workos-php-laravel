<?php

namespace WorkOS\Laravel;

use Illuminate\Config\Repository;
use Illuminate\Foundation\Application;

/**
 * Class LaravelTestCase.
 *
 * Chock full of Laravel specific helper functions.
 */
class LaravelTestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * Setup a basic \Illuminate\Foundation\Application
     */
    protected function setupApplication()
    {
        $app = new Application();
        $app->setBasePath(sys_get_temp_dir());
        $app->instance("config", new Repository());

        return $app;
    }

    /**
     * Setup WorkOS Service Providers
     */
    protected function setupProvider($app)
    {
        $provider = new WorkOSServiceProvider($app);
        $app->register($provider);
        $provider->boot();

        return $provider;
    }
}
