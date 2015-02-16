<?php
namespace Cme\Web\Controllers;

use Cme\Models\CMESmtpProvider;
use Illuminate\Support\Facades\DB;
use \Illuminate\Support\Facades\Input;
use \Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;

class SmtpProvidersController extends BaseController
{
  public function index()
  {
    $result = CMESmtpProvider::getAllActive();

    $data['smtpProviders'] = $result;

    return View::make('smtp.list', $data);
  }

  public function neww()
  {
    return View::make('smtp.new');
  }

  public function add()
  {
    $data = Input::all();
    CMESmtpProvider::insert($data);

    return Redirect::route('smtp-providers');
  }

  public function edit($id)
  {
    $data['smtpProvider'] = CMESmtpProvider::find($id);

    return View::make('smtp.edit', $data);
  }

  public function update()
  {
    $data = Input::all();
    if($data['password'] == "")
    {
      unset($data['password']);
    }
    DB::table('smtp_providers')->where('id', '=', $data['id'])
      ->update($data);

    return Redirect::to('/smtp-providers/edit/' . $data['id'])->with(
      'msg',
      'SMTP Provider has been updated'
    );
  }

  public function setDefault($id)
  {
    DB::table('smtp_providers')
      ->update(['default' => 0]);

    DB::table('smtp_providers')->where('id', '=', $id)
      ->update(['default' => 1]);

    return Redirect::route('smtp-providers');
  }

  public function delete($id)
  {
    $data['deleted_at'] = time();
    DB::table('smtp_providers')->where('id', '=', $id)
      ->update($data);

    return Redirect::to('/smtp-providers')->with('msg', 'SMTP Provider has been deleted');
  }
}