<?php

use App\Http\Controllers\Admin\FrequencyController;
use App\Http\Controllers\Admin\KeyFactorController;
use App\Http\Controllers\Admin\KeyFactorsController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\ProjectImagesController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\loginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\User\ProfileController;
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

Route::middleware(['Auth:sanctum', AdminMiddleware::class])->group(function () {
    //admin-only routes here
    Route::resource('/admin/frequency', FrequencyController::class);
    Route::post('/update-service', [ServiceController::class, 'update']);
    Route::resource('/admin/service', ServiceController::class);

    Route::resource('/admin/projects', ProjectController::class);
    Route::post('admin/project-images', [ProjectImagesController::class, 'uploadImagesFromAdmin']);
    Route::get('admin/key_factors',[KeyFactorsController::class, 'index']);
    Route::put('admin/key_factors/{id}',[KeyFactorsController::class, 'update']);
    Route::post('admin/key_factors',[KeyFactorsController::class, 'store']);
    Route::delete('admin/key_factors/{id}',[KeyFactorsController::class, 'destroy']);
    Route::resource('admin/users', UserController::class);
});

Route::middleware(['Auth:sanctum'])->group(function () {
    Route::get('key_factors',[\App\Http\Controllers\User\KeyFactorController::class, 'getKeyFactors']);
    Route::get('frequencies',[\App\Http\Controllers\User\KeyFactorController::class, 'getFrequencies']);
    Route::get('updateProfilePicture',[ProfileController::class, 'updateProfilePicture']);
    Route::get('updatePassword',[ProfileController::class, 'updateUserPassword']);
});


