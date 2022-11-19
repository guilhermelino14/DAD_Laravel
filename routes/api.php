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

Route::middleware('auth:api')->get('/userteste', function (Request $request) {
    return $request->user();
});


Route::resource('product', 'App\Http\Controllers\ProductController',['only' => ['show','index']]);

Route::resource('product', 'App\Http\Controllers\ProductController',['except' => ['show','index']])->middleware(['auth:api', 'ManagerVerification']);

Route::resource('orders', 'App\Http\Controllers\OrderController')->middleware('cors');
Route::resource('user', 'App\Http\Controllers\UserController');
Route::resource('customer', 'App\Http\Controllers\CustomerController');
Route::resource('order_item', 'App\Http\Controllers\Order_itemController'); 

Route::get('product/photo/{photo}', 'App\Http\Controllers\ProductController@photo');
Route::get('user/photo/{photo}', 'App\Http\Controllers\UserController@photo');

Route::post('login', 'App\Http\Controllers\AuthController@login');
Route::post('register', 'App\Http\Controllers\AuthController@register');
Route::post('logout', 'App\Http\Controllers\AuthController@logout')->middleware('auth:api');
Route::get('userType', 'App\Http\Controllers\AuthController@userType')->middleware('auth:api');

Route::get('ordersPreparingOrReady', 'App\Http\Controllers\OrderController@getOrdersPreparingOrReady')->middleware('cors');
Route::put('orderUpdate', 'App\Http\Controllers\OrderController@orderUpdate')->middleware('cors');
Route::put('orderItemUpdate', 'App\Http\Controllers\Order_itemController@orderItemUpdate')->middleware('cors');
Route::get('showOrderwithId/{id}', 'App\Http\Controllers\OrderController@showOrderwithId')->middleware('cors');