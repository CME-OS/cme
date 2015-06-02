<?php
namespace Cme\Web\Controllers;

use Cme\Models\CMEUser;
use CmeData\UserData;
use CmeKernel\Core\CmeKernel;
use CmeKernel\Exceptions\InvalidDataException;
use \Illuminate\Support\Facades\Input;
use \Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
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
    $data = Session::get('formData', ['input' => null, 'errors' => null]);
    return View::make('users.new', $data);
  }

  public function add()
  {
    $data             = Input::all();
    $userData         = UserData::hydrate($data);
    try
    {
      CmeKernel::User()->create($userData);
      return Redirect::to('/users');
    }
    catch(InvalidDataException $e)
    {
      return Redirect::to('/users/new')->with(
        'formData',
        [
          'input'  => Input::all(),
          'errors' => $userData->getValidationErrors()
        ]
      );
    }
  }

  public function edit($id)
  {
    $data['user'] = CmeKernel::User()->get($id);
    $data         = array_merge(
      $data,
      Session::get('formData', ['input' => null, 'errors' => null])
    );
    return View::make('users.edit', $data);
  }

  public function update()
  {
    $userData = UserData::hydrate(Input::all());
    try
    {
      CmeKernel::User()->update($userData);
      return Redirect::to('/users');
    }
    catch(InvalidDataException $e)
    {
      return Redirect::to('/users/edit/' . $userData->id)->with(
        'formData',
        [
          'input'  => Input::all(),
          'errors' => $userData->getValidationErrors()
        ]
      );
    }
  }

  public function delete($id)
  {
    CmeKernel::User()->delete($id);
    return Redirect::to('/users');
  }
}
