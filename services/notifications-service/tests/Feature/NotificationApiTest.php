<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_notification_can_be_created_and_listed(): void
    {
        $create = $this->postJson('/api/v1/contact', [
            'to' => 'hello@example.com',
            'subject' => 'Nueva propuesta',
            'message' => 'El cliente quiere revisar el plan de migración.',
            'channel' => 'email',
        ]);

        $create->assertStatus(202)
            ->assertJsonPath('data.to', 'hello@example.com')
            ->assertJsonPath('ok', true);

        $list = $this->getJson('/api/v1/notifications');

        $list->assertStatus(200)
            ->assertJsonPath('ok', true)
            ->assertJsonPath('data.0.subject', 'Nueva propuesta');
    }
}
