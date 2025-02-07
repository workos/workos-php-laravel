<?php

namespace WorkOS\Laravel\Models\Concerns;

trait HasWorkOSAuth
{
	public function initializeHasWorkOSAuth()
	{
		/*$this->fillable = array_merge($this->fillable, [*/
		/*	'workos_id',*/
		/*	'organization_id',*/
		/*	'sso_data',*/
		/*]);*/
		/**/
		/*$this->casts = array_merge($this->casts, [*/
		/*	'sso_data' => 'array',*/
		/*]);*/

        // Merge the fillable attributes
        $this->mergeFillable([
            'workos_id',
            'organization_id',
            'sso_data',
        ]);

        // Merge the casts
        $this->mergeCasts([
            'sso_data' => 'array',
        ]);
	}

	public static function findByWorkOSId(string $workosId)
	{
		return static::where('workos_id', $workosId)->first();
	}

	public function updateWorkOSData($workosUser)
	{
		return $this->update([
			'sso_data' => $workosUser->toArray()
		]);
	}

	public function getWorkOSFullNameAttribute()
	{
		return trim(($this->sso_data['firstName'] ?? '').' '.($this->sso_data['lastName'] ?? ''));
	}

	public function belongsToOrganization(string $organizationId)
	{
		return $this->organization_id === $organizationId;
	}
}
