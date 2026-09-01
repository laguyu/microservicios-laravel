<?php

use App\Http\Controllers\Api\GatewayController;
use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    return response()->json([
        'ok' => true,
        'service' => 'api-gateway',
        'status' => 'healthy',
    ]);
});

Route::prefix('v1')->group(function () {
    Route::post('/auth/register', [GatewayController::class, 'register']);
    Route::post('/auth/login', [GatewayController::class, 'login']);
    Route::get('/dashboard', [GatewayController::class, 'dashboard']);
    Route::get('/clients', [GatewayController::class, 'clients']);
    Route::post('/clients', [GatewayController::class, 'storeClient']);
    Route::get('/projects', [GatewayController::class, 'projects']);
    Route::post('/projects', [GatewayController::class, 'storeProject']);
    Route::get('/tasks', [GatewayController::class, 'tasks']);
    Route::post('/tasks', [GatewayController::class, 'storeTask']);
    Route::get('/notifications', [GatewayController::class, 'notifications']);
    Route::post('/contact', [GatewayController::class, 'storeContact']);
});
