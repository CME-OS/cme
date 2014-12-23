<?php
namespace Cme\Web\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class HomeController extends BaseController
{
  public function index()
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

    $time    = strtotime("-7 days midnight");
    $endTime = strtotime(date('Y-m-d 23:59:59'));
    $events  = DB::select(
      "SELECT * FROM campaign_events WHERE time >= $time"
    );

    $dStats = []; //daily stats
    $hStats = []; //hourly stats
    foreach($events as $event)
    {
      if(!isset($dStats[$event->campaign_id]))
      {
        //build blank slate for daily stats
        $startTime = $time;
        for($s = $startTime; $s <= $endTime; $s += 86400)
        {
          $day = date('Y-m-d', $s);
          foreach($eventTypes as $type)
          {
            $dStats[$event->campaign_id][$day][$type] = 0;
          }
        }
      }
      if(!isset($hStats[$event->campaign_id]))
      {
        //build blank slate for hourly stats
        $startTime = strtotime(date('Y-m-d 00:00:00'));
        for($s = $startTime; $s <= $endTime; $s += 3600)
        {
          $hour = date('Y-m-d H:00:00', $s);
          foreach($eventTypes as $type)
          {
            $hStats[$event->campaign_id][$hour][$type] = 0;
          }
        }
      }

      $day = date('Y-m-d', $event->time);
      if(isset($dStats[$event->campaign_id][$day]))
      {
        $dStats[$event->campaign_id][$day][$event->event_type]++;
      }

      $hour = date('Y-m-d H:00:00', $event->time);
      if(isset($hStats[$event->campaign_id][$hour]))
      {
        $hStats[$event->campaign_id][$hour][$event->event_type]++;
      }
    }

    /**@todo order by created or send time!?*/
    $campaignLookUp         = DB::table('campaigns')->orderBy('created', 'desc')->lists('subject', 'id');
    $data['eventTypes']     = $eventTypes;
    $data['dStats']         = $dStats;
    $data['hStats']         = $hStats;
    $data['campaignLookUp'] = $campaignLookUp;

    return View::make('home', $data);
  }
}
