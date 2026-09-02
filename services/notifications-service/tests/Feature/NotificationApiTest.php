<?php

namespace Tests\Feature;

use App\Support\JwtToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_notification_can_be_created_and_listed(): void
    {
        config()->set('app.jwt_secret', 'testing-secret');
        config()->set('app.jwt_algorithm', 'HS256');

        $headers = [
            'Authorization' => 'Bearer '.JwtToken::issue(1, 'ana@example.com'),
        ];

        $this->postJson('/api/v1/contact', [
            'to' => 'blocked@example.com',
            'subject' => 'Sin token',
            'message' => 'Este request debe fallar sin token.',
            'channel' => 'email',
        ])->assertStatus(401);

        $create = $this->postJson('/api/v1/contact', [
            'to' => 'hello@example.com',
            'subject' => 'Nueva propuesta',
            'message' => 'El cliente quiere revisar el plan de migración.',
            'channel' => 'email',
        ], $headers);

        $create->assertStatus(202)
            ->assertJsonPath('data.to', 'hello@example.com')
            ->assertJsonPath('ok', true);

        $list = $this->getJson('/api/v1/notifications', $headers);

        $list->assertStatus(200)
            ->assertJsonPath('ok', true)
            ->assertJsonPath('data.0.subject', 'Nueva propuesta');
    }
}
