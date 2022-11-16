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
//    $response = \Illuminate\Support\Facades\Http::get('https://wiveda.de/api/v1/cars');
//    $brands = collect(json_decode($response->body()));
//
//    foreach ($brands as $brand => $models) {
//        $newCarBrand = new \App\Models\Eloquent\CarBrand;
//        $newCarBrand->name = $brand;
//        $newCarBrand->save();
//
//        foreach ($models as $model) {
//            $newCarBrandModel = new \App\Models\Eloquent\CarBrandModel;
//            $newCarBrandModel->car_brand_id = $newCarBrand->id;
//            $newCarBrandModel->name = $model;
//            $newCarBrandModel->save();
//        }
//    }
//
//    return 'Trastransfer completed';
});
