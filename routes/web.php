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
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::resource('/customer','CustomerController');

Route::resource('/order','OrderController');

Route::resource('/products','ProductController');

Route::get('/bill','OrderController@bill');

Route::get('/delivery','OrderController@delivery');

Route::get('/deleteorder','OrderController@deleteOrderShow');

Route::post('/deleteorder','OrderController@isdeleteOrder');

//Route::post('/customer','CustomerController@update');