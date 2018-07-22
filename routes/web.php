<?php

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
    /*
    App\Administrator::create([
        'email' => 'admin@admin.com',
        'name' => 'admin',
        'password' => bcrypt('admin@2017'),
    ]);
    */
    return '';
    //return view('welcome');
});
Route::get('login', 'Api\AuthController@login')->name('login');

