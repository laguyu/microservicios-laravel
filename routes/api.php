<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\OperationsController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);

    Route::get('/dashboard', [OperationsController::class, 'index']);
    Route::get('/clients', [OperationsController::class, 'clients']);
    Route::post('/clients', [OperationsController::class, 'storeClient']);
    Route::get('/projects', [OperationsController::class, 'projects']);
    Route::post('/projects', [OperationsController::class, 'storeProject']);
    Route::get('/tasks', [OperationsController::class, 'tasks']);
    Route::post('/tasks', [OperationsController::class, 'storeTask']);

    Route::post('/contact', [NotificationController::class, 'store']);
    Route::get('/notifications', [NotificationController::class, 'index']);
});
