<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Project;
use App\Models\Task;
use Dedoc\Scramble\Attributes\BodyParameter;
use Dedoc\Scramble\Attributes\Endpoint;
use Dedoc\Scramble\Attributes\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OperationsController extends Controller
{
    #[Endpoint(operationId: 'operations.dashboard', title: 'Dashboard de operaciones', description: 'Devuelve métricas y registros recientes del negocio.')]
    #[Response(status: 200, description: 'Resumen del estado del negocio.')]
    public function dashboard(): JsonResponse
    {
        return response()->json([
            'ok' => true,
            'data' => [
                'service' => 'users-service',
                'domain' => 'operations',
                'metrics' => [
                    'clients' => Client::query()->count(),
                    'projects' => Project::query()->count(),
                    'tasks' => Task::query()->count(),
                ],
                'clients' => Client::query()->latest()->get(),
                'projects' => Project::query()->with('client')->latest()->get(),
                'tasks' => Task::query()->with('project')->latest()->get(),
            ],
        ]);
    }

    #[Endpoint(operationId: 'operations.clients', title: 'Listar clientes', description: 'Obtiene el listado de clientes registrados.')]
    #[Response(status: 200, description: 'Listado de clientes.')]
    public function clients(): JsonResponse
    {
        return response()->json([
            'ok' => true,
            'data' => Client::query()->latest()->get(),
        ]);
    }

    #[Endpoint(operationId: 'operations.storeClient', title: 'Crear cliente', description: 'Crea un nuevo cliente.')]
    #[BodyParameter('name', 'Nombre del cliente.', required: true, type: 'string', example: 'María López')]
    #[BodyParameter('email', 'Correo electrónico del cliente.', required: true, type: 'string', format: 'email', example: 'maria@empresa.com')]
    #[BodyParameter('company', 'Empresa del cliente.', type: 'string', example: 'Empresa ACME')]
    #[BodyParameter('status', 'Estado del cliente.', type: 'string', example: 'active')]
    #[Response(status: 201, description: 'Cliente creado correctamente.')]
    public function storeClient(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:clients'],
            'company' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'max:50'],
        ]);

        $client = Client::query()->create($data);

        return response()->json([
            'ok' => true,
            'data' => $client,
        ], 201);
    }

    #[Endpoint(operationId: 'operations.projects', title: 'Listar proyectos', description: 'Obtiene el listado de proyectos.')]
    #[Response(status: 200, description: 'Listado de proyectos.')]
    public function projects(): JsonResponse
    {
        return response()->json([
            'ok' => true,
            'data' => Project::query()->with('client')->latest()->get(),
        ]);
    }

    #[Endpoint(operationId: 'operations.storeProject', title: 'Crear proyecto', description: 'Crea un nuevo proyecto asociado a un cliente.')]
    #[BodyParameter('client_id', 'Identificador del cliente relacionado.', required: true, type: 'integer', example: 1)]
    #[BodyParameter('name', 'Nombre del proyecto.', required: true, type: 'string', example: 'Portal corporativo')]
    #[BodyParameter('status', 'Estado del proyecto.', type: 'string', example: 'in_progress')]
    #[BodyParameter('budget', 'Presupuesto del proyecto.', type: 'number', example: 15000.50)]
    #[BodyParameter('due_date', 'Fecha límite del proyecto.', type: 'string', format: 'date', example: '2026-10-30')]
    #[Response(status: 201, description: 'Proyecto creado correctamente.')]
    public function storeProject(Request $request): JsonResponse
    {
        $data = $request->validate([
            'client_id' => ['required', 'integer', 'exists:clients,id'],
            'name' => ['required', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'max:50'],
            'budget' => ['nullable', 'numeric'],
            'due_date' => ['nullable', 'date'],
        ]);

        $project = Project::query()->create($data);

        return response()->json([
            'ok' => true,
            'data' => $project,
        ], 201);
    }

    #[Endpoint(operationId: 'operations.tasks', title: 'Listar tareas', description: 'Obtiene el listado de tareas.')]
    #[Response(status: 200, description: 'Listado de tareas.')]
    public function tasks(): JsonResponse
    {
        return response()->json([
            'ok' => true,
            'data' => Task::query()->with('project')->latest()->get(),
        ]);
    }

    #[Endpoint(operationId: 'operations.storeTask', title: 'Crear tarea', description: 'Crea una nueva tarea asociada a un proyecto.')]
    #[BodyParameter('project_id', 'Identificador del proyecto asociado.', required: true, type: 'integer', example: 1)]
    #[BodyParameter('title', 'Título de la tarea.', required: true, type: 'string', example: 'Definir cronograma')]
    #[BodyParameter('assignee', 'Responsable de la tarea.', type: 'string', example: 'Ana García')]
    #[BodyParameter('priority', 'Prioridad de la tarea.', type: 'string', example: 'high')]
    #[BodyParameter('status', 'Estado de la tarea.', type: 'string', example: 'pending')]
    #[Response(status: 201, description: 'Tarea creada correctamente.')]
    public function storeTask(Request $request): JsonResponse
    {
        $data = $request->validate([
            'project_id' => ['required', 'integer', 'exists:projects,id'],
            'title' => ['required', 'string', 'max:255'],
            'assignee' => ['nullable', 'string', 'max:255'],
            'priority' => ['nullable', 'string', 'max:50'],
            'status' => ['nullable', 'string', 'max:50'],
        ]);

        $task = Task::query()->create($data);

        return response()->json([
            'ok' => true,
            'data' => $task,
        ], 201);
    }
}
