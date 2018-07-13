<?php
namespace App\Cme\Web\Controllers;

use App\Cme\Helpers\InstallerHelper;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\View;

class BaseController extends Controller
{

  public function __construct()
  {
    if(InstallerHelper::isCMEInstalled())
    {
      $this->_getCMEState();
    }
  }

  /**
   * Setup the layout used by the controller.
   *
   * @return void
   */
  protected function setupLayout()
  {
    if(!is_null($this->layout))
    {
      $this->layout = View::make($this->layout);
    }
  }

  protected function _getCMEState()
  {
    $lists     = DB::select(
      'SELECT count(*) as count FROM lists WHERE deleted_at IS NULL'
    );
    $brands    = DB::select(
      'SELECT count(*) as count FROM brands WHERE brand_deleted_at IS NULL'
    );
    $campaigns = DB::select(
      'SELECT count(*) as count FROM campaigns WHERE deleted_at IS NULL'
    );

    $data                    = new \stdClass();
    $data->enableList        = !($lists[0]->count > 0);
    $data->enableBrand       = $brands[0]->count <= 0 && $lists[0]->count > 0;
    $data->enableCampaign    = $brands[0]->count > 0 && $lists[0]->count > 0;
    $data->listCompleted     = $lists[0]->count > 0;
    $data->brandCompleted    = $brands[0]->count > 0;
    $data->campaignCompleted = $campaigns[0]->count > 0;
    $data->showWizardButton  = (!$data->listCompleted || !$data->brandCompleted)
      && Request::path() != '/';

    View::share('state', $data);
  }
}
