<?php

namespace WorkOS\Laravel\Models\Concerns;

trait HasWorkOSAuth
{
    public function initializeHasWorkOSAuth()
    {
        // Merge the fillable attributes
        $this->mergeFillable([
            'workos_id',
        ]);

        static::creating(function ($model) {
            if (! $model->password && $model->workos_id) {
                $model->password = null;
            }
        });
    }

    public static function findByWorkOSId(string $workosId)
    {
        return static::where('workos_id', $workosId)->first();
    }

    public function getWorkOSFullNameAttribute()
    {
        return trim(($this->sso_data['firstName'] ?? '').' '.($this->sso_data['lastName'] ?? ''));
    }
}
