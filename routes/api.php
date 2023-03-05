<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\ProductController;
use App\Http\Controllers\Api\v1\CartController;

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


Route::prefix('product')
	->group(function () {
		Route::get('index',[ProductController::class,'index']);
		Route::post('store',[ProductController::class,'store']);
		Route::post('update',[ProductController::class,'update']);
		Route::post('delete',[ProductController::class,'delete']);
	});
Route::prefix('cart')
	 ->group(function () {
		 Route::get('index',[CartController::class,'index']);
		 Route::post('store',[CartController::class,'addProductsToCartItems']);
		 Route::post('deleteCartItem',[CartController::class,'deleteCartItem']);
		 Route::post('deleteAllCart',[CartController::class,'deleteAllCart']);
	 });

