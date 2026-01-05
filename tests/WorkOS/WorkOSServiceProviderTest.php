<?php

namespace WorkOS\Laravel;

use WorkOS\Laravel\Services\WorkOSService;

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

    public function test_register_work_os_service_provider_yields_expected_config()
    {
        $this->setDefaultConfig([
            'api_key' => 'pk_secretsauce',
            'client_id' => 'client_pizza',
            'api_base_url' => 'https://workos-hop.com/',
        ]);

        // Resolve the service to trigger lazy initialization
        $this->app->make('workos');

        $this->assertEquals('pk_secretsauce', \WorkOS\WorkOS::getApiKey());
        $this->assertEquals('client_pizza', \WorkOS\WorkOS::getClientId());
        $this->assertEquals('https://workos-hop.com/', \WorkOS\WorkOS::getApiBaseUrl());
    }

    public function test_workos_helper_function_returns_work_os_service_instance()
    {
        $this->assertInstanceOf(WorkOSService::class, workos());
    }

    public function test_workos_helper_function_enables_fluent_access()
    {
        $this->assertInstanceOf(\WorkOS\UserManagement::class, workos()->userManagement());
    }

    public function test_it_resolves_service_via_injection_and_configures_sdk()
    {
        $service = $this->app->make(WorkOSService::class);

        $this->assertInstanceOf(WorkOSService::class, $service);
        $this->assertSame($service, $this->app->make('workos'));
        $this->assertSame($service, workos());
    }

    public function test_workos_service_resolves_all_supported_services()
    {
        $service = workos();

        $this->assertInstanceOf(\WorkOS\AuditLogs::class, $service->auditLogs());
        $this->assertInstanceOf(\WorkOS\DirectorySync::class, $service->directorySync());
        $this->assertInstanceOf(\WorkOS\MFA::class, $service->mfa());
        $this->assertInstanceOf(\WorkOS\Organizations::class, $service->organizations());
        $this->assertInstanceOf(\WorkOS\Passwordless::class, $service->passwordless());
        $this->assertInstanceOf(\WorkOS\Portal::class, $service->portal());
        $this->assertInstanceOf(\WorkOS\SSO::class, $service->sso());
        $this->assertInstanceOf(\WorkOS\UserManagement::class, $service->userManagement());
        $this->assertInstanceOf(\WorkOS\Vault::class, $service->vault());
        $this->assertInstanceOf(\WorkOS\Webhook::class, $service->webhook());
        $this->assertInstanceOf(\WorkOS\Widgets::class, $service->widgets());
    }

    public function test_workos_service_caches_service_instances()
    {
        $service = workos();

        $userManagement1 = $service->userManagement();
        $userManagement2 = $service->userManagement();

        $this->assertSame($userManagement1, $userManagement2);
    }

    public function test_workos_service_throws_exception_for_unsupported_service()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('WorkOS service [unsupportedService] is not supported.');

        $service = workos();
        $service->unsupportedService();
    }

    public function test_api_base_url_is_set_when_provided()
    {
        $this->setDefaultConfig(['api_base_url' => 'https://custom-api.workos.com/']);

        $this->app->make('workos');

        $this->assertEquals('https://custom-api.workos.com/', \WorkOS\WorkOS::getApiBaseUrl());
    }

    public function test_service_map_includes_all_available_workos_services()
    {
        // Discover available services from the WorkOS PHP SDK
        $libPath = __DIR__ . '/../../vendor/workos/workos-php/lib';
        $availableServices = [];

        if (!is_dir($libPath)) {
            $this->markTestSkipped('WorkOS PHP SDK vendor directory not found at: ' . $libPath);
        }

        foreach (glob($libPath . '/*.php') as $file) {
            $className = basename($file, '.php');

            // Skip utility classes that aren't services
            if (in_array($className, ['WorkOS', 'Client', 'Version', 'Resource'])) {
                continue;
            }

            // Convert class name to camelCase method name
            // Special handling for acronyms: MFA -> mfa, SSO -> sso
            // Note: Str::camel() doesn't work for all-caps (MFA -> mFA, not mfa)
            if (ctype_upper($className)) {
                $methodName = strtolower($className);
            } else {
                $methodName = lcfirst($className);
            }
            $availableServices[$methodName] = "WorkOS\\{$className}";
        }

        // Get the actual service map from WorkOSService using reflection
        $service = workos();
        $reflection = new \ReflectionClass($service);
        $property = $reflection->getProperty('serviceMap');
        $property->setAccessible(true);
        $actualServiceMap = $property->getValue($service);

        // Compare available services with mapped services
        $missingServices = array_diff_key($availableServices, $actualServiceMap);
        $extraServices = array_diff_key($actualServiceMap, $availableServices);

        $errorMessage = '';
        if (!empty($missingServices)) {
            $errorMessage .= 'Missing services in WorkOSService::$serviceMap: ' . implode(', ', array_keys($missingServices)) . '. ';
        }
        if (!empty($extraServices)) {
            $errorMessage .= 'Extra services in WorkOSService::$serviceMap: ' . implode(', ', array_keys($extraServices)) . '.';
        }

        $this->assertEmpty($missingServices + $extraServices, $errorMessage);
        $this->assertEquals($availableServices, $actualServiceMap, 'Service map should match available WorkOS services');
    }
}
