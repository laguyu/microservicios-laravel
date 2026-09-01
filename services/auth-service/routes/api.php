<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    return response()->json([
        'ok' => true,
        'service' => 'auth-service',
        'status' => 'healthy',
    ]);
});

Route::prefix('v1')->group(function () {
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);
});
