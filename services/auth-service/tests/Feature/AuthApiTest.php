<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_and_login(): void
    {
        $payload = [
            'name' => 'Ana Portfolio',
            'email' => 'ana.portfolio@example.com',
            'password' => 'password123',
        ];

        $register = $this->postJson('/api/v1/auth/register', $payload);

        $register->assertStatus(201)
            ->assertJsonPath('data.user.email', 'ana.portfolio@example.com')
            ->assertJsonPath('ok', true);

        $login = $this->postJson('/api/v1/auth/login', [
            'email' => 'ana.portfolio@example.com',
            'password' => 'password123',
        ]);

        $login->assertStatus(200)
            ->assertJsonPath('data.user.email', 'ana.portfolio@example.com')
            ->assertJsonPath('ok', true);
    }
}
