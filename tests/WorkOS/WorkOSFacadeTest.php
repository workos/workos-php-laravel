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

        $service = new WorkOSService();
        $reflection = new \ReflectionClass($service);
        $property = $reflection->getProperty('serviceMap');
        $property->setAccessible(true);
        $serviceMap = $property->getValue($service);

        foreach ($serviceMap as $method => $class) {
            $this->assertInstanceOf($class, WorkOS::$method(), "Facade method {$method}() should return an instance of {$class}");
        }
    }
}
