<?php

namespace Bloglyzer\Http\Controllers;

use Bloglyzer\User;
use Validator;
use Bloglyzer\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    public function showLogin()
    {
        return \View::make('login');
    }

    public function doLogin()
    {

        $password  = \Input::get('password');

        if($password == env('ACCESS_PASSWORD')) {
            $user = \Bloglyzer\Models\User::first();
            \Auth::LoginUsingId($user->id, true);
            return \Redirect::to('/');
        }

    }

    public function doLogout()
    {
        \Auth::logout(); // log the user out of our application
        return \Redirect::to('login'); // redirect the user to the login screen
    }
}
