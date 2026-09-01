<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Dedoc\Scramble\Attributes\BodyParameter;
use Dedoc\Scramble\Attributes\Endpoint;
use Dedoc\Scramble\Attributes\Response;
use Illuminate\Http\Client\Response as ClientResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class GatewayController extends Controller
{
    #[Endpoint(operationId: 'gateway.register', title: 'Registrar usuario', description: 'Reenvía el registro de usuario al servicio de autenticación.')]
    #[BodyParameter('name', 'Nombre completo del usuario.', required: true, type: 'string', example: 'Ana García')]
    #[BodyParameter('email', 'Correo electrónico único del usuario.', required: true, type: 'string', format: 'email', example: 'ana@ejemplo.com')]
    #[BodyParameter('password', 'Contraseña segura con al menos 8 caracteres.', required: true, type: 'string', format: 'password', example: 'Secret123')]
    #[Response(status: 201, description: 'Usuario registrado correctamente.')]
    #[Response(status: 422, description: 'Los datos enviados no cumplen la validación.')]
    public function register(Request $request): JsonResponse
    {
        return $this->forward($request, 'auth', 'POST', '/api/v1/auth/register');
    }

    #[Endpoint(operationId: 'gateway.login', title: 'Iniciar sesión', description: 'Reenvía la autenticación al servicio de autenticación.')]
    #[BodyParameter('email', 'Correo electrónico registrado.', required: true, type: 'string', format: 'email', example: 'ana@ejemplo.com')]
    #[BodyParameter('password', 'Contraseña del usuario.', required: true, type: 'string', format: 'password', example: 'Secret123')]
    #[Response(status: 200, description: 'Sesión iniciada correctamente.')]
    #[Response(status: 422, description: 'Credenciales inválidas.')]
    public function login(Request $request): JsonResponse
    {
        return $this->forward($request, 'auth', 'POST', '/api/v1/auth/login');
    }

    #[Endpoint(operationId: 'gateway.dashboard', title: 'Dashboard de operaciones', description: 'Obtiene métricas y registros recientes desde el servicio de usuarios.')]
    #[Response(status: 200, description: 'Resumen del estado del negocio.')]
    public function dashboard(Request $request): JsonResponse
    {
        return $this->forward($request, 'users', 'GET', '/api/v1/dashboard');
    }

    #[Endpoint(operationId: 'gateway.clients', title: 'Listar clientes', description: 'Obtiene los clientes desde el servicio de usuarios.')]
    #[Response(status: 200, description: 'Listado de clientes.')]
    public function clients(Request $request): JsonResponse
    {
        return $this->forward($request, 'users', 'GET', '/api/v1/clients');
    }

    #[Endpoint(operationId: 'gateway.storeClient', title: 'Crear cliente', description: 'Reenvía la creación de un cliente al servicio de usuarios.')]
    #[BodyParameter('name', 'Nombre del cliente.', required: true, type: 'string', example: 'María López')]
    #[BodyParameter('email', 'Correo electrónico del cliente.', required: true, type: 'string', format: 'email', example: 'maria@empresa.com')]
    #[BodyParameter('company', 'Empresa del cliente.', type: 'string', example: 'Empresa ACME')]
    #[BodyParameter('status', 'Estado del cliente.', type: 'string', example: 'active')]
    #[Response(status: 201, description: 'Cliente creado correctamente.')]
    public function storeClient(Request $request): JsonResponse
    {
        return $this->forward($request, 'users', 'POST', '/api/v1/clients');
    }

    #[Endpoint(operationId: 'gateway.projects', title: 'Listar proyectos', description: 'Obtiene los proyectos desde el servicio de usuarios.')]
    #[Response(status: 200, description: 'Listado de proyectos.')]
    public function projects(Request $request): JsonResponse
    {
        return $this->forward($request, 'users', 'GET', '/api/v1/projects');
    }

    #[Endpoint(operationId: 'gateway.storeProject', title: 'Crear proyecto', description: 'Reenvía la creación de un proyecto al servicio de usuarios.')]
    #[BodyParameter('client_id', 'Identificador del cliente relacionado.', required: true, type: 'integer', example: 1)]
    #[BodyParameter('name', 'Nombre del proyecto.', required: true, type: 'string', example: 'Portal corporativo')]
    #[BodyParameter('status', 'Estado del proyecto.', type: 'string', example: 'in_progress')]
    #[BodyParameter('budget', 'Presupuesto del proyecto.', type: 'number', example: 15000.50)]
    #[BodyParameter('due_date', 'Fecha límite del proyecto.', type: 'string', format: 'date', example: '2026-10-30')]
    #[Response(status: 201, description: 'Proyecto creado correctamente.')]
    public function storeProject(Request $request): JsonResponse
    {
        return $this->forward($request, 'users', 'POST', '/api/v1/projects');
    }

    #[Endpoint(operationId: 'gateway.tasks', title: 'Listar tareas', description: 'Obtiene las tareas desde el servicio de usuarios.')]
    #[Response(status: 200, description: 'Listado de tareas.')]
    public function tasks(Request $request): JsonResponse
    {
        return $this->forward($request, 'users', 'GET', '/api/v1/tasks');
    }

    #[Endpoint(operationId: 'gateway.storeTask', title: 'Crear tarea', description: 'Reenvía la creación de una tarea al servicio de usuarios.')]
    #[BodyParameter('project_id', 'Identificador del proyecto asociado.', required: true, type: 'integer', example: 1)]
    #[BodyParameter('title', 'Título de la tarea.', required: true, type: 'string', example: 'Definir cronograma')]
    #[BodyParameter('assignee', 'Responsable de la tarea.', type: 'string', example: 'Ana García')]
    #[BodyParameter('priority', 'Prioridad de la tarea.', type: 'string', example: 'high')]
    #[BodyParameter('status', 'Estado de la tarea.', type: 'string', example: 'pending')]
    #[Response(status: 201, description: 'Tarea creada correctamente.')]
    public function storeTask(Request $request): JsonResponse
    {
        return $this->forward($request, 'users', 'POST', '/api/v1/tasks');
    }

    #[Endpoint(operationId: 'gateway.notifications', title: 'Listar notificaciones', description: 'Obtiene notificaciones desde el servicio de notificaciones.')]
    #[Response(status: 200, description: 'Listado de notificaciones.')]
    public function notifications(Request $request): JsonResponse
    {
        return $this->forward($request, 'notifications', 'GET', '/api/v1/notifications');
    }

    #[Endpoint(operationId: 'gateway.storeContact', title: 'Enviar contacto o notificación', description: 'Reenvía un mensaje al servicio de notificaciones.')]
    #[BodyParameter('to', 'Correo electrónico del destinatario.', required: true, type: 'string', format: 'email', example: 'cliente@empresa.com')]
    #[BodyParameter('subject', 'Asunto del mensaje.', required: true, type: 'string', example: 'Consulta desde portafolio')]
    #[BodyParameter('message', 'Contenido del mensaje.', required: true, type: 'string', example: 'Necesito más información sobre el proyecto.')]
    #[BodyParameter('status', 'Estado del envío.', type: 'string', example: 'queued')]
    #[BodyParameter('channel', 'Canal de entrega.', type: 'string', example: 'email')]
    #[Response(status: 202, description: 'Notificación registrada correctamente.')]
    public function storeContact(Request $request): JsonResponse
    {
        return $this->forward($request, 'notifications', 'POST', '/api/v1/contact');
    }

    private function forward(Request $request, string $service, string $method, string $path): JsonResponse
    {
        $response = Http::acceptJson()
            ->connectTimeout(3)
            ->timeout(10)
            ->withHeaders(array_filter([
                'Authorization' => $request->header('Authorization'),
            ]))
            ->send($method, rtrim((string) config("services.microservices.{$service}"), '/').$path, [
                'json' => $request->all(),
            ]);

        return $this->response($response);
    }

    private function response(ClientResponse $response): JsonResponse
    {
        return response()->json($response->json(), $response->status());
    }
}
