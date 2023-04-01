<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\VerificationController;
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

//V1
Route::name('v1.')->prefix('v1')->group(function () {
    
    //Authentication Routes
    Route::name('auth.')->prefix('auth')->group(function () { 
        Route::post('login', [AuthController::class, 'login'])->name('login');
        Route::post('register', [AuthController::class, 'register'])->name('register');
        Route::post('forget-password', [AuthController::class, 'forget_password'])->name('forget_password');
        Route::post('reset-password', [AuthController::class, 'reset_password'])->name('reset_password');
        Route::get('email-verification', [VerificationController::class, 'check_verification'])->name('check_verification');
    });
    
    //Authenticated Routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('auth/logout', [AuthController::class, 'logout'])->name('auth.logout');
        Route::get('auth/resend-verification', [VerificationController::class, 'resend_verification'])->name('auth.resend_verification');
    });
});