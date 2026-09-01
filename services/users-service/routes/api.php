<?php

use App\Http\Controllers\Api\OperationsController;
use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    return response()->json([
        'ok' => true,
        'service' => 'users-service',
        'status' => 'healthy',
    ]);
});

Route::prefix('v1')->group(function () {
    Route::get('/dashboard', [OperationsController::class, 'dashboard']);
    Route::get('/clients', [OperationsController::class, 'clients']);
    Route::post('/clients', [OperationsController::class, 'storeClient']);
    Route::get('/projects', [OperationsController::class, 'projects']);
    Route::post('/projects', [OperationsController::class, 'storeProject']);
    Route::get('/tasks', [OperationsController::class, 'tasks']);
    Route::post('/tasks', [OperationsController::class, 'storeTask']);
});
