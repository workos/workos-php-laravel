<?php

namespace WorkOS\Laravel;

use WorkOS\WorkOS;

class WorkOSServiceProviderTest extends LaravelTestCase
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

    public function test_register_work_os_service_provider_binds_workos_client()
    {
        $this->setDefaultConfig([
            'api_key' => 'pk_secretsauce',
            'client_id' => 'client_pizza',
            'api_base_url' => 'https://workos-hop.com/',
        ]);

        $client = $this->app->make('workos');

        $this->assertInstanceOf(WorkOS::class, $client);
    }

    public function test_workos_helper_function_returns_workos_client_instance()
    {
        $this->assertInstanceOf(WorkOS::class, workos());
    }

    public function test_workos_helper_function_enables_fluent_access()
    {
        $this->assertInstanceOf(\WorkOS\Service\UserManagement::class, workos()->userManagement());
    }

    public function test_it_resolves_client_via_injection()
    {
        $client = $this->app->make(WorkOS::class);

        $this->assertInstanceOf(WorkOS::class, $client);
        $this->assertSame($client, $this->app->make('workos'));
        $this->assertSame($client, workos());
    }

    public function test_workos_client_resolves_all_supported_services()
    {
        $client = workos();

        $this->assertInstanceOf(\WorkOS\Actions::class, $client->actions());
        $this->assertInstanceOf(\WorkOS\Service\AdminPortal::class, $client->adminPortal());
        $this->assertInstanceOf(\WorkOS\Service\ApiKeys::class, $client->apiKeys());
        $this->assertInstanceOf(\WorkOS\Service\AuditLogs::class, $client->auditLogs());
        $this->assertInstanceOf(\WorkOS\Service\Authorization::class, $client->authorization());
        $this->assertInstanceOf(\WorkOS\Service\Connect::class, $client->connect());
        $this->assertInstanceOf(\WorkOS\Service\DirectorySync::class, $client->directorySync());
        $this->assertInstanceOf(\WorkOS\Service\Events::class, $client->events());
        $this->assertInstanceOf(\WorkOS\Service\FeatureFlags::class, $client->featureFlags());
        $this->assertInstanceOf(\WorkOS\Service\MultiFactorAuth::class, $client->multiFactorAuth());
        $this->assertInstanceOf(\WorkOS\Service\OrganizationDomains::class, $client->organizationDomains());
        $this->assertInstanceOf(\WorkOS\Service\Organizations::class, $client->organizations());
        $this->assertInstanceOf(\WorkOS\Passwordless::class, $client->passwordless());
        $this->assertInstanceOf(\WorkOS\Service\Pipes::class, $client->pipes());
        $this->assertInstanceOf(\WorkOS\PKCEHelper::class, $client->pkce());
        $this->assertInstanceOf(\WorkOS\Service\Radar::class, $client->radar());
        $this->assertInstanceOf(\WorkOS\SessionManager::class, $client->sessionManager());
        $this->assertInstanceOf(\WorkOS\Service\SSO::class, $client->sso());
        $this->assertInstanceOf(\WorkOS\Service\UserManagement::class, $client->userManagement());
        $this->assertInstanceOf(\WorkOS\Vault::class, $client->vault());
        $this->assertInstanceOf(\WorkOS\WebhookVerification::class, $client->webhookVerification());
        $this->assertInstanceOf(\WorkOS\Service\Webhooks::class, $client->webhooks());
        $this->assertInstanceOf(\WorkOS\Service\Widgets::class, $client->widgets());
    }

    public function test_workos_client_caches_service_instances()
    {
        $client = workos();

        $userManagement1 = $client->userManagement();
        $userManagement2 = $client->userManagement();

        $this->assertSame($userManagement1, $userManagement2);
    }

    public function test_api_base_url_is_accepted_when_provided()
    {
        $this->setDefaultConfig(['api_base_url' => 'https://custom-api.workos.com/']);

        $client = $this->app->make('workos');

        $this->assertInstanceOf(WorkOS::class, $client);
    }
}
