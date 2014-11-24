<?php

class LoginController extends BaseController
{
  public function login()
  {
    return View::make('login');
  }

  public function authenticate()
  {
    $formData = Input::only('email', 'password');
    if (Auth::attempt($formData)) {
      return Redirect::intended('/');
    }
    return Redirect::route('login');
  }
}
