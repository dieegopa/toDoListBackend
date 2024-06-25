<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);

    Route::middleware('auth:sanctum')->group(function () {

        Route::prefix('/user')->group(function () {
            Route::get('/', [UserController::class, 'index']);
            Route::get('/tags', [UserController::class, 'indexTags']);
        });

        Route::prefix('/tags')->group(function () {
            Route::post('/', [TagController::class, 'store']);
        });

        Route::prefix('/tasks')->group(function () {
            Route::get('/', [TaskController::class, 'index']);
            Route::post('/', [TaskController::class, 'store']);
            Route::get('/{task}', [TaskController::class, 'show']);
            Route::patch('/{task}', [TaskController::class, 'update']);
            Route::delete('/{task}', [TaskController::class, 'destroy']);
        });

        Route::post('/logout', [AuthController::class, 'logout']);
    });
});
