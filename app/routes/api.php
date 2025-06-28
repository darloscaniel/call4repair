<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CallController;
use App\Http\Controllers\EmployeeController;

Route::post('login', [UserController::class, 'login']);
Route::apiResource('calls', CallController::class);
Route::apiResource('employees', EmployeeController::class);