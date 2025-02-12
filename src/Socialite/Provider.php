<?php

namespace WorkOS\Laravel\Socialite;

use SocialiteProviders\Manager\OAuth2\AbstractProvider;
use SocialiteProviders\Manager\OAuth2\User;

class Provider extends AbstractProvider
{
    public const IDENTIFIER = 'WORKOS';

    /**
     * {@inheritdoc}
     */
    protected $scopes = [
        'openid',
        'profile',
        'email',
    ];

    protected $scopeSeparator = ' ';

    protected $useAuthKit = true;

    /**
     * {@inheritdoc}
     */
    protected function getAuthUrl($state)
    {
        $authUrl = $this->buildAuthUrlFromBase('https://api.workos.com/user_management/authorize', $state);

        if ($this->useAuthKit) {
            $authUrl .= '&provider=authkit';
        }

        return $authUrl;
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl()
    {
        return 'https://api.workos.com/user_management/authenticate';
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token)
    {
        return $this->credentialsResponseBody;
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
        return (new User)->setRaw($user)->map([
            'id' => $user['user']['id'],
            'nickname' => $user['user']['email'] ?? null,
            'name' => trim(($user['user']['first_name'] ?? '').' '.($user['user']['last_name'] ?? '')),
            'email' => $user['user']['email'] ?? null,
            'avatar' => $user['user']['profile_picture_url'] ?? null,
        ]);
    }

    public function withOrganizationId($organizationId)
    {
        $this->useAuthKit = false;

        $this->parameters['organization_id'] = $organizationId;

        return $this;
    }

    public function withConnectionId($connectionId)
    {
        $this->useAuthKit = false;

        $this->parameters['connection_id'] = $connectionId;

        return $this;
    }

    public function withProvider($provider)
    {
        $this->useAuthKit = false;
        $this->parameters['provider'] = $provider;

        return $this;
    }
}
