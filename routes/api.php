<?php

use App\Http\Controllers\ManuscriptController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/users', [AuthController::class, 'manageUsers'])->middleware('role:super_admin');
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->middleware('role:super_admin');

    Route::apiResource('manuscripts', ManuscriptController::class)
        ->only(['index', 'show'])
        ->names([
            'index' => 'manuscript.index',
            'show' => 'manuscript.show',
        ]);

    Route::middleware('role:admin,super_admin')->group(function () {
        Route::apiResource('manuscripts', ManuscriptController::class)
            ->only(['store', 'update', 'destroy'])
            ->names([
                'store' => 'manuscript.store',
                'update' => 'manuscript.update',
                'destroy' => 'manuscript.destroy',
            ]);
    });
});
