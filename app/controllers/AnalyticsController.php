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

  public function trackOpen()
  {
    $source = Route::input('source');
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
}
