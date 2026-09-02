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

    public function test_gateway_openapi_marks_private_routes_as_bearer_protected(): void
    {
        $content = file_get_contents(public_path('docs/api.json'));

        $this->assertNotFalse($content);
        $content = preg_replace('/^\xEF\xBB\xBF/', '', $content) ?? $content;

        /** @var array<string, mixed> $body */
        $body = json_decode($content, true, 512, JSON_THROW_ON_ERROR);

        $this->assertSame('http', $body['components']['securitySchemes']['http']['type'] ?? null);
        $this->assertSame('bearer', $body['components']['securitySchemes']['http']['scheme'] ?? null);
        $this->assertSame('JWT', $body['components']['securitySchemes']['http']['bearerFormat'] ?? null);
        $this->assertSame([
            [
                'http' => [],
            ],
        ], $body['security'] ?? null);
        $this->assertSame([], $body['paths']['/v1/auth/login']['post']['security'] ?? null);
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
