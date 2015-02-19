<?php
namespace Cme\Web\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class HomeController extends BaseController
{
  public function index()
  {
    $eventTypes = [
      'queued',
      'sent',
      'opened',
      /*'clicked',*/
      'failed',
      'bounced',
      'unsubscribed'
    ];

    $stats          = [];
    $campaigns      = DB::select(
      'SELECT * FROM campaigns ORDER BY send_time DESC LIMIT 5'
    );
    $campaignLookUp = [];
    foreach($campaigns as $campaign)
    {
      $lastId = 0;
      do
      {
        $events = DB::select(
          "SELECT * FROM campaign_events
           WHERE event_id > $lastId
           AND campaign_id = $campaign->id
           ORDER BY event_id ASC LIMIT 1000"
        );
        foreach($events as $event)
        {
          if(!isset($stats[$event->campaign_id]))
          {
            foreach($eventTypes as $type)
            {
              $stats[$event->campaign_id][$type] = 0;
            }
          }

          if(isset($stats[$event->campaign_id][$event->event_type]))
          {
            $stats[$event->campaign_id][$event->event_type]++;
          }

          $stats[$event->campaign_id]['opened_rate'] = $this->_percentage(
            $stats[$event->campaign_id]['opened'],
            $stats[$event->campaign_id]['sent']
          );

          $lastId = $event->event_id;
        }
      }
      while($events);

      $campaignLookUp[$campaign->id] = $campaign->subject;
    }

    $data['eventTypes']     = $eventTypes;
    $data['stats']          = $stats;
    $data['campaignLookUp'] = $campaignLookUp;

    //get total stats
    $totalStats = DB::select(
      "SELECT count(*) as total, event_type FROM campaign_events GROUP BY event_type"
    );

    $data['totalStats'] = $totalStats;

    return View::make('dashboard.home', $data);
  }

  private function _percentage($a, $b)
  {
    $result = "~";
    if($b > 0)
    {
      $result = number_format((($a / $b) * 100), 2) . '%';
    }
    return $result;
  }
}
