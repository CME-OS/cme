<?php
namespace Cme\Web\Controllers;

use CmeKernel\Core\CmeKernel;
use CmeKernel\Enums\EventType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class HomeController extends BaseController
{
  public function index()
  {
    $eventTypes = EventType::getPossibleValues();

    // we don't want clicks, tests and unknowns showing up on the dashboard
    // so we unset them here
    foreach($eventTypes as $i => $type)
    {
      if(in_array(
        $type,
        [EventType::CLICKED, EventType::TEST, EventType::UNKNOWN]
      ))
      {
        unset($eventTypes[$i]);
      }
    }

    $stats          = [];
    $campaigns      = DB::select(
      'SELECT * FROM campaigns
       WHERE deleted_at IS NULL
       ORDER BY send_time DESC LIMIT 6'
    );
    $campaignLookUp = [];
    foreach($campaigns as $campaign)
    {
      $stats[$campaign->id] = CmeKernel::Analytics()->getEventCounts(
        $campaign->id
      );

      $stats[$campaign->id]['opened_rate'] = $this->_percentage(
        $stats[$campaign->id]['opened']['unique'],
        $stats[$campaign->id]['sent']['unique']
      );
      if(!isset($campaignLookUp[$campaign->id]))
      {
        $campaignLookUp[$campaign->id] = new \stdClass();
      }
      $campaignLookUp[$campaign->id]->name     = $campaign->name;
      $campaignLookUp[$campaign->id]->sendTime = $campaign->send_time;
    }

    $data['eventTypes']     = $eventTypes;
    $data['stats']          = $stats;
    $data['campaignLookUp'] = $campaignLookUp;

    // for the total stats, we don't want to show failed and bounced stats
    // so we unset them here
    $totalEventTypes = $eventTypes;
    foreach($totalEventTypes as $i => $type)
    {
      if(in_array($type, [EventType::FAILED, EventType::BOUNCED]))
      {
        unset($totalEventTypes[$i]);
      }
    }

    //create Blank State
    $totalStats = [];
    foreach($totalEventTypes as $type)
    {
      $event             = new \stdClass();
      $event->total      = 0;
      $event->event_type = $type;
      $totalStats[$type] = $event;
    }

    //get this month total stats
    $monthStart = strtotime(date('Y-m-01 00:00:00'));
    $monthEnd   = strtotime(date('Y-m-t 23:59:59'));
    $result     = DB::select(
      "SELECT count(*) as total, event_type
      FROM campaign_events
      WHERE event_type IN ('" . implode("','", $totalEventTypes) . "')
      AND time BETWEEN $monthStart AND $monthEnd
      GROUP BY event_type"
    );

    //make sure we have a row for every event type
    //taking care of the 'Blank State'
    if($result)
    {
      foreach($result as $event)
      {
        if(isset($totalStats[$event->event_type]))
        {
          $totalStats[$event->event_type]->total = $event->total;
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
