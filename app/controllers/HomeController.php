<?php

class HomeController extends BaseController
{
  public function index()
  {
    $reportType = [7, 30];
    $eventTypes = [
      'failed',
      'queued',
      'sent',
      'opened',
      'clicked',
      'bounced',
      'unsubscribed'
    ];

    foreach($reportType as $period)
    {
      $time = strtotime("-$period days");
      $events       = DB::select(
        $x = "SELECT * FROM campaign_events WHERE time >= $time"
      );

      $stats[$period] = [];
      foreach($events as $event)
      {
        if(!isset($stats[$period][$event->campaign_id]))
        {
          foreach($eventTypes as $type)
          {
            $stats[$period][$event->campaign_id][$type] = 0;
          }
        }

        $stats[$period][$event->campaign_id][$event->event_type]++;
      }
    }


    $campaignLookUp = DB::table('campaigns')->lists('subject', 'id');
    $data['stats'] = $stats;
    $data['campaignLookUp'] = $campaignLookUp;

    return View::make('home', $data);
  }
}
