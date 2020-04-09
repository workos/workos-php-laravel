<?php

namespace WorkOS\Laravel;

class WorkOSServiceProviderTest extends LaravelTestCase
{
    protected $app;

    protected function setUp()
    {
        $this->app = $this->setupApplication();
    }
    
    public function testRegisterWorkOSServiceProviderYieldsExpectedConfig()
    {
        $this->app["config"]->set("workos.api_key", "pk_secretsauce");
        $this->app["config"]->set("workos.project_id", "project_pizza");
        $this->app["config"]->set("workos.api_base_url", "https://workos-hop.com/");
        $this->setupProvider($this->app);
        
        $this->assertEquals("pk_secretsauce", \WorkOS\WorkOS::getApiKey());
        $this->assertEquals("project_pizza", \WorkOS\WorkOS::getProjectId());
        $this->assertEquals("https://workos-hop.com/", \WorkOS\WorkOS::getApiBaseUrl());
    }
}
