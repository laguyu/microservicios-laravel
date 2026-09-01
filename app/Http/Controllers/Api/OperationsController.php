<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Operations\OperationsDashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

class OperationsController extends Controller
{
    public function __construct(protected OperationsDashboardService $dashboardService) {}

    public function index(): JsonResponse
    {
        return response()->json([
            'ok' => true,
            'data' => $this->dashboardService->getDashboard(),
        ]);
    }

    public function clients(): JsonResponse
    {
        return response()->json([
            'ok' => true,
            'data' => $this->dashboardService->getClients(),
        ]);
    }

    public function projects(): JsonResponse
    {
        return response()->json([
            'ok' => true,
            'data' => $this->dashboardService->getProjects(),
        ]);
    }

    public function tasks(): JsonResponse
    {
        return response()->json([
            'ok' => true,
            'data' => $this->dashboardService->getTasks(),
        ]);
    }

    public function storeClient(Request $request): JsonResponse
    {
        try {
            $data = $this->dashboardService->createClient($request->all());

            return response()->json([
                'ok' => true,
                'data' => $data,
            ], 201);
        } catch (InvalidArgumentException $exception) {
            return response()->json([
                'ok' => false,
                'message' => $exception->getMessage(),
            ], 422);
        }
    }

    public function storeProject(Request $request): JsonResponse
    {
        try {
            $data = $this->dashboardService->createProject($request->all());

            return response()->json([
                'ok' => true,
                'data' => $data,
            ], 201);
        } catch (InvalidArgumentException $exception) {
            return response()->json([
                'ok' => false,
                'message' => $exception->getMessage(),
            ], 422);
        }
    }

    public function storeTask(Request $request): JsonResponse
    {
        try {
            $data = $this->dashboardService->createTask($request->all());

            return response()->json([
                'ok' => true,
                'data' => $data,
            ], 201);
        } catch (InvalidArgumentException $exception) {
            return response()->json([
                'ok' => false,
                'message' => $exception->getMessage(),
            ], 422);
        }
    }
}
