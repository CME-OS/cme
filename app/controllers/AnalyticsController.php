<?php

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Route;

class AnalyticsController extends BaseController
{
  public function index()
  {
    $data = [];
    return View::make('analytics.index', $data);
  }

  public function trackOpen($source)
  {
    list($campaignId, $listId, $subscriberId) = explode('_', $source);

    if($campaignId && $listId && $subscriberId)
    {
      DB::table('campaign_events')->insert(
        [
        'campaign_id'   => $campaignId,
        'list_id'       => $listId,
        'subscriber_id' => $subscriberId,
        'event_type'    => 'opened',
        'time'          => time()
        ]
      );
    }
  }

  public function trackUnsubscribe($source, $redirect)
  {
    list($campaignId, $listId, $subscriberId) = explode('_', $source);
    if($campaignId && $listId && $subscriberId)
    {
      DB::table('campaign_events')->insert(
        [
        'campaign_id'   => $campaignId,
        'list_id'       => $listId,
        'subscriber_id' => $subscriberId,
        'event_type'    => 'unsubscribed',
        'time'          => time()
        ]
      );

      if(ListHelper::tableExists($listId))
      {
        DB::table(ListHelper::getTable($listId))
          ->where('id', '=', $subscriberId)
          ->update(['unsubscribed' => 1]);
      }

      return Redirect::to(base64_decode($redirect));
    }
  }
}
