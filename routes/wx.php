<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Wx\AuthController;

Route::post('auth/register', [AuthController::class,'register']);
Route::post('auth/regSms', [AuthController::class,'regSms']);
Route::post('auth/login', [AuthController::class,'login']);
// token获取用户信息
Route::get('auth/user', [AuthController::class,'user']);
