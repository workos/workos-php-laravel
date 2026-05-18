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

    /**
     * Calls every public instance accessor on \WorkOS\WorkOS through the facade
     * and asserts the returned object matches the upstream method's declared
     * return type. Reflection-driven, so new upstream accessors are picked up
     * automatically — no test list to maintain.
     */
    public function test_facade_proxies_every_upstream_service_accessor()
    {
        WorkOS::setFacadeApplication($this->app);

        $accessors = $this->upstreamAccessors();
        $this->assertNotEmpty($accessors, 'Expected at least one upstream WorkOS accessor.');

        foreach ($accessors as $method => $returnType) {
            $this->assertInstanceOf(
                $returnType,
                WorkOS::$method(),
                "Facade method {$method}() should return an instance of {$returnType}"
            );
        }
    }

    /**
     * @return array<string, class-string> map of accessor name => declared return type
     */
    private function upstreamAccessors(): array
    {
        $ref = new \ReflectionClass(\WorkOS\WorkOS::class);
        $accessors = [];
        foreach ($ref->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            if ($method->isStatic() || $method->isConstructor()) {
                continue;
            }
            if ($method->getDeclaringClass()->getName() !== \WorkOS\WorkOS::class) {
                continue;
            }
            $returnType = $method->getReturnType();
            if (! $returnType instanceof \ReflectionNamedType || $returnType->isBuiltin()) {
                continue;
            }
            $accessors[$method->getName()] = $returnType->getName();
        }

        return $accessors;
    }
}
