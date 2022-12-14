<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Wx\User\AuthController;
use App\Http\Controllers\Wx\User\AddressController;
use App\Http\Controllers\Wx\Goods\CatalogController;

// 注册登录
Route::post('auth/register', [AuthController::class, 'register']); // 注册
Route::post('auth/regSms', [AuthController::class, 'regSms']); // 注册验证码、修改手机号获取验证码
Route::post('auth/login', [AuthController::class, 'login']); //登录
Route::get('auth/info', [AuthController::class, 'info']); // token获取用户信息
Route::post('auth/logout', [AuthController::class, 'logout']); //登出
Route::post('auth/reset', [AuthController::class, 'reset']); //重置密码
// 个人信息
Route::post('auth/profile', [AuthController::class, 'profile']); //修改个人信息
// 地址
Route::get('address/list', [AddressController::class, 'list']); // 地址列表
Route::get('address/detail', [AddressController::class, 'detail']); // 地址详情
Route::post('address/save', [AddressController::class, 'save']); // 修改
Route::post('address/delete', [AddressController::class, 'delete']); // 删除
// 商品类目
Route::any('catalog/index', [CatalogController::class, 'index']); // 全部分类
Route::any('catalog/current', [CatalogController::class, 'current']); // 当前分类
