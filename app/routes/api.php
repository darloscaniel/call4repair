<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CallController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\UserController;

// Login is throttled per IP to slow down brute-force attempts.
Route::post('login', [UserController::class, 'login'])->middleware('throttle:login');

// Abertura pública de chamados (formulário do cliente) — limitada a 5/min por IP.
Route::post('calls', [CallController::class, 'store'])->middleware('throttle:calls');

Route::middleware('auth:api')->group(function () {
    Route::get('me', [UserController::class, 'me']);
    Route::post('refresh', [UserController::class, 'refresh']);
    Route::post('logout', [UserController::class, 'logout']);

    Route::get('calls', [CallController::class, 'index'])->middleware('permission:view calls');
    Route::get('calls/{call}', [CallController::class, 'show'])->middleware('permission:view calls');
    Route::put('calls/{call}', [CallController::class, 'update'])->middleware('permission:manage calls');
    Route::delete('calls/{call}', [CallController::class, 'destroy'])->middleware('permission:delete calls');

    Route::apiResource('employees', EmployeeController::class)
        ->middleware('permission:manage employees');
});

