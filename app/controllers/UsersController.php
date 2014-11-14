<?php
use \Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Route;
use \Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Schema;

class UsersController extends BaseController
{
  public function index()
  {
    $data = [];
    return View::make('users.index', $data);
  }
}
