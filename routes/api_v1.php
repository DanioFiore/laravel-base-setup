<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\TestsController;
use App\Http\Controllers\Api\V1\UsersController;
use App\Http\Controllers\Api\V1\AdminsController;

// USE THIS ROUTE FOR TESTING
Route::get('/test', [TestsController::class, 'test']);

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Apply sanctum authentication middleware to a group of routes
Route::middleware('auth:sanctum')->group(function () {
    // AUTH
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'currentUser']);

    // ADMINS MIDDLEWARE
    Route::middleware('admin')->group(function () {
        // USERS
        Route::get('/users', [UsersController::class, 'index']);
        Route::post('/users', [UsersController::class, 'store']);
        Route::patch('/users/{id}/restore', [UsersController::class, 'restore']);
        Route::delete('/users/{id}', [UsersController::class, 'softDestroy']);
    });
});