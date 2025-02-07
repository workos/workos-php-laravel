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
		// publish migrations
		$this->callSilent('vendor:publish', ['--tag' => 'workos-migrations']);

		// publish config
		$this->callSilent('vendor:publish', ['--tag' => 'workos-config']);

		// install views
		$this->callSilent('vendor:publish', ['--tag' => 'workos-views']);

		// install models
		/*$this->callSilent('vendor:publish', ['--tag' => 'workos-models']);*/

		// install routes
		$this->callSilent('vendor:publish', ['--tag' => 'workos-routes']);

		$this->updateUserModel();


		$this->updateEnvironmentFile();

		$this->info('WorkOS starter kit installed successfully! 🚀');
		$this->info('Please add your WorkOS API key and Client ID to your .env file.');
	}

	protected function updateUserModel()
	{
		$filesystem = new Filesystem();

		if ($filesystem->exists($model = app_path('Models/User.php'))) {
			$filesystem->copy(__DIR__.'../../../subs/User.php', $model);
		}
	}

	protected function updateEnvironmentFile()
	{
		$env = file_get_contents(base_path('.env'));

		if (!str_contains($env, 'WORKOS_API_KEY=')) {
			file_put_contents(base_path('.env'), "\n\nWORKOS_API_KEY=\nWORKOS_CLIENT_ID=\n", FILE_APPEND);
		}
	}
}
