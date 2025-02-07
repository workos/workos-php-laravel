<?php

namespace WorkOS\Laravel\Models\Concerns;

trait HasWorkOSAuth
{
	public function initializeHasWorkOSAuth()
	{
		$this->fillable = array_merge($this->fillable, [
			'workos_id',
			'organization_id',
			'sso_data',
		]);

		$this->casts = array_merge($this->casts, [
			'sso_data' => 'array',
		]);
	}

	public static function findByWorkOSId(string $workosId)
	{
		return static::where('workos_id', $workosId)->first();
	}
}
