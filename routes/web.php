<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\loginController;
use App\Http\Controllers\User\ProjectOfferController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/email/success', function () {
    return view('email.verificationSuccess');
})->name('email.success');

Route::get('reset-password/{token}', [loginController::class, 'showResetPasswordForm'])->name('reset.password.get');
Route::post('/reset-password', [loginController::class, 'submitResetPasswordForm'])->name('update.password');
Route::get('/offers/download/{id}/{selected_option}', [ProjectOfferController::class, 'downloadOfferAsPdf']);
