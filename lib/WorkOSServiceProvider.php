<?php

declare(strict_types=1);

namespace WorkOS\Laravel;

use Illuminate\Support\ServiceProvider;
use WorkOS\Credentials;
use WorkOS\WorkloadIdentity\Client as CredentialsClient;
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
            $credentials = $this->resolveCredentials($app, $config['credentials'] ?? null);

            // Credentials take the place of the API key.
            $requiredKeys = $credentials === null ? ['api_key', 'client_id'] : ['client_id'];

            foreach ($requiredKeys as $required) {
                if (empty($config[$required])) {
                    throw new \RuntimeException(
                        "WorkOS is not configured: `workos.{$required}` is missing. "
                        ."Set the corresponding environment variable (e.g. WORKOS_API_KEY, WORKOS_CLIENT_ID) "
                        ."or publish and edit config/workos.php."
                    );
                }
            }

            $args = [
                'clientId' => $config['client_id'],
                'userAgent' => sprintf('%s/%s', Version::SDK_IDENTIFIER, Version::SDK_VERSION),
            ];

            if (! empty($config['api_base_url'])) {
                $args['baseUrl'] = $config['api_base_url'];
            }

            if ($credentials !== null) {
                return CredentialsClient::create($credentials, ...$args);
            }

            return new WorkOS(...$args, apiKey: $config['api_key']);
        });

        // Allows for dependency injection (e.g. `show(WorkOS $workos)`)
        // while still ensuring we're using the configured singleton rather than
        // potentially generating a new, unconfigured version of the singleton
        $this->app->alias('workos', WorkOS::class);
    }

    /**
     * Turn the `workos.credentials` config value into credentials: an
     * instance is used as it is, and a callable is called to make one.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @param  mixed  $configured
     */
    private function resolveCredentials($app, $configured): ?Credentials
    {
        if ($configured === null) {
            return null;
        }

        $credentials = ! $configured instanceof Credentials && $this->isCredentialsFactory($configured)
            ? $app->call($configured)
            : $configured;

        if (! $credentials instanceof Credentials) {
            throw new \RuntimeException(
                'WorkOS is not configured: `workos.credentials` must be a '.Credentials::class
                .' instance or a callable that returns one.'
            );
        }

        return $credentials;
    }

    /**
     * Whether the container can call the value: any PHP callable, a
     * 'Class@method' string, or the name of an invokable class.
     *
     * @param  mixed  $configured
     */
    private function isCredentialsFactory($configured): bool
    {
        if (is_callable($configured)) {
            return true;
        }

        return is_string($configured)
            && (str_contains($configured, '@') || method_exists($configured, '__invoke'));
    }
}
