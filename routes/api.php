<?php

use Illuminate\Http\Request;

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
# 后台接口
Route::namespace('Api\Administrator')->name('admin.')->prefix('admin')->middleware('auth:admin')->group(function () {
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::get('me', 'AuthController@me');
    Route::resource('user', 'UserController');
    Route::resource('shop', 'ShopController');
    Route::resource('bargain', 'BargainController');
    Route::post('items/{id}/publish', 'ItemController@publish')->name('item.publish');
    Route::resource('items', 'ItemController');
});
# 后台登陆
Route::namespace('Api')->name('admin.')->prefix('admin')->group(function () {
    Route::post('login', 'Administrator\AuthController@login')->name('login');
});


# 小程序接口,需要认证
Route::namespace('Api')->prefix('v1')->middleware('auth:api')->group(function () {
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::get('me', 'AuthController@me');
    Route::post('update', 'AuthController@update');
    Route::post('exchange/{id}', 'BargainController@exchange')->name('exchange');
    Route::post('help/{id}', 'BargainController@help')->name('help');
    Route::post('create/{id}', 'BargainController@create');
    Route::post('me/bargains', 'BargainController@index');
});
# 小程序接口，无需认证
Route::namespace('Api')->prefix('v1')->group(function () {
    Route::any('login', 'AuthController@login');
    Route::get('shop/{id}', 'ShopController@view');
    Route::get('index', 'ShopController@index');
    # 砍价的具体页面
    Route::get('bargain/{id}', 'BargainController@view')->name('bargain');
    Route::get('bargain/{id}/users', 'BargainController@bargainUsers');
});
