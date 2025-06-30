<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CallController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\UserController;
use App\Models\Call;

Route::post('login', [UserController::class, 'login']);
Route::post('forms', [CallController::class, 'forms']);

Route::middleware('auth:api')->group(function () {
    Route::apiResource('calls', CallController::class);
    Route::apiResource('employees', EmployeeController::class);
});

