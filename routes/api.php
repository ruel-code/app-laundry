<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotaController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SantriController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    Route::get('/dashboard', DashboardController::class);

    Route::get('/santri', [SantriController::class, 'index']);
    Route::post('/santri', [SantriController::class, 'store']);
    Route::get('/santri/{santri}', [SantriController::class, 'show']);
    Route::put('/santri/{santri}', [SantriController::class, 'update']);
    Route::delete('/santri/{santri}', [SantriController::class, 'destroy']);

    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{order}', [OrderController::class, 'show']);
    Route::patch('/orders/{order}/status', [OrderController::class, 'update']);
    Route::patch('/orders/{order}/payment', [OrderController::class, 'update']);
    Route::get('/orders/{order}/nota', NotaController::class);
    Route::delete('/orders/{order}', [OrderController::class, 'destroy']);

    Route::get('/reports/daily', [ReportController::class, 'daily']);
    Route::get('/reports/monthly', [ReportController::class, 'monthly']);
});
