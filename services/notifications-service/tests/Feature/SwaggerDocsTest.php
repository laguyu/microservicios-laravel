<?php

namespace Tests\Feature;

use Tests\TestCase;

class SwaggerDocsTest extends TestCase
{
    public function test_notifications_swagger_routes_have_explicit_summaries(): void
    {
        $response = $this->getJson('/docs/api.json');

        $response->assertOk();

        $body = $response->json();

        $this->assertNotSame('', $body['paths']['/v1/notifications']['get']['summary'] ?? '');
        $this->assertNotSame('', $body['paths']['/v1/contact']['post']['summary'] ?? '');
    }
}
