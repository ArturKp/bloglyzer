<?php

namespace Bloglyzer\Http\Controllers;

use Bloglyzer\Http\Controllers\Controller;

class HomeController extends Controller {

	public function showHome()
	{
	    return \View::make('home');
	}

}