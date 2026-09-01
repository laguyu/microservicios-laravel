<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Notifications\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

class NotificationController extends Controller
{
    public function __construct(protected NotificationService $notificationService) {}

    public function index(): JsonResponse
    {
        return response()->json([
            'ok' => true,
            'data' => $this->notificationService->getNotifications(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $payload = $this->notificationService->sendContact([
                'to' => $request->input('to', env('CONTACT_EMAIL', 'soporte@empresa.com')),
                'subject' => $request->input('subject', 'Nuevo contacto desde tu portafolio'),
                'message' => $request->input('message', ''),
                'channel' => $request->input('channel', 'email'),
                'status' => $request->input('status', 'queued'),
            ]);

            return response()->json([
                'ok' => true,
                'data' => $payload,
            ], 202);
        } catch (InvalidArgumentException $exception) {
            return response()->json([
                'ok' => false,
                'message' => $exception->getMessage(),
            ], 422);
        }
    }
}
