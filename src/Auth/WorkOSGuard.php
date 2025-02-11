<?php

namespace WorkOS\Laravel\Auth;

use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;

class WorkOSGuard implements Guard
{
    use GuardHelpers;

    public function __construct(UserProvider $provider)
    {
        $this->provider = $provider;
    }

    public function user()
    {
        if (! is_null($this->user)) {
            return $this->user;
        }

        return null;
    }

    public function validate(array $credentials = [])
    {
        return false;
    }
}
