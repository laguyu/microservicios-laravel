<?php

namespace Tests\Feature;

use App\Support\JwtToken;
use Illuminate\Support\Facades\Http;
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

    public function test_gateway_blocks_private_routes_without_token(): void
    {
        $this->getJson('/api/v1/dashboard')
            ->assertStatus(401)
            ->assertJsonPath('ok', false);
    }

    public function test_gateway_allows_private_routes_with_valid_token(): void
    {
        config()->set('app.jwt_secret', 'testing-secret');
        config()->set('app.jwt_algorithm', 'HS256');
        config()->set('services.microservices.users', 'https://users.test');

        Http::preventStrayRequests();
        Http::fake([
            'https://users.test/api/v1/dashboard' => Http::response([
                'ok' => true,
                'data' => [
                    'metrics' => [
                        'clients' => 1,
                    ],
                ],
            ], 200),
        ]);

        $token = JwtToken::issue(1, 'ana@example.com');

        $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->getJson('/api/v1/dashboard')
            ->assertStatus(200)
            ->assertJsonPath('ok', true)
            ->assertJsonPath('data.metrics.clients', 1);
    }
}
