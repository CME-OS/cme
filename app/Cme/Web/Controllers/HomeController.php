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
      'SELECT * FROM campaigns
       WHERE deleted_at IS NULL
       ORDER BY send_time DESC LIMIT 6'
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
              $stats[$c][$e]['unique']++;
            }

            $stats[$c][$e]['total']++;
          }

          //always show total queued
          $stats[$c]['queued']['unique'] = $stats[$c]['queued']['total'];

          $stats[$c]['opened_rate'] = $this->_percentage(
            $stats[$c]['opened']['unique'],
            $stats[$c]['sent']['unique']
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

    $totalEventTypes = [
      'queued',
      'sent',
      'opened',
      'unsubscribed'
    ];

    //get total stats
    $totalStats = DB::select(
      "SELECT count(*) as total, event_type
      FROM campaign_events
      WHERE event_type IN ('".implode("','", $totalEventTypes)."')
      GROUP BY event_type"
    );

    //make sure we have a row for every event type
    //taking care of the 'Blank State'
    foreach($totalStats as $event)
    {
      foreach($totalEventTypes as $type)
      {
        if(!isset($event->$type))
        {
          $event->$type = 0;
        }
      }
    }

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
