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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware([
    'auth:user_api'
])->group(function () {
    Route::post('login', [\App\Http\Controllers\Api\UserController::class, 'login'])->withoutMiddleware('auth:user_api');

    Route::prefix('carBrand')->group(function () {
        Route::get('getAll', [\App\Http\Controllers\Api\CarBrandController::class, 'getAll']);
    });

    Route::prefix('carBrandModel')->group(function () {
        Route::get('getAll', [\App\Http\Controllers\Api\CarBrandModelController::class, 'getAll']);
        Route::get('getByCarBrandId', [\App\Http\Controllers\Api\CarBrandModelController::class, 'getByCarBrandId']);
    });

    Route::prefix('pricePrediction')->group(function () {
        Route::post('check', [\App\Http\Controllers\Api\PricePredictionController::class, 'check']);
    });
});
