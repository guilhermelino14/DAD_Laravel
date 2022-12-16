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

Route::resource('products', 'App\Http\Controllers\ProductController',['only' => ['show','index']]);
Route::get('products/photo/{photo}', 'App\Http\Controllers\ProductController@photo');

Route::resource('products', 'App\Http\Controllers\ProductController',['except' => ['show','index']])->middleware(['auth:api', 'ManagerVerification']);

Route::resource('orders', 'App\Http\Controllers\OrderController')->middleware('cors');
Route::get('users/photo/{photo}', 'App\Http\Controllers\UserController@photo');

Route::post('login', 'App\Http\Controllers\AuthController@login');
Route::post('register', 'App\Http\Controllers\AuthController@register');

Route::group(['middleware' => ['auth:api']], function() {
    
    Route::resource('users', 'App\Http\Controllers\UserController');
    Route::resource('customers', 'App\Http\Controllers\CustomerController');
    Route::resource('order_items', 'App\Http\Controllers\Order_itemController'); 


    Route::get('ordersPreparingOrReady', 'App\Http\Controllers\OrderController@getOrdersPreparingOrReady')->middleware('cors');
    Route::get('getOrdersToPublicBoard', 'App\Http\Controllers\OrderController@getOrdersToPublicBoard')->middleware('cors');

    Route::put('orderUpdate', 'App\Http\Controllers\OrderController@orderUpdate')->middleware('cors');
    Route::put('orderItemUpdate', 'App\Http\Controllers\Order_itemController@orderItemUpdate')->middleware('cors');
    Route::get('showOrderwithId/{id}', 'App\Http\Controllers\OrderController@showOrderwithId')->middleware('cors');

    Route::get('costumerGetUser/{id}', 'App\Http\Controllers\CustomerController@costumerGetUser')->middleware('cors');


    Route::resource('statistics', 'App\Http\Controllers\StatisticsController')->middleware('cors');
    Route::get('statistics/totalEarn/D', 'App\Http\Controllers\StatisticsController@totalEarn');
    Route::get('statistics/totalEarn/Mounth', 'App\Http\Controllers\StatisticsController@totalEarnLast3Months');
    Route::get('statistics/totalSpent/{id}', 'App\Http\Controllers\StatisticsController@totalSpent');
    Route::get('statistics/totalSpentPoints/{id}', 'App\Http\Controllers\StatisticsController@totalSpentPoints');
    Route::get('statistics/totalPointsEarned/{id}', 'App\Http\Controllers\StatisticsController@totalPointsEarned')->middleware('auth:api');

    Route::post('logout', 'App\Http\Controllers\AuthController@logout');
    Route::get('userType', 'App\Http\Controllers\AuthController@userType');
});