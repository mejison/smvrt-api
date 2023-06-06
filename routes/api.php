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
Route::post('/profile/password-reset', [App\Http\Controllers\ProfileController::class, 'reset_password'])->name('reset_password');
Route::get('/profile/notifications', [App\Http\Controllers\ProfileController::class, 'get_last_notifications'])->name('get_last_notifications');
Route::get('/profile/projects', [App\Http\Controllers\ProfileController::class, 'projects'])->name('get_projects');

/* settings */
Route::get('/profile/settings', [App\Http\Controllers\ProfileController::class, 'get_settings'])->name('get-settings');
Route::post('/profile/settings', [App\Http\Controllers\ProfileController::class, 'update_settings'])->name('update-settings');

/* roles */
Route::get('/roles', [App\Http\Controllers\ProfileController::class, 'get_roles'])->name('get-all-roles');

/* teams */
Route::get('/profile/teams', [App\Http\Controllers\ProfileController::class, 'get_teams'])->name('get-profile-teams');
Route::delete('/team/{team}/member/remove', [App\Http\Controllers\TeamController::class, 'remove_member'])->name('remove-member-from-team');
Route::put('/team/{team}/member/update', [App\Http\Controllers\TeamController::class, 'member_update'])->name('member-update');
Route::post('/team/{team}/member/add', [App\Http\Controllers\TeamController::class, 'add_member'])->name('add-member');
Route::post('/team', [App\Http\Controllers\TeamController::class, 'create_team'])->name('create-team');

/* documents */
Route::get('/document/types', [App\Http\Controllers\DocumentController::class, 'get_types'])->name('get-types');

/* projects */
Route::put('/project/{project}/member/{user}/role', [App\Http\Controllers\ProjectController::class, 'requests_to_change_role'])->name('requests_to_change_role');
Route::post('/project', [App\Http\Controllers\ProjectController::class, 'create'])->name('project_create');
Route::get('/project/categories', [App\Http\Controllers\ProjectController::class, 'get_categories'])->name('get_categories');
/* notifications */ 
Route::post('/notification/{notification}/accept', [App\Http\Controllers\NotificationController::class, 'accept'])->name('notification_accept');
Route::post('/notification/{notification}/reject', [App\Http\Controllers\NotificationController::class, 'reject'])->name('notification_reject');
Route::post('/notification/{notification}/read', [App\Http\Controllers\NotificationController::class, 'read'])->name('notification_read');
