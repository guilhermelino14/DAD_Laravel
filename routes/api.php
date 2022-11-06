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

// Route::middleware('auth:sanctum')->get('/userteste', function (Request $request) {
//     return $request->user();
// });


Route::resource('product', 'App\Http\Controllers\ProductController',['only' => ['show','index']]);

Route::resource('product', 'App\Http\Controllers\ProductController',['except' => ['show','index']])->middleware(['auth:sanctum', 'ManagerVerification']);

Route::resource('order', 'App\Http\Controllers\OrderController');
Route::resource('user', 'App\Http\Controllers\UserController');
Route::resource('customer', 'App\Http\Controllers\CustomerController');
Route::resource('order_item', 'App\Http\Controllers\Order_itemController'); 

Route::get('product/photo/{photo}', 'App\Http\Controllers\ProductController@photo');

Route::post('login', 'App\Http\Controllers\AuthController@login');
Route::post('register', 'App\Http\Controllers\AuthController@register');
Route::post('logout', 'App\Http\Controllers\AuthController@logout')->middleware('auth:sanctum');

