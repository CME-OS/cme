<?php
namespace Cme\Web\Controllers;

use Cme\Helpers\InstallerHelper;
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
    if(InstallerHelper::isCMEInstalled())
    {
      return View::make('login.login');
    }
    return Redirect::to('/setup');
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

  public function logout()
  {
    Auth::logout();
    return Redirect::to('/login');
  }
}
