<?php

use App\Http\Controllers\Api\{
    AuthController,
    VehicleApiController,
    FuelApiController,
    MovementApiController,
    ApprovalApiController,
    NotificationApiController,
};
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);

        Route::get('/vehicles', [VehicleApiController::class, 'index']);
        Route::get('/vehicles/{vehicle}', [VehicleApiController::class, 'show']);

        Route::get('/fuel', [FuelApiController::class, 'index']);
        Route::post('/fuel', [FuelApiController::class, 'store']);

        Route::get('/movements', [MovementApiController::class, 'index']);
        Route::post('/movements/checkout', [MovementApiController::class, 'checkout']);
        Route::put('/movements/{movement}/checkin', [MovementApiController::class, 'checkin']);

        Route::get('/approvals', [ApprovalApiController::class, 'index']);
        Route::post('/approvals', [ApprovalApiController::class, 'store']);
        Route::get('/approvals/{approval}', [ApprovalApiController::class, 'show']);

        Route::get('/notifications', [NotificationApiController::class, 'index']);
        Route::get('/notifications/unread', [NotificationApiController::class, 'unread']);
        Route::put('/notifications/{id}/read', [NotificationApiController::class, 'markRead']);
        Route::put('/notifications/read-all', [NotificationApiController::class, 'markAllRead']);
    });
});
