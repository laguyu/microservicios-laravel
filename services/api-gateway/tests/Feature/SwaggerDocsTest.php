<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SwaggerDocsTest extends TestCase
{
    public function test_gateway_openapi_documents_post_request_fields(): void
    {
        $response = $this->getJson('/docs/api.json');

        $response->assertOk();

        $body = $response->json();
        $schema = $body['paths']['/v1/auth/register']['post']['requestBody']['content']['application/json']['schema'] ?? [];

        $this->assertSame('object', $schema['type'] ?? null);
        $this->assertArrayHasKey('name', $schema['properties'] ?? []);
        $this->assertArrayHasKey('email', $schema['properties'] ?? []);
        $this->assertArrayHasKey('password', $schema['properties'] ?? []);
    }

    public function test_gateway_forwards_registration_payload(): void
    {
        config()->set('services.microservices.auth', 'https://auth.test');

        Http::preventStrayRequests();
        Http::fake([
            'https://auth.test/api/v1/auth/register' => Http::response([
                'ok' => true,
            ], 201),
        ]);

        $this->postJson('/api/v1/auth/register', [
            'name' => 'Ana García',
            'email' => 'ana@ejemplo.com',
            'password' => 'Secret123',
        ])->assertCreated()->assertJsonPath('ok', true);

        Http::assertSent(fn ($request) => $request->url() === 'https://auth.test/api/v1/auth/register'
            && $request['email'] === 'ana@ejemplo.com');
    }
}
