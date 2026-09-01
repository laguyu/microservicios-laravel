<?php

use App\Http\Controllers\Api\NotificationController;
use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    return response()->json([
        'ok' => true,
        'service' => 'notifications-service',
        'status' => 'healthy',
    ]);
});

Route::prefix('v1')->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/contact', [NotificationController::class, 'store']);
});
