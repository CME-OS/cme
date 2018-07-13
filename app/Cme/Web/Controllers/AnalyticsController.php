<?php
namespace App\Cme\Web\Controllers;

use App\Cme\Models\CMECampaign;
use CmeKernel\Core\CmeKernel;
use CmeKernel\Enums\EventType;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;

class AnalyticsController extends BaseController
{
  public function index()
  {
    $id                 = (int)Route::input('id');
    $data['selectedId'] = $id;
    if($id)
    {
      $data['stats']        = CmeKernel::Analytics()->getEventCounts($id);
      $data['opens']        = CmeKernel::Analytics()->getLastXOfEvent(
        EventType::OPENED(),
        $id
      );
      $data['unsubscribes'] = CmeKernel::Analytics()->getLastXOfEvent(
        EventType::UNSUBSCRIBED(),
        $id
      );
      $data['clicks']       = CmeKernel::Analytics()->getLinkActivity($id);
      $campaign             = CmeKernel::Campaign()->get($id);
      $campaign->brand      = CmeKernel::Brand()->get($campaign->brandId);
      $campaign->list       = CmeKernel::EmailList()->get($campaign->listId);
      $data['campaign']     = $campaign;
    }
    $data['campaigns'] = Cmekernel::Campaign()->getKeyedListFor('name');
    return View::make('analytics.index', $data);
  }
}
