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
	Route::get('login', array('uses' => 'AuthController@showLogin'));
	Route::post('login', array('uses' => 'AuthController@doLogin'));
});

Route::group(['middleware' => 'auth'], function () {

	Route::get('logout', array('uses' => 'AuthController@doLogout'));

	Route::get('/', array('uses' => 'HomeController@showHome'));

});

