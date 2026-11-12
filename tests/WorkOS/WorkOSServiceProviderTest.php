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

        // Verify the configured values actually reach the underlying HttpClient,
        // so a typo in the provider's constructor arg names would be caught.
        $httpClient = (new \ReflectionClass($client))
            ->getProperty('httpClient')
            ->getValue($client);

        $this->assertSame('pk_secretsauce', $httpClient->getApiKey());
        $this->assertSame('client_pizza', $httpClient->getClientId());

        $baseUrl = (new \ReflectionClass($httpClient))
            ->getProperty('baseUrl')
            ->getValue($httpClient);
        $this->assertSame('https://workos-hop.com/', $baseUrl);
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
        $this->assertInstanceOf(\WorkOS\Service\Agents::class, $client->agents());
        $this->assertInstanceOf(\WorkOS\Service\ApiKeys::class, $client->apiKeys());
        $this->assertInstanceOf(\WorkOS\Service\AuditLogs::class, $client->auditLogs());
        $this->assertInstanceOf(\WorkOS\Service\Authorization::class, $client->authorization());
        $this->assertInstanceOf(\WorkOS\Service\ClientApi::class, $client->clientApi());
        $this->assertInstanceOf(\WorkOS\Service\Connect::class, $client->connect());
        $this->assertInstanceOf(\WorkOS\Service\DirectorySync::class, $client->directorySync());
        $this->assertInstanceOf(\WorkOS\Service\Events::class, $client->events());
        $this->assertInstanceOf(\WorkOS\Service\FeatureFlags::class, $client->featureFlags());
        $this->assertInstanceOf(\WorkOS\Service\Groups::class, $client->groups());
        $this->assertInstanceOf(\WorkOS\Service\MultiFactorAuth::class, $client->multiFactorAuth());
        $this->assertInstanceOf(\WorkOS\Service\OrganizationDomains::class, $client->organizationDomains());
        $this->assertInstanceOf(\WorkOS\Service\OrganizationMembershipService::class, $client->organizationMembership());
        $this->assertInstanceOf(\WorkOS\Service\Organizations::class, $client->organizations());
        $this->assertInstanceOf(\WorkOS\Passwordless::class, $client->passwordless());
        $this->assertInstanceOf(\WorkOS\Service\Pipes::class, $client->pipes());
        $this->assertInstanceOf(\WorkOS\Service\PipesProvider::class, $client->pipesProvider());
        $this->assertInstanceOf(\WorkOS\PKCEHelper::class, $client->pkce());
        $this->assertInstanceOf(\WorkOS\Service\PlatformTeams::class, $client->platformTeams());
        $this->assertInstanceOf(\WorkOS\Service\Radar::class, $client->radar());
        $this->assertInstanceOf(\WorkOS\SessionManager::class, $client->sessionManager());
        $this->assertInstanceOf(\WorkOS\Service\SSO::class, $client->sso());
        $this->assertInstanceOf(\WorkOS\Service\UserManagement::class, $client->userManagement());
        $this->assertInstanceOf(\WorkOS\Service\Vault::class, $client->vault());
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

        $httpClient = (new \ReflectionClass($client))
            ->getProperty('httpClient')
            ->getValue($client);
        $baseUrl = (new \ReflectionClass($httpClient))
            ->getProperty('baseUrl')
            ->getValue($httpClient);

        $this->assertSame('https://custom-api.workos.com/', $baseUrl);
    }

    public function test_user_agent_identifies_laravel_sdk()
    {
        $client = $this->app->make('workos');

        $httpClient = (new \ReflectionClass($client))->getProperty('httpClient')->getValue($client);
        $userAgent = (new \ReflectionClass($httpClient))->getProperty('userAgent')->getValue($httpClient);

        $this->assertSame(
            sprintf('%s/%s', Version::SDK_IDENTIFIER, Version::SDK_VERSION),
            $userAgent
        );
        $this->assertStringStartsWith('WorkOS PHP Laravel/', $userAgent);
    }

    public function test_configured_credentials_authenticate_the_client_instead_of_an_api_key()
    {
        $forms = [
            'an instance' => new StubCredentials(),
            'a closure' => fn () => new StubCredentials(),
            'a static method' => [StubCredentialsFactory::class, 'makeStatic'],
            'Class@method' => StubCredentialsFactory::class.'@make',
            'an invokable class name' => StubCredentialsFactory::class,
        ];

        foreach ($forms as $form => $configured) {
            StubCredentials::$resolved = 0;
            $this->app->forgetInstance('workos');
            // With and without a configured API key: credentials win either way.
            $this->setDefaultConfig([
                'api_key' => $form === 'an instance' ? null : 'pk_test',
                'credentials' => $configured,
            ]);

            $client = $this->app->make('workos');

            $httpClient = (new \ReflectionClass($client))->getProperty('httpClient')->getValue($client);
            $this->assertNull($httpClient->getApiKey(), $form);
            $this->assertSame('client_test', $httpClient->getClientId(), $form);

            try {
                $client->organizations()->getOrganization('org_01');
                $this->fail("Expected the client to resolve the configured credentials ({$form}).");
            } catch (\LogicException $e) {
                $this->assertSame('asked the configured credentials', $e->getMessage(), $form);
            }
            $this->assertSame(1, StubCredentials::$resolved, $form);
        }

        // Anything else is a configuration error, not a silent fall back to the key.
        $this->app->forgetInstance('workos');
        $this->setDefaultConfig(['credentials' => 'not-credentials']);
        try {
            $this->app->make('workos');
            $this->fail('Expected a RuntimeException for an invalid credentials value.');
        } catch (\RuntimeException $e) {
            $this->assertStringContainsString('workos.credentials', $e->getMessage());
        }
    }
}

/**
 * Credentials that fail on resolve, so a test can see the client ask them
 * for its headers without sending a request.
 */
class StubCredentials implements \WorkOS\Credentials
{
    public static int $resolved = 0;

    public function resolve(): \WorkOS\CredentialSnapshot
    {
        self::$resolved++;

        throw new \LogicException('asked the configured credentials');
    }

    public function invalidate(int $generation): void
    {
    }
}

class StubCredentialsFactory
{
    public static function makeStatic(): StubCredentials
    {
        return new StubCredentials();
    }

    public function make(): StubCredentials
    {
        return new StubCredentials();
    }

    public function __invoke(): StubCredentials
    {
        return new StubCredentials();
    }
}
