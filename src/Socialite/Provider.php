<?php

namespace WorkOS\Laravel\Socialite;

use Laravel\Socialite\Two\InvalidStateException;
use SocialiteProviders\Manager\OAuth2\AbstractProvider;
use SocialiteProviders\Manager\OAuth2\User;
use WorkOS\UserManagement;

class Provider extends AbstractProvider
{
    public const IDENTIFIER = 'WORKOS';

    protected $scopes = ['openid', 'profile', 'email'];

    protected $scopeSeparator = ' ';

    protected $useAuthkit = true;

    protected UserManagement $userManagement;

    protected $rawResponse;

    protected $paramaters = [];

    public function __construct(...$args)
    {
        parent::__construct(...$args);

        $this->userManagement = new UserManagement;
    }

    protected function getAuthUrl($state)
    {
        return $this->userManagement->getAuthorizationUrl(
            redirectUri: $this->redirectUrl,
            state: ['state' => $state],
            provider: $this->useAuthkit ? 'authkit' : null,
            connectionId: $this->paramaters['connection_id'] ?? null,
            organizationId: $this->paramaters['organization_id'] ?? null
        );
    }

    protected function getTokenUrl()
    {
        // FIXME: is this required?
        return '';
    }

    public function user()
    {
        if ($this->user) {
            return $this->user;
        }

        if ($this->hasInvalidState()) {
            throw new InvalidStateException;
        }

        $response = $this->userManagement->authenticateWithCode(clientId: $this->clientId, code: $this->getCode());

        $this->rawResponse = $response;

        $user = $this->mapUserToObject($response->raw['user']);

        return $user;
    }

    protected function getUserByToken($token)
    {
        $res = $this->userManagement->authenticateWithCode(clientId: $this->clientId, code: $token);

        return $res;
    }

    protected function mapUserToObject(array $user)
    {
        return (new User)->setRaw($user)->map([
            'id' => $user['id'],
            'nickname' => $user['email'] ?? null,
            'name' => trim(($user['first_name'] ?? '').' '.($user['last_name'] ?? '')),
            'email' => $user['email'] ?? null,
            'avatar' => $user['profile_picture_url'] ?? null,
            'organization_id' => $user['organization_id'] ?? null,
        ]);
    }

    protected function getCodeFields($state = null)
    {
        $fields = parent::getCodeFields($state);

        dd($fields);

        return array_merge($fields, $this->paramaters);
    }

    public function withOrganizationId($organizationId)
    {
        $this->useAuthkit = false;
        $this->paramaters['organization_id'] = $organizationId;

        return $this;
    }

    public function withConnectionId($connectionId)
    {
        $this->useAuthkit = false;
        $this->paramaters['connection_id'] = $connectionId;

        return $this;
    }

    public function withProvider($provider)
    {
        $this->useAuthkit = false;
        $this->paramaters['provider'] = $provider;

        return $this;
    }
}
