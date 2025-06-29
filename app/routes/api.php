<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CallController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\UserController;

Route::post('login', [UserController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::apiResource('calls', CallController::class);
    Route::apiResource('employees', EmployeeController::class);
});

