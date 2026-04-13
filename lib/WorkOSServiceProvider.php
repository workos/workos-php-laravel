<?php

declare(strict_types=1);

namespace WorkOS\Laravel;

use Illuminate\Support\ServiceProvider;
use WorkOS\WorkOS;

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

        // Ensures that the WorkOS client is configured only once, rather than every request
        $this->app->singleton('workos', function ($app) {
            $config = $app['config']->get('workos');

            foreach (['api_key', 'client_id'] as $required) {
                if (empty($config[$required])) {
                    throw new \RuntimeException(
                        "WorkOS is not configured: `workos.{$required}` is missing. "
                        ."Set the corresponding environment variable (e.g. WORKOS_API_KEY, WORKOS_CLIENT_ID) "
                        ."or publish and edit config/workos.php."
                    );
                }
            }

            $args = [
                'apiKey' => $config['api_key'],
                'clientId' => $config['client_id'],
                'userAgent' => sprintf('%s/%s', Version::SDK_IDENTIFIER, Version::SDK_VERSION),
            ];

            if (! empty($config['api_base_url'])) {
                $args['baseUrl'] = $config['api_base_url'];
            }

            return new WorkOS(...$args);
        });

        // Allows for dependency injection (e.g. `show(WorkOS $workos)`)
        // while still ensuring we're using the configured singleton rather than
        // potentially generating a new, unconfigured version of the singleton
        $this->app->alias('workos', WorkOS::class);
    }
}
