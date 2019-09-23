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

// 產品
Route::resource('products', 'ProductController');

// 訂單
Route::post('orders/keyword/receive', 'OrderController@multipleReceive');
Route::post('orders/keyword/', 'OrderController@keyword');
Route::post('orders/keyword/exact', 'OrderController@exactSearch');
Route::delete('orders/keyword/delete', 'OrderController@deleteSearch');
Route::get('orders/create/{id}', 'OrderController@create');
Route::put('orders/receive/{id}', 'OrderController@updateReceive');
Route::resource('orders', 'OrderController');

// 存貨
Route::get('stockReports/create/{id}', 'StockReportController@create');
Route::resource('stockReports', 'StockReportController');

// 報表
Route::post('reports/search', 'ReportController@search');
Route::resource('reports', 'ReportController');


Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
