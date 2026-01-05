<?php

declare(strict_types=1);

namespace WorkOS\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \WorkOS\AuditLogs auditLogs()
 * @method static \WorkOS\DirectorySync directorySync()
 * @method static \WorkOS\MFA mfa()
 * @method static \WorkOS\Organizations organizations()
 * @method static \WorkOS\Passwordless passwordless()
 * @method static \WorkOS\Portal portal()
 * @method static \WorkOS\SSO sso()
 * @method static \WorkOS\UserManagement userManagement()
 * @method static \WorkOS\Vault vault()
 * @method static \WorkOS\Webhook webhook()
 * @method static \WorkOS\Widgets widgets()
 *
 * @see \WorkOS\Laravel\WorkOSService
 */
class WorkOS extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'workos';
    }
}
