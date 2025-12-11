<?php

namespace WorkOS\Laravel;

use WorkOS\Laravel\Facades\WorkOS;
use WorkOS\Laravel\Services\WorkOSService;

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

    public function test_facade_resolves_workos_service()
    {
        WorkOS::setFacadeApplication($this->app);

        $this->assertInstanceOf(\WorkOS\UserManagement::class, WorkOS::userManagement());
    }

    public function test_facade_provides_access_to_all_services()
    {
        WorkOS::setFacadeApplication($this->app);

        $this->assertInstanceOf(\WorkOS\AuditLogs::class, WorkOS::auditLogs());
        $this->assertInstanceOf(\WorkOS\DirectorySync::class, WorkOS::directorySync());
        $this->assertInstanceOf(\WorkOS\MFA::class, WorkOS::mfa());
        $this->assertInstanceOf(\WorkOS\Organizations::class, WorkOS::organizations());
        $this->assertInstanceOf(\WorkOS\Portal::class, WorkOS::portal());
        $this->assertInstanceOf(\WorkOS\SSO::class, WorkOS::sso());
        $this->assertInstanceOf(\WorkOS\UserManagement::class, WorkOS::userManagement());
    }
}
