<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Wx\AuthController;

Route::post('auth/register', [AuthController::class,'register']);
Route::post('auth/regSms', [AuthController::class,'regSms']);
