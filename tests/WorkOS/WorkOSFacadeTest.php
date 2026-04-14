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

    public function test_facade_covers_every_upstream_service_accessor()
    {
        // Drift guard: if workos/workos-php adds a new service accessor in a
        // future minor bump (our composer constraint is ^5.0.1), this test
        // fails so we remember to add it to the @method docblock on the
        // facade and to the expected-services list above.
        $ref = new \ReflectionClass(\WorkOS\WorkOS::class);
        $upstreamAccessors = [];
        foreach ($ref->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            if ($method->isStatic() || $method->isConstructor()) {
                continue;
            }
            if ($method->getDeclaringClass()->getName() !== \WorkOS\WorkOS::class) {
                continue;
            }
            $upstreamAccessors[] = $method->getName();
        }

        $covered = [
            'actions', 'adminPortal', 'apiKeys', 'auditLogs', 'authorization',
            'connect', 'directorySync', 'events', 'featureFlags', 'multiFactorAuth',
            'organizationDomains', 'organizations', 'passwordless', 'pipes', 'pkce',
            'radar', 'sessionManager', 'sso', 'userManagement', 'vault',
            'webhookVerification', 'webhooks', 'widgets',
        ];

        $missing = array_diff($upstreamAccessors, $covered);
        $this->assertEmpty(
            $missing,
            'Upstream \\WorkOS\\WorkOS exposes service accessor(s) not covered by this '
            .'package: '.implode(', ', $missing).'. Add them to the @method docblock on '
            .'WorkOS\\Laravel\\Facades\\WorkOS and to the expected-services test lists.'
        );
    }
}
