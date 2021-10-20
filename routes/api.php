<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PanicController;
use App\Http\Controllers\AuthController;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('register', [AuthController::class, 'signup']);
Route::post('login', [AuthController::class, 'signin']);

Route::middleware('auth:api')->group(function () {
    Route::resource('v1/panic/create', PanicController::class);
    Route::resource('v1/panic/get', PanicController::class);  
});

Route::post('v1/panic/cancel', [PanicController::class, 'destroy']);




