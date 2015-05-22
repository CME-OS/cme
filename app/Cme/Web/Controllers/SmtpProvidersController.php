<?php
namespace Cme\Web\Controllers;

use CmeData\SmtpProviderData;
use CmeKernel\Core\CmeKernel;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;
use \Illuminate\Support\Facades\Input;
use \Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;

class SmtpProvidersController extends BaseController
{
  public function index()
  {
    $data['smtpProviders'] = CmeKernel::SmtpProvider()->all();
    return View::make('smtp.list', $data);
  }

  public function neww()
  {
    return View::make('smtp.new');
  }

  public function add()
  {
    $data = Input::all();
    CmeKernel::SmtpProvider()->create(
      SmtpProviderData::hydrate($data),
      Config::get('app.key')
    );
    return Redirect::route('smtp-providers');
  }

  public function edit($id)
  {
    $provider             = CmeKernel::SmtpProvider()->get($id);
    $provider->username   = Crypt::decrypt($provider->username);
    $data['smtpProvider'] = $provider;
    return View::make('smtp.edit', $data);
  }

  public function update()
  {
    $data = Input::all();
    CmeKernel::SmtpProvider()->update(
      SmtpProviderData::hydrate($data),
      Config::get('key')
    );
    return Redirect::to('/smtp-providers/edit/' . $data['id'])->with(
      'msg',
      'SMTP Provider has been updated'
    );
  }

  public function setDefault($id)
  {
    CmeKernel::SmtpProvider()->setAsDefault($id);
    return Redirect::route('smtp-providers');
  }

  public function delete($id)
  {
    CmeKernel::SmtpProvider()->delete($id);
    return Redirect::to('/smtp-providers')->with(
      'msg',
      'SMTP Provider has been deleted'
    );
  }
}
