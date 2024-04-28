<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BasketController;
use App\Http\Controllers\BrandsController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\LikesController;
use App\Http\Controllers\UserBannesController;
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

Route::prefix('/categories')->
group(function () {
    Route::get('/', [CategoriesController::class, 'index']);
    Route::get('/child', [CategoriesController::class, 'getCategoriesWithChildren']);
    Route::get('/items/{id}', [CategoriesController::class, 'getCategoryItemIds']);
    Route::post('/', [CategoriesController::class, 'store']);
    Route::post('/{category}', [CategoriesController::class, 'update']);
    Route::get('/show/{id}', [CategoriesController::class, 'show']);
    Route::delete('/{category}', [CategoriesController::class, 'delete']);
});

Route::prefix('/brands')->
group(function () {
    Route::get('/', [BrandsController::class, 'index']);
    Route::post('/', [BrandsController::class, 'store']);
    Route::post('/{brand}', [BrandsController::class, 'update']);
    Route::delete('/{brand}', [BrandsController::class, 'delete']);
    Route::get('/show/{id}', [BrandsController::class, 'show']);
});

Route::prefix('/items')->
group(function () {
    Route::get('/', [ItemController::class, 'index']);
    Route::post('/', [ItemController::class, 'store']);
    Route::get('/show/{id}', [ItemController::class, 'show']);
    Route::post('/{id}', [ItemController::class, 'update']);
    Route::delete('/{id}', [ItemController::class, 'delete']);
    Route::delete('/deleteImage/{image}', [ItemController::class, 'DeleteImage']);
});

Route::prefix('/basket')->
group(function () {
    Route::post('/', [BasketController::class, 'store']);
    Route::delete('/{id}', [BasketController::class, 'delete']);
    Route::get('/show/{user_id}', [BasketController::class, 'show']);
});

Route::prefix('/likes')->
group(function () {
    Route::get('/{item}', [LikesController::class, 'index']);
    Route::post('/', [LikesController::class, 'store']);
    Route::get('/show/{id}', [LikesController::class, 'show']);
    Route::post('/{like}', [LikesController::class, 'update']);
    Route::delete('/{like}', [LikesController::class, 'delete']);
    Route::post('/check', [LikesController::class, 'isLiked']);
});

Route::prefix('/bans')->
group(function () {
    Route::get('/', [UserBannesController::class, 'index']);
    Route::get('/{user}', [UserBannesController::class, 'index_by_user']);
    Route::post('/', [UserBannesController::class, 'store']);
    Route::post('/{ban}', [UserBannesController::class, 'update']);
    Route::delete('/{ban}', [UserBannesController::class, 'delete']);
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/user-profile', [AuthController::class, 'userProfile']);
});
