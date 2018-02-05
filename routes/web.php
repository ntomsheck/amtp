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

Route::get('/', 'TestController@index');

Route::post('/start', 'TestController@start');

Route::any('/test', 'TestController@portTest');

Route::any('/test/connectivity', 'TestController@connectivity');

Route::post('/test/dhcp', 'TestController@dhcp');

Route::post('/test/dns', 'TestController@dns');

Route::any('/test/checkDns', 'TestController@checkDns');

Route::post('/test/routing', 'TestController@routing');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
