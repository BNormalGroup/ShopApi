<?php

use App\Http\Controllers\BrandsController;
use App\Http\Controllers\CategoriesController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('/categories')->
group(function () {
    Route::get('/', [CategoriesController::class, 'index']);
    Route::post('/', [CategoriesController::class, 'store']);
    Route::post('/{category}', [CategoriesController::class, 'update']);
    Route::delete('/{category}', [CategoriesController::class, 'delete']);
});
Route::prefix('/brands')->
group(function () {
    Route::get('/', [BrandsController::class, 'index']);
    Route::post('/', [BrandsController::class, 'store']);
    Route::post('/{brand}', [BrandsController::class, 'update']);
    Route::delete('/{brand}', [BrandsController::class, 'delete']);
});
