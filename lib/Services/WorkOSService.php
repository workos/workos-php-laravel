<?php

declare(strict_types=1);

namespace WorkOS\Laravel\Services;

use InvalidArgumentException;
use WorkOS\AuditLogs;
use WorkOS\DirectorySync;
use WorkOS\MFA;
use WorkOS\Organizations;
use WorkOS\Passwordless;
use WorkOS\Portal;
use WorkOS\SSO;
use WorkOS\UserManagement;
use WorkOS\Vault;
use WorkOS\Webhook;
use WorkOS\Widgets;

/**
 * A singleton class that provides a fluent interface for accessing WorkOS services
 * in a Laravel Application.
 *
 * @method \WorkOS\AuditLogs auditLogs()
 * @method \WorkOS\DirectorySync directorySync()
 * @method \WorkOS\MFA mfa()
 * @method \WorkOS\Organizations organizations()
 * @method \WorkOS\Passwordless passwordless()
 * @method \WorkOS\Portal portal()
 * @method \WorkOS\SSO sso()
 * @method \WorkOS\UserManagement userManagement()
 * @method \WorkOS\Vault vault()
 * @method \WorkOS\Webhook webhook()
 * @method \WorkOS\Widgets widgets()
 */
class WorkOSService
{
    /**
     * The array of cached service instances
     *
     * @var array
     */
    private $instances = [];

    /**
     * Map of supported services to their class names
     *
     * @var array
     */
    private $serviceMap = [
        'auditLogs' => AuditLogs::class,
        'directorySync' => DirectorySync::class,
        'mfa' => MFA::class,
        'organizations' => Organizations::class,
        'passwordless' => Passwordless::class,
        'portal' => Portal::class,
        'sso' => SSO::class,
        'userManagement' => UserManagement::class,
        'vault' => Vault::class,
        'webhook' => Webhook::class,
        'widgets' => Widgets::class,
    ];

    /**
     * Dynamically resolve a WorkOS service.
     *
     * @param  string  $name
     * @param  array  $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if (! array_key_exists($name, $this->serviceMap)) {
            throw new InvalidArgumentException("WorkOS service [$name] is not supported.");
        }

        if (isset($this->instances[$name])) {
            return $this->instances[$name];
        }

        return $this->instances[$name] = $arguments ? new $this->serviceMap[$name]($arguments) : new $this->serviceMap[$name];
    }
}
