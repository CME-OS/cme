<?php
namespace Cme\Web\Controllers;

use Cme\Models\CMECampaign;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;

class AnalyticsController extends BaseController
{
  public function index()
  {
    $id = (int)Route::input('id');
    $data['selectedId'] = $id;
    $data['stats']      = $this->_getStats($id);
    $data['campaigns']  = CMECampaign::getKeyedListFor('subject');
    return View::make('analytics.index', $data);
  }

  private function _getStats($id)
  {
    $eventTypes = [
      'queued',
      'sent',
      'opened',
      'clicked',
      'failed',
      'bounced',
      'unsubscribed'
    ];

    $stats  = [];

    foreach($eventTypes as $type)
    {
      $stats[$type] = 0;
    }

    $lastId = 0;
    do
    {
      $events = DB::select(
        "SELECT * FROM campaign_events
         WHERE event_id > $lastId
         AND campaign_id = $id
         ORDER BY event_id ASC LIMIT 1000"
      );
      foreach($events as $event)
      {
        if(isset($stats[$event->event_type]))
        {
          $stats[$event->event_type]++;
        }
        $lastId = $event->event_id;
      }
    }
    while($events);

    return $stats;
  }
}
