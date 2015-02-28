<?php
namespace Cme\Web\Controllers;

use Cme\Helpers\InstallerHelper;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\View;

class SetupController extends BaseController
{
  public function index()
  {
    if(InstallerHelper::isCMEInstalled())
    {
      return View::make('setup.installed');
    }
    return View::make('setup.step1');
  }

  public function install()
  {
    InstallerHelper::$domain     = Request::server('HTTP_HOST');
    InstallerHelper::$dbName     = Input::get('dbName');
    InstallerHelper::$dbHost     = Input::get('dbHost');
    InstallerHelper::$dbUser     = Input::get('dbUser');
    InstallerHelper::$dbPassword = Input::get('dbPass');
    InstallerHelper::$awsKey     = Input::get('awsKey');
    InstallerHelper::$awsSecret  = Input::get('awsSecret');
    InstallerHelper::$awsRegion  = Input::get('awsRegion');

    InstallerHelper::createEnvFile('stage');
    InstallerHelper::installDb(InstallerHelper::getInstallClasses());
    InstallerHelper::createUser('admin', 'admin');
    InstallerHelper::writeInstallFlag();

    return Redirect::to('/setup');
  }

  public function skip()
  {
    InstallerHelper::writeInstallFlag();
    return Redirect::intended('/');
  }
}
