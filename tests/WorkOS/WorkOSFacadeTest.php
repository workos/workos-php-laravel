<?php

namespace WorkOS\Laravel;

use WorkOS\Laravel\Facades\WorkOS;

class WorkOSFacadeTest extends LaravelTestCase
{
    protected $app;

    protected function setUp(): void
    {
        $this->app = $this->setupApplication();
        $this->setDefaultConfig();
        $this->setupProvider($this->app);
    }

    protected function setDefaultConfig(array $overrides = []): void
    {
        $defaults = [
            'api_key' => 'pk_test',
            'client_id' => 'client_test',
        ];

        foreach (array_merge($defaults, $overrides) as $key => $value) {
            $this->app['config']->set("workos.{$key}", $value);
        }
    }

    public function test_facade_resolves_workos_client()
    {
        WorkOS::setFacadeApplication($this->app);

        $this->assertInstanceOf(\WorkOS\Service\UserManagement::class, WorkOS::userManagement());
    }

    public function test_facade_provides_access_to_all_services()
    {
        WorkOS::setFacadeApplication($this->app);

        $expectedServices = [
            'adminPortal' => \WorkOS\Service\AdminPortal::class,
            'apiKeys' => \WorkOS\Service\ApiKeys::class,
            'auditLogs' => \WorkOS\Service\AuditLogs::class,
            'authorization' => \WorkOS\Service\Authorization::class,
            'connect' => \WorkOS\Service\Connect::class,
            'directorySync' => \WorkOS\Service\DirectorySync::class,
            'events' => \WorkOS\Service\Events::class,
            'featureFlags' => \WorkOS\Service\FeatureFlags::class,
            'multiFactorAuth' => \WorkOS\Service\MultiFactorAuth::class,
            'organizationDomains' => \WorkOS\Service\OrganizationDomains::class,
            'organizations' => \WorkOS\Service\Organizations::class,
            'passwordless' => \WorkOS\Passwordless::class,
            'pipes' => \WorkOS\Service\Pipes::class,
            'pkce' => \WorkOS\PKCEHelper::class,
            'radar' => \WorkOS\Service\Radar::class,
            'sessionManager' => \WorkOS\SessionManager::class,
            'sso' => \WorkOS\Service\SSO::class,
            'userManagement' => \WorkOS\Service\UserManagement::class,
            'vault' => \WorkOS\Vault::class,
            'webhookVerification' => \WorkOS\WebhookVerification::class,
            'webhooks' => \WorkOS\Service\Webhooks::class,
            'widgets' => \WorkOS\Service\Widgets::class,
            'actions' => \WorkOS\Actions::class,
        ];

        foreach ($expectedServices as $method => $class) {
            $this->assertInstanceOf($class, WorkOS::$method(), "Facade method {$method}() should return an instance of {$class}");
        }
    }
}
