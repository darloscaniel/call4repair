<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CallController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\UserController;
use App\Models\Call;

Route::post('login', [UserController::class, 'login']);
Route::post('calls', [CallController::class, 'store']);

Route::middleware('auth:api')->group(function () {
        Route::get('calls', [CallController::class, 'index']);
    Route::get('calls/{call}', [CallController::class, 'show']);
    Route::put('calls/{call}', [CallController::class, 'update']);
    Route::delete('calls/{call}', [CallController::class, 'destroy']);
    Route::apiResource('employees', EmployeeController::class);
});

