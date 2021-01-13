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
        $this->app["config"]->set("workos.client_id", "client_pizza");
        $this->app["config"]->set("workos.api_base_url", "https://workos-hop.com/");
        $this->setupProvider($this->app);
        
        $this->assertEquals("pk_secretsauce", \WorkOS\WorkOS::getApiKey());
        $this->assertEquals("client_pizza", \WorkOS\WorkOS::getClientId());
        $this->assertEquals("https://workos-hop.com/", \WorkOS\WorkOS::getApiBaseUrl());
    }
}
