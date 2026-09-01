<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Auth\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

class AuthController extends Controller
{
    public function __construct(protected AuthService $authService) {}

    public function register(Request $request): JsonResponse
    {
        try {
            $response = $this->authService->register($request->all());

            return response()->json([
                'ok' => true,
                'data' => $response,
            ], 201);
        } catch (InvalidArgumentException $exception) {
            return response()->json([
                'ok' => false,
                'message' => $exception->getMessage(),
            ], 422);
        }
    }

    public function login(Request $request): JsonResponse
    {
        try {
            $response = $this->authService->login($request->all());

            return response()->json([
                'ok' => true,
                'data' => $response,
            ]);
        } catch (InvalidArgumentException $exception) {
            return response()->json([
                'ok' => false,
                'message' => $exception->getMessage(),
            ], 401);
        }
    }
}
