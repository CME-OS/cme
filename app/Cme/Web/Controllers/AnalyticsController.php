<?php
namespace Cme\Web\Controllers;

use Cme\Models\CMECampaign;
use Cme\Models\CMECampaignEvent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class AnalyticsController extends BaseController
{
  public function index($id)
  {
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
    $events = CMECampaignEvent::getCampaignEvents($id);

    foreach($eventTypes as $type)
    {
      $stats[$type] = 0;
    }
    foreach($events as $event)
    {
      if(isset($stats[$event->event_type]))
      {
        $stats[$event->event_type]++;
      }
    }

    return $stats;
  }
}
