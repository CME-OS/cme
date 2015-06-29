<?php
namespace Cme\Web\Controllers;

use Cme\Helpers\InstallerHelper;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;

class SetupController extends BaseController
{
  public function index()
  {
    if(InstallerHelper::isCMEInstalled())
    {
      return Redirect::to('/login');
    }
    $step = Route::input('step', 1);
    if($step == 1 || ($step > 1 && InstallerHelper::hostMeetsRequirements()))
    {
      switch($step)
      {
        case 1:
          $data['installReady'] = InstallerHelper::hostMeetsRequirements();
          return View::make('setup.step1', $data);
        case 2:
          $data = Session::get(
            'redirectData',
            ['error' => null, 'formData' => null]
          );
          return View::make('setup.step2', $data);
        case 3:
          $data = Session::get(
            'redirectData',
            ['errors' => null, 'formData' => null]
          );
          return View::make('setup.step3', $data);
        case 4:

          $data['crontab'] = InstallerHelper::generateCrontabConfig();
          $data['monit']   = InstallerHelper::generateMonitConfig();
          InstallerHelper::writeInstallFlag();
          return View::make('setup.step4', $data);
      }
    }
    return Redirect::to('/setup');
  }

  public function install()
  {
    InstallerHelper::$domain = Request::server('HTTP_HOST');
    InstallerHelper::$dbName = Input::get('dbName');
    InstallerHelper::$dbHost = Input::get('dbHost');
    InstallerHelper::$dbUser = Input::get('dbUser');
    InstallerHelper::$dbPassword = Input::get('dbPass');
    InstallerHelper::$awsKey = Input::get('awsKey');
    InstallerHelper::$awsSecret = Input::get('awsSecret');
    InstallerHelper::$awsRegion = Input::get('awsRegion');

    //test db connection
    try
    {
      if(mysqli_connect(
        InstallerHelper::$dbHost,
        InstallerHelper::$dbUser,
        InstallerHelper::$dbPassword
      )
      )
      {
        InstallerHelper::createEnvFile();
        InstallerHelper::createCommanderConfigFile();
        InstallerHelper::installDb(InstallerHelper::getInstallClasses());
        return Redirect::to('/setup/3');
      }
    }
    catch(\Exception $e)
    {
      $error = "CME cannot seem to connect to your database. "
        . "Please check that you have entered the right details";

      Log::error("MySQL ERROR: " . $e->getMessage());

      return Redirect::to('/setup/2')->with(
        'redirectData',
        ['error' => $error, 'formData' => Input::all()]
      );
    }
  }

  public function createUser()
  {
    try
    {
      InstallerHelper::createUser(
        Input::get('email'),
        Input::get('password')
      );
      return Redirect::to('/setup/4');
    }
    catch(\Exception $e)
    {
      return Redirect::to('/setup/3')->with(
        'redirectData',
        ['errors' => $e->errors, 'formData' => Input::all()]
      );
    }
  }

  public function installed()
  {
    $data['username'] = 'admin@' . Request::server('HTTP_HOST');
    return View::make('setup.installed', $data);
  }

  public function skip()
  {
    InstallerHelper::writeInstallFlag();
    return Redirect::intended('/');
  }
}
