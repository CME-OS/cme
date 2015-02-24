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
    $counted        = [];
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
           AND subscriber_id > 0
           ORDER BY event_id ASC LIMIT 1000"
        );
        foreach($events as $event)
        {
          $c = $event->campaign_id;
          $e = $event->event_type;
          $s = $event->subscriber_id;

          if(!isset($stats[$c]))
          {
            foreach($eventTypes as $type)
            {
              $stats[$c][$type]['unique'] = 0;
              $stats[$c][$type]['total']  = 0;
            }
          }

          if(isset($stats[$c][$e]))
          {
            if(!isset($counted[$c][$e][$s]))
            {
              $counted[$c][$e][$s] = 1;
              $stats[$event->campaign_id][$event->event_type]['unique']++;
            }
            else
            {
              $stats[$event->campaign_id][$event->event_type]['total']++;
            }
          }

          //always show total queued
          $stats[$c]['queued']['unique'] = $stats[$c]['queued']['total'];

          $stats[$event->campaign_id]['opened_rate'] = $this->_percentage(
            $stats[$event->campaign_id]['opened']['unique'],
            $stats[$event->campaign_id]['sent']['total']
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
