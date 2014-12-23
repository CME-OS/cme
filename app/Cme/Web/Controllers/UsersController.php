<?php
namespace Cme\Web\Controllers;

use Cme\Models\CMEUser;
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
    $data               = Input::all();
    $data['password']   = Hash::make($data['password']);
    $data['created_at'] = date('Y-m-d H:i:s');
    $data['updated_at'] = date('Y-m-d H:i:s');
    $data['active']     = 1;
    $userId             = DB::table('users')->insertGetId($data);

    return Redirect::to('/users');
  }

  public function edit($id)
  {
    $data['user'] = CMEUser::find($id);
    return View::make('users.edit', $data);
  }

  public function update()
  {
    //TODO: write logic for updating users
    //need to think of which fields should be updatable
    return Redirect::to('/users');
  }

  public function delete($id)
  {
    CMEUser::destroy($id);
    return Redirect::to('/users');
  }
}
