<?php

namespace WorkOS\Laravel;

use Illuminate\Support\ServiceProvider;

/**
 * Class WorkOSServiceProvider.
 */
class WorkOSServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the ServiceProvider.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes(
                [__DIR__."/../config/workos.php" => config_path("workos.php")]
            );
        }
    }

    /**
     * Register the ServiceProvider as well as setup WorkOS.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__."/../config/workos.php", "workos");

        $config = $this->app["config"]->get("workos");
        \WorkOS\WorkOS::setApiKey($config["api_key"]);
        \WorkOS\WorkOS::setProjectId($config["project_id"]);
        \WorkOS\WorkOS::setIdentifier(\WorkOS\Laravel\Version::SDK_IDENTIFIER);
        \WorkOS\WorkOS::setVersion(\WorkOS\Laravel\Version::SDK_VERSION);

        if ($config["api_base_url"]) {
            \WorkOS\WorkOS::setApiBaseUrl($config["api_base_url"]);
        }
    }
}
