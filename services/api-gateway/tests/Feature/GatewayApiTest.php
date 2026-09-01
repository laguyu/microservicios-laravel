<?php

namespace Tests\Feature;

use Tests\TestCase;

class GatewayApiTest extends TestCase
{
    public function test_gateway_health_and_proxy_route_are_available(): void
    {
        $health = $this->getJson('/api/health');

        $health->assertStatus(200)
            ->assertJsonPath('service', 'api-gateway')
            ->assertJsonPath('status', 'healthy');
    }
}
