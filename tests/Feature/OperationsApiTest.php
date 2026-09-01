<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OperationsApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_can_be_created_via_operations_api(): void
    {
        $response = $this->postJson('/api/v1/clients', [
            'name' => 'Ana García',
            'email' => 'ana@empresa.com',
            'company' => 'Studio Co',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('ok', true)
            ->assertJsonPath('data.name', 'Ana García');

        $this->assertDatabaseHas('clients', [
            'email' => 'ana@empresa.com',
            'company' => 'Studio Co',
        ]);
    }

    public function test_dashboard_returns_live_metrics_from_database(): void
    {
        $clientA = Client::query()->create([
            'name' => 'Cliente A',
            'email' => 'clientea@example.com',
            'company' => 'Studio A',
            'status' => 'active',
        ]);

        $clientB = Client::query()->create([
            'name' => 'Cliente B',
            'email' => 'clienteb@example.com',
            'company' => 'Studio B',
            'status' => 'prospect',
        ]);

        $projectA = Project::query()->create([
            'client_id' => $clientA->id,
            'name' => 'Proyecto A',
            'status' => 'in_progress',
            'budget' => 3500,
            'due_date' => '2026-09-15',
        ]);

        Project::query()->create([
            'client_id' => $clientB->id,
            'name' => 'Proyecto B',
            'status' => 'pending',
            'budget' => 1200,
            'due_date' => '2026-10-10',
        ]);

        Task::query()->create([
            'project_id' => $projectA->id,
            'title' => 'Diseño',
            'assignee' => 'Nora',
            'priority' => 'high',
            'status' => 'pending',
        ]);

        Task::query()->create([
            'project_id' => $projectA->id,
            'title' => 'Implementación',
            'assignee' => 'Luis',
            'priority' => 'medium',
            'status' => 'in_progress',
        ]);

        $response = $this->getJson('/api/v1/dashboard');

        $response->assertOk()
            ->assertJsonPath('data.metrics.clients', 2)
            ->assertJsonPath('data.metrics.projects', 2)
            ->assertJsonPath('data.metrics.tasks', 2);
    }

    public function test_contact_form_creates_a_notification_record(): void
    {
        $response = $this->postJson('/api/v1/contact', [
            'to' => 'soporte@empresa.com',
            'subject' => 'Nuevo proyecto',
            'message' => 'Necesitamos una propuesta para una landing page.',
        ]);

        $response->assertStatus(202)
            ->assertJsonPath('ok', true)
            ->assertJsonPath('data.to', 'soporte@empresa.com');

        $this->assertDatabaseHas('notifications', [
            'to' => 'soporte@empresa.com',
            'subject' => 'Nuevo proyecto',
            'status' => 'queued',
        ]);
    }
}
