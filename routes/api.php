<?php

use App\Http\Controllers\AuthController;
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

        Route::post('/logout', [AuthController::class, 'logout']);
    });
});
