<?php

use App\Http\Controllers\Admin\FrequencyController;
use App\Http\Controllers\Admin\KeyFactorController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Auth\loginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('Auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/login', [loginController::class, 'login']);
Route::post('/register', [RegisterController::class, 'register']);
Route::middleware(['Auth:sanctum'])->group(function () {
    Route::post('/logout', [loginController::class, 'logout']);
});

Route::middleware([AdminMiddleware::class])->group(function () {
    //admin-only routes here
    Route::resource('/admin/frequency', FrequencyController::class);
    Route::resource('/admin/keyfactor', KeyFactorController::class);
    Route::resource('/admin/service', ServiceController::class);
})->middleware(['Auth:sanctum']);
