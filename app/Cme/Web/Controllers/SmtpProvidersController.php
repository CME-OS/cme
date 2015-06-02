<?php
namespace Cme\Web\Controllers;

use CmeData\SmtpProviderData;
use CmeKernel\Core\CmeKernel;
use CmeKernel\Exceptions\InvalidDataException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;
use \Illuminate\Support\Facades\Input;
use \Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
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
    $data = Session::get('formData', ['input' => null, 'errors' => null]);
    return View::make('smtp.new', $data);
  }

  public function add()
  {
    $smtpData = SmtpProviderData::hydrate(Input::all());
    try
    {
      CmeKernel::SmtpProvider()->create(
        $smtpData,
        Config::get('app.key')
      );
      return Redirect::route('smtp-providers');
    }
    catch(InvalidDataException $e)
    {
      return Redirect::to('/smtp-providers/new')->with(
        'formData',
        [
          'input'  => Input::all(),
          'errors' => $smtpData->getValidationErrors()
        ]
      );
    }
  }

  public function edit($id)
  {
    $provider             = CmeKernel::SmtpProvider()->get($id);
    $provider->username   = Crypt::decrypt($provider->username);
    $data['smtpProvider'] = $provider;

    $data = array_merge(
      $data,
      Session::get('formData', ['input' => null, 'errors' => null])
    );

    return View::make('smtp.edit', $data);
  }

  public function update()
  {
    $smtpData = SmtpProviderData::hydrate(Input::all());
    try
    {
      CmeKernel::SmtpProvider()->update(
        $smtpData,
        Config::get('app.key')
      );
      return Redirect::to('/smtp-providers/edit/' . $smtpData->id)->with(
        'msg',
        'SMTP Provider has been updated'
      );
    }
    catch(InvalidDataException $e)
    {
      return Redirect::to('/smtp-providers/edit/' . $smtpData->id)->with(
        'formData',
        [
          'input'  => Input::all(),
          'errors' => $smtpData->getValidationErrors()
        ]
      );
    }
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
