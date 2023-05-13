<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/user/activate', [App\Http\Controllers\AuthController::class, 'activate'])->name('activate');
Route::get('/social-callback/google', [App\Http\Controllers\AuthController::class, 'callback_google'])->name('callback_google');


// Route::get('/email', function () {
//     return view('emails.forgot-password', ['email' => 'test', 'link' => 'link']);
// });
