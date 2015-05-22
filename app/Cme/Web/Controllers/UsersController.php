<?php
namespace Cme\Web\Controllers;

use Cme\Models\CMEUser;
use CmeData\UserData;
use CmeKernel\Core\CmeKernel;
use \Illuminate\Support\Facades\Input;
use \Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class UsersController extends BaseController
{
  public function index()
  {
    $data['users'] = CMEUser::all();
    return View::make('users.list', $data);
  }

  public function neww()
  {
    return View::make('users.new');
  }

  public function add()
  {
    $data             = Input::all();
    $data['password'] = Hash::make($data['password']);
    CmeKernel::User()->create(UserData::hydrate($data));
    return Redirect::to('/users');
  }

  public function edit($id)
  {
    $data['user'] = CmeKernel::User()->get($id);
    return View::make('users.edit', $data);
  }

  public function update()
  {
    CmeKernel::User()->update(Input::all());
    return Redirect::to('/users');
  }

  public function delete($id)
  {
    CmeKernel::User()->delete($id);
    return Redirect::to('/users');
  }
}
