<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OperationsApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_and_domain_resources_work(): void
    {
        $client = $this->postJson('/api/v1/clients', [
            'name' => 'Alpha Consultora',
            'email' => 'contacto@alphaconsultora.com',
            'company' => 'Alpha',
            'status' => 'active',
        ]);

        $client->assertStatus(201)
            ->assertJsonPath('data.email', 'contacto@alphaconsultora.com');

        $clientId = $client->json('data.id');

        $project = $this->postJson('/api/v1/projects', [
            'client_id' => $clientId,
            'name' => 'Portfolio digital',
            'status' => 'active',
            'budget' => 4500.00,
            'due_date' => '2026-12-31',
        ]);

        $project->assertStatus(201)
            ->assertJsonPath('data.name', 'Portfolio digital');

        $projectId = $project->json('data.id');

        $task = $this->postJson('/api/v1/tasks', [
            'project_id' => $projectId,
            'title' => 'Diseñar la estrategia',
            'assignee' => 'Ana',
            'priority' => 'high',
            'status' => 'pending',
        ]);

        $task->assertStatus(201)
            ->assertJsonPath('data.title', 'Diseñar la estrategia');

        $dashboard = $this->getJson('/api/v1/dashboard');

        $dashboard->assertStatus(200)
            ->assertJsonPath('data.metrics.clients', 1)
            ->assertJsonPath('data.metrics.projects', 1)
            ->assertJsonPath('data.metrics.tasks', 1);
    }
}
