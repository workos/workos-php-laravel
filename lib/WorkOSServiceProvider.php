<?php

declare(strict_types=1);

namespace WorkOS\Laravel;

use Illuminate\Support\ServiceProvider;
use WorkOS\Laravel\Services\WorkOSService;

/**
 * Class WorkOSServiceProvider.
 */
class WorkOSServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the ServiceProvider.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes(
                [__DIR__.'/../config/workos.php' => config_path('workos.php')],
                'workos-config'
            );
        }
    }

    /**
     * Register the ServiceProvider as well as setup WorkOS.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/workos.php', 'workos');

        // Ensures that the WorkOS service is configured only once, rather than every request
        $this->app->singleton('workos', function ($app) {
            $config = $app['config']->get('workos');

            \WorkOS\WorkOS::setApiKey($config['api_key']);
            \WorkOS\WorkOS::setClientId($config['client_id']);
            \WorkOS\WorkOS::setIdentifier(Version::SDK_IDENTIFIER);
            \WorkOS\WorkOS::setVersion(Version::SDK_VERSION);

            if ($config['api_base_url']) {
                \WorkOS\WorkOS::setApiBaseUrl($config['api_base_url']);
            }

            return new WorkOSService;
        });

        // Allows for dependency injection (e.g. `show(WorkOSService $service)`)
        // while still ensuring we're using the configured singleton rather than
        // potentially generating a new, unconfigured version of the singleton
        $this->app->alias('workos', WorkOSService::class);
    }
}
