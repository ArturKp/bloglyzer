<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
Route::group(['middleware' => 'guest'], function () {
	Route::get('login', ['uses' => 'AuthController@showLogin']);
	Route::post('login', ['uses' => 'AuthController@doLogin']);
});

Route::group(['middleware' => 'auth'], function () {

	Route::get('logout', ['uses' => 'AuthController@doLogout']);

	Route::get('/', ['uses' => 'HomeController@showHome']);

	Route::get('/statistics', ['uses' => 'StatisticsController@getStatistics']);

	Route::get('/posts', ['as' => 'posts.listing', 'uses' => 'StatisticsController@getPosts']);

	Route::get('/posts/{id}', ['as' => 'posts.single', 'uses' => 'StatisticsController@showPost']);
});

