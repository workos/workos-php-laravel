<?php

declare(strict_types=1);

namespace WorkOS\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \WorkOS\Actions actions()
 * @method static \WorkOS\Service\AdminPortal adminPortal()
 * @method static \WorkOS\Service\ApiKeys apiKeys()
 * @method static \WorkOS\Service\AuditLogs auditLogs()
 * @method static \WorkOS\Service\Authorization authorization()
 * @method static \WorkOS\Service\Connect connect()
 * @method static \WorkOS\Service\DirectorySync directorySync()
 * @method static \WorkOS\Service\Events events()
 * @method static \WorkOS\Service\FeatureFlags featureFlags()
 * @method static \WorkOS\Service\MultiFactorAuth multiFactorAuth()
 * @method static \WorkOS\Service\OrganizationDomains organizationDomains()
 * @method static \WorkOS\Service\Organizations organizations()
 * @method static \WorkOS\Passwordless passwordless()
 * @method static \WorkOS\Service\Pipes pipes()
 * @method static \WorkOS\PKCEHelper pkce()
 * @method static \WorkOS\Service\Radar radar()
 * @method static \WorkOS\SessionManager sessionManager()
 * @method static \WorkOS\Service\SSO sso()
 * @method static \WorkOS\Service\UserManagement userManagement()
 * @method static \WorkOS\Vault vault()
 * @method static \WorkOS\WebhookVerification webhookVerification()
 * @method static \WorkOS\Service\Webhooks webhooks()
 * @method static \WorkOS\Service\Widgets widgets()
 *
 * @see \WorkOS\WorkOS
 */
class WorkOS extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'workos';
    }
}
