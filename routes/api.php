<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\apps\users\UsersController;
use App\Http\Controllers\apps\users\UserStatsController;
use App\Http\Controllers\CenterOfCostController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\CardController;





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

Route::group(['prefix' => 'auth'], function () {
  Route::post('login', [AuthController::class, 'login']);
  Route::post('register', [AuthController::class, 'register']);
  Route::post('password/email', [AuthController::class, 'sendPasswordResetLink']);  // Trimite linkul de resetare
  Route::post('password/reset', [AuthController::class, 'resetPassword']);  // Resetează parola
  Route::get('verify-token', [AuthController::class, 'verifyToken']);


  Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('logout', [AuthController::class, 'logout']);
    Route::get('user', [AuthController::class, 'user']);
  });
});

Route::group(['prefix' => 'apps/users', 'middleware' => 'auth:sanctum'], function () {
  Route::get('/', [App\Http\Controllers\apps\users\UsersController::class, 'index']);
  Route::get('/{id}', [App\Http\Controllers\apps\users\UsersController::class, 'show']);
  Route::post('/', [App\Http\Controllers\apps\users\UsersController::class, 'store']);
  Route::put('/{id}', [App\Http\Controllers\apps\users\UsersController::class, 'update']);
  Route::delete('/{id}', [App\Http\Controllers\apps\users\UsersController::class, 'destroy']);
});

Route::group(['prefix' => 'apps/roles', 'middleware' => 'auth:sanctum'], function () {
  Route::get('/list', [App\Http\Controllers\apps\users\RolesController::class, 'index']);
  Route::put('/update/{role}', [App\Http\Controllers\apps\users\RolesController::class, 'update']);
  Route::post('/add', [App\Http\Controllers\apps\users\RolesController::class, 'store']);
  Route::get('/rolespermission', [App\Http\Controllers\apps\users\RolesController::class, 'rolesPermission']);
  Route::delete('delete/{id}', [App\Http\Controllers\apps\users\RolesController::class, 'destroy']);
});

Route::group(['prefix' => 'apps/permissions', 'middleware' => 'auth:sanctum'], function () {
  Route::get('/list', [App\Http\Controllers\apps\users\PermissionsController::class, 'index']);
});

Route::group(['prefix' => 'apps/stats', 'middleware' => 'auth:sanctum'], function () {
  Route::get('/', [App\Http\Controllers\apps\users\UserStatsController::class, 'getUserStats']);
});

Route::group(['prefix' => 'logs/activity', 'middleware' => 'auth:sanctum'], function () {
  Route::post('/', [App\Http\Controllers\logs\ActivityLogController::class, 'store']);
});

// New CRUD routes for centers_of_cost, requests, and card
Route::group(['middleware' => 'auth:sanctum'], function () {
  Route::apiResource('centers-of-cost', CenterOfCostController::class);
  Route::apiResource('requests', RequestController::class);
  Route::apiResource('cards', CardController::class);
});
Route::get('masked-cards', [CardController::class, 'getMaskedCards']);
