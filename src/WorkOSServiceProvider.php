<?php

namespace WorkOS\Laravel;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use WorkOS\Laravel\Auth\WorkOSGuard;
use WorkOS\Laravel\Console\Commands\InstallWorkOS;

/**
 * Class WorkOSServiceProvider.
 */
class WorkOSServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/workos.php', 'workos');

        $config = $this->app['config']->get('workos');
        \WorkOS\WorkOS::setApiKey($config['api_key']);
        \WorkOS\WorkOS::setClientId($config['client_id']);
        \WorkOS\WorkOS::setIdentifier(\WorkOS\Laravel\Version::SDK_IDENTIFIER);
        \WorkOS\WorkOS::setVersion(\WorkOS\Laravel\Version::SDK_VERSION);

        if ($config['api_base_url']) {
            \WorkOS\WorkOS::setApiBaseUrl($config['api_base_url']);
        }

        $this->app['config']->set('auth.guards.workos', [
            'driver' => 'workos',
            'provider' => 'users',
        ]);
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([InstallWorkOS::class]);

            $this->publishes([
                __DIR__.'/../config/workos.php' => config_path('workos.php'),
            ], 'workos-config');

            $migrationFileName = 'add_workos_fields_to_users.php';
            if (! self::migrationFileExists($migrationFileName)) {
                $this->publishes([
                    __DIR__."/../database/migrations/{$migrationFileName}.stub" => database_path('migrations/'.date('Y_m_d_His', time()).'_'.$migrationFileName),
                ], 'workos-migrations');
            }

            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/workos'),
            ], 'workos-views');

            $this->publishes([
                __DIR__.'/../routes' => base_path('routes'),
            ], 'workos-routes');

            $this->publishes([
                __DIR__.'/Http/Controllers' => app_path('Http/Controllers/WorkOS'),
            ], 'workos-controllers');

            $this->publishes([
                __DIR__.'/WorkOSServiceProvider.php' => app_path('Providers/WorkOSServiceProvider.php'),
            ], 'workos-provider');

            /* $this->publishes([ */
            /*    __DIR__.'/../app/Models' => app_path('Models') */
            /* ], 'workos-models'); */
        } elseif (file_exists(base_path('routes/workos.php'))) {
            $this->loadRoutesFrom(base_path('routes/workos.php'));
        }

        /* $this->loadRoutesFrom(base_path('routes'), 'workos'); */
        $this->loadViewsFrom(resource_path(('views/vendor/workos')), 'workos');

        Auth::extend('workos', function ($app, $name, array $config) {
            return new WorkOSGuard(
                $app->make(\WorkOS\WorkOS::class),
                $app['auth']->createUserProvider($config['provider']),
            );
        });
    }

    protected function migrationFileExists(string $migrationFileName)
    {
        $len = strlen($migrationFileName);
        foreach (glob(database_path('migrations/*.php')) as $filename) {
            if ((substr($filename, -$len) === $migrationFileName)) {
                return true;
            }
        }

        return false;
    }
}
