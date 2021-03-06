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

Route::post('/test/clear', 'TestController@cancelTest');

Route::any('/test', 'TestController@portTest');

Route::any('/test/save', 'TestController@store');

Route::any('/test/connectivity', 'TestController@connectivity');

Route::any('/test/checkDns', 'TestController@checkDns');

Route::post('/test/routing', 'TestController@routing');

Route::any('/test/download', 'TestController@throughputDown');

Route::any('getIP.php', 'TestController@throughputGetIP');

Route::any('empty.php', 'TestController@throughputEmpty');

Route::any('/test/complete', 'TestController@complete');

Route::any('/test/results', [
    'as' => 'test.results',
    'uses' => 'TestController@show'
]);



Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
