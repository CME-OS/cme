<?php
namespace Cme\Web\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;

class LoginController extends BaseController
{
  /**
   * @return mixed
   */
  public function login()
  {
    return View::make('login');
  }

  /**
   * @return mixed
   */
  public function authenticate()
  {
    $formData = Input::only('email', 'password');

    if(Auth::attempt($formData))
    {
      return Redirect::intended('/');
    }
    else
    {
      return Redirect::route('login')->with(
        'message',
        'Something was wrong please try again.'
      );
    }
    //return Redirect::route('login');
  }
}
