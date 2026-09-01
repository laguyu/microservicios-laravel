<?php

namespace Tests\Feature;

use Tests\TestCase;

class SwaggerDocsTest extends TestCase
{
    public function test_users_swagger_routes_have_explicit_summaries(): void
    {
        $response = $this->getJson('/docs/api.json');

        $response->assertOk();

        $body = $response->json();

        $this->assertNotSame('', $body['paths']['/v1/clients']['get']['summary'] ?? '');
        $this->assertNotSame('', $body['paths']['/v1/clients']['post']['summary'] ?? '');
        $this->assertNotSame('', $body['paths']['/v1/projects']['post']['summary'] ?? '');
        $this->assertNotSame('', $body['paths']['/v1/tasks']['post']['summary'] ?? '');
    }
}
