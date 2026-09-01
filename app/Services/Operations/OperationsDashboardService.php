<?php

namespace App\Services\Operations;

use App\Repositories\ClientRepository;
use App\Repositories\ProjectRepository;
use App\Repositories\TaskRepository;
use InvalidArgumentException;

class OperationsDashboardService
{
    public function __construct(
        protected ClientRepository $clientRepository,
        protected ProjectRepository $projectRepository,
        protected TaskRepository $taskRepository,
    ) {}

    public function getDashboard(): array
    {
        $clients = $this->clientRepository->all()->map(fn ($client) => [
            'id' => $client->id,
            'name' => $client->name,
            'email' => $client->email,
            'company' => $client->company,
            'status' => $client->status,
        ])->all();

        $projects = $this->projectRepository->all()->map(fn ($project) => [
            'id' => $project->id,
            'name' => $project->name,
            'status' => $project->status,
            'budget' => (float) $project->budget,
            'due_date' => $project->due_date,
            'client_id' => $project->client_id,
            'client' => $project->client ? [
                'id' => $project->client->id,
                'name' => $project->client->name,
            ] : null,
        ])->all();

        $tasks = $this->taskRepository->all()->map(fn ($task) => [
            'id' => $task->id,
            'project_id' => $task->project_id,
            'title' => $task->title,
            'assignee' => $task->assignee,
            'priority' => $task->priority,
            'status' => $task->status,
            'project' => $task->project ? [
                'id' => $task->project->id,
                'name' => $task->project->name,
            ] : null,
        ])->all();

        return [
            'service' => 'operations-service',
            'domain' => 'crm',
            'metrics' => [
                'clients' => count($clients),
                'projects' => count($projects),
                'tasks' => count($tasks),
            ],
            'clients' => $clients,
            'projects' => $projects,
            'tasks' => $tasks,
        ];
    }

    public function getClients(): array
    {
        return $this->clientRepository->all()->map(fn ($client) => [
            'id' => $client->id,
            'name' => $client->name,
            'email' => $client->email,
            'company' => $client->company,
            'status' => $client->status,
        ])->all();
    }

    public function getProjects(): array
    {
        return $this->projectRepository->all()->map(fn ($project) => [
            'id' => $project->id,
            'name' => $project->name,
            'status' => $project->status,
            'budget' => (float) $project->budget,
            'due_date' => $project->due_date,
            'client_id' => $project->client_id,
        ])->all();
    }

    public function getTasks(): array
    {
        return $this->taskRepository->all()->map(fn ($task) => [
            'id' => $task->id,
            'project_id' => $task->project_id,
            'title' => $task->title,
            'assignee' => $task->assignee,
            'priority' => $task->priority,
            'status' => $task->status,
        ])->all();
    }

    public function createClient(array $payload): array
    {
        $name = trim((string) ($payload['name'] ?? ''));
        $email = strtolower(trim((string) ($payload['email'] ?? '')));

        if ($name === '' || $email === '') {
            throw new InvalidArgumentException('name y email son obligatorios.');
        }

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('El correo no tiene un formato válido.');
        }

        if ($this->clientRepository->findByEmail($email) !== null) {
            throw new InvalidArgumentException('Ya existe un cliente con ese correo.');
        }

        $client = $this->clientRepository->create([
            'name' => $name,
            'email' => $email,
            'company' => $payload['company'] ?? null,
            'status' => $payload['status'] ?? 'active',
        ]);

        return [
            'id' => $client->id,
            'name' => $client->name,
            'email' => $client->email,
            'company' => $client->company,
            'status' => $client->status,
        ];
    }

    public function createProject(array $payload): array
    {
        $clientId = (int) ($payload['client_id'] ?? 0);
        $name = trim((string) ($payload['name'] ?? ''));

        if ($clientId <= 0 || $name === '') {
            throw new InvalidArgumentException('client_id y name son obligatorios.');
        }

        $project = $this->projectRepository->create([
            'client_id' => $clientId,
            'name' => $name,
            'status' => $payload['status'] ?? 'in_progress',
            'budget' => $payload['budget'] ?? 0,
            'due_date' => $payload['due_date'] ?? null,
        ]);

        return [
            'id' => $project->id,
            'client_id' => $project->client_id,
            'name' => $project->name,
            'status' => $project->status,
            'budget' => (float) $project->budget,
            'due_date' => $project->due_date,
        ];
    }

    public function createTask(array $payload): array
    {
        $projectId = (int) ($payload['project_id'] ?? 0);
        $title = trim((string) ($payload['title'] ?? ''));

        if ($projectId <= 0 || $title === '') {
            throw new InvalidArgumentException('project_id y title son obligatorios.');
        }

        $task = $this->taskRepository->create([
            'project_id' => $projectId,
            'title' => $title,
            'assignee' => $payload['assignee'] ?? null,
            'priority' => $payload['priority'] ?? 'medium',
            'status' => $payload['status'] ?? 'pending',
        ]);

        return [
            'id' => $task->id,
            'project_id' => $task->project_id,
            'title' => $task->title,
            'assignee' => $task->assignee,
            'priority' => $task->priority,
            'status' => $task->status,
        ];
    }
}
