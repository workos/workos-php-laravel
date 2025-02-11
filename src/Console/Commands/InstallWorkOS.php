<?php

namespace WorkOS\Laravel\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class InstallWorkOS extends Command
{
    protected $signature = 'workos:install';

    protected $description = 'Install the WorkOS package';

    public function handle()
    {
        $this->publishFiles();

        /* $this->updateUserModel(); */

        $this->updateServicesConfig();

        $this->updateEnvironmentFile();

        $this->info('WorkOS starter kit installed successfully! 🚀');
        $this->info('Please add your WorkOS API key and Client ID to your .env file.');
    }

    protected function publishFiles()
    {
        // install provider
        $this->callSilent('vendor:publish', ['--tag' => 'workos-provider']);

        // publish migrations
        $this->callSilent('vendor:publish', ['--tag' => 'workos-migrations']);

        // publish config
        // $this->callSilent('vendor:publish', ['--tag' => 'workos-config']);

        // install views
        // $this->callSilent('vendor:publish', ['--tag' => 'workos-views']);

        // install models
        $this->callSilent('vendor:publish', ['--tag' => 'workos-models']);

        // install routes
        $this->callSilent('vendor:publish', ['--tag' => 'workos-routes']);

        // install controllers
        // $this->callSilent('vendor:publish', ['--tag' => 'workos-controllers']);
    }

    protected function updateUserModel()
    {
        $modelPath = app_path('Models/User.php');
        $content = file_get_contents($modelPath);

        if (! str_contains($content, "'workos_id'")) {
            $content = preg_replace(
                "/(protected\s+\$fillable\s*=\s*\[\s*(?:'|\")\w+(?:'|\")\s*,\s*(?:'|\")\w+(?:'|\")\s*,\s*(?:'|\")\w+(?:'|\")\s*)/",
                "$1,\n        'workos_id'",
                $content
            );
            file_put_contents($modelPath, $content);
        }
    }

    protected function xupdateUserModel()
    {
        $filesystem = new Filesystem;

        if ($filesystem->exists($model = app_path('Models/User.php'))) {
            $filesystem->copy(__DIR__.'../../../subs/User.php', $model);
        }
    }

    protected function updateEnvironmentFile()
    {
        $env = file_get_contents(base_path('.env'));

        if (! str_contains($env, 'WORKOS_API_KEY=')) {
            file_put_contents(base_path('.env'), "\n\nWORKOS_API_KEY=\nWORKOS_CLIENT_ID=\n", FILE_APPEND);
        }
    }

    protected function updateServicesConfig()
    {
        $servicesPath = config_path('services.php');
        $contents = file_get_contents($servicesPath);

        if (! str_contains($contents, "'workos' =>")) {
            $config = "
	'workos' => [
		'client_id' => env('WORKOS_CLIENT_ID'),
		'client_secret' => env('WORKOS_API_KEY'),
		'redirect' => env('WORKOS_REDIRECT_URI'),
	]";
            $contents = str_replace('];', $config."\n];", $contents);
            file_put_contents($servicesPath, $contents);
        }
    }

    protected function updateConfigApp()
    {
        $configPath = config_path('app.php');
        $content = file_get_contents($configPath);

        // Add the provider if it's not already there
        if (! str_contains($content, 'App\\Providers\\WorkOSServiceProvider::class')) {
            $content = str_replace(
                "'providers' => ServiceProvider::defaultProviders()->merge([",
                "'providers' => ServiceProvider::defaultProviders()->merge([\n        App\\Providers\\WorkOSServiceProvider::class,",
                $content
            );
            file_put_contents($configPath, $content);
        }
    }
}
