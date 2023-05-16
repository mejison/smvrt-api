<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/auth/register', [App\Http\Controllers\AuthController::class, 'register'])->name('register');
Route::post('/auth/resend', [App\Http\Controllers\AuthController::class, 'resend'])->name('resend');
Route::post('/auth/login', [App\Http\Controllers\AuthController::class, 'login'])->name('login');
Route::post('/auth/logout', [App\Http\Controllers\AuthController::class, 'logout'])->name('logout');
Route::post('/auth/refresh', [App\Http\Controllers\AuthController::class, 'refresh'])->name('refresh');
Route::get('/auth/me', [App\Http\Controllers\AuthController::class, 'me'])->name('me');
Route::post('/auth/forgot', [App\Http\Controllers\AuthController::class, 'forgot'])->name('forgot');
Route::post('/auth/reset', [App\Http\Controllers\AuthController::class, 'reset'])->name('reset');
Route::get('/auth/google/redirect', [App\Http\Controllers\AuthController::class, 'google_redirect'])->name('redirect');

/* profile */
Route::post('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('update');
