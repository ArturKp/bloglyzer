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

Route::get('/', function () {
	// Bloglyzer\Models\Post::where('url', 'like', '%marimell%')->delete();
    return \Response::json(Bloglyzer\Models\Post::take(1)->skip(rand(0,1000))->first());
});
