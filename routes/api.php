<?php

use App\Http\Controllers\AuthController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//V1
Route::name('v1.')->prefix('v1')->group(function () {
    
    //Authentication Routes
    Route::name('auth.')->prefix('auth')->group(function () { 
        Route::post('login', [AuthController::class, 'login'])->name('login');
        Route::post('register', [AuthController::class, 'register'])->name('register');
        Route::post('forget-password', [AuthController::class, 'forget_password'])->name('forget_password');
        Route::post('reset-password', [AuthController::class, 'reset_password'])->name('reset_password');
    });
    

    //Authenticated Routes
    Route::middleware('auth:sanctum')->group(function () {

    });
});