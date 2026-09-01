<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Dedoc\Scramble\Attributes\BodyParameter;
use Dedoc\Scramble\Attributes\Endpoint;
use Dedoc\Scramble\Attributes\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    #[Endpoint(operationId: 'notifications.index', title: 'Listar notificaciones', description: 'Obtiene el historial de notificaciones registradas.')]
    #[Response(status: 200, description: 'Listado de notificaciones.')]
    public function index(): JsonResponse
    {
        return response()->json([
            'ok' => true,
            'data' => Notification::query()->latest()->get(),
        ]);
    }

    #[Endpoint(operationId: 'notifications.store', title: 'Enviar contacto o notificación', description: 'Crea una nueva notificación o mensaje de contacto.')]
    #[BodyParameter('to', 'Correo electrónico del destinatario.', required: true, type: 'string', format: 'email', example: 'cliente@empresa.com')]
    #[BodyParameter('subject', 'Asunto del mensaje.', required: true, type: 'string', example: 'Consulta desde portafolio')]
    #[BodyParameter('message', 'Contenido del mensaje.', required: true, type: 'string', example: 'Necesito más información sobre el proyecto.')]
    #[BodyParameter('status', 'Estado del envío.', type: 'string', example: 'queued')]
    #[BodyParameter('channel', 'Canal de entrega.', type: 'string', example: 'email')]
    #[Response(status: 202, description: 'Notificación registrada correctamente.')]
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'to' => ['required', 'email', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
            'status' => ['nullable', 'string', 'max:50'],
            'channel' => ['nullable', 'string', 'max:50'],
        ]);

        $notification = Notification::query()->create([
            'to' => strtolower($data['to']),
            'subject' => $data['subject'],
            'message' => $data['message'],
            'status' => $data['status'] ?? 'queued',
            'channel' => $data['channel'] ?? 'email',
        ]);

        return response()->json([
            'ok' => true,
            'data' => $notification,
        ], 202);
    }
}
