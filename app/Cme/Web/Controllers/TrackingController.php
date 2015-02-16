<?php
namespace Cme\Web\Controllers;

use Cme\Helpers\ListHelper;
use Cme\Models\CMECampaign;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class TrackingController extends BaseController
{
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
        $subscriber = DB::table(ListHelper::getTable($listId))
          ->where('id', '=', $subscriberId)
          ->first();

        $campaign = CMECampaign::find($campaignId);

        $unsubscribed = DB::table('unsubscribes')
          ->where('email', '=', $subscriber->email)
          ->first();
        if(!$unsubscribed)
        {
          DB::table('unsubscribes')->insert(
            [
              'email'       => $subscriber->email,
              'brand_id'    => $campaign->brand_id,
              'campaign_id' => $campaignId,
              'list_id'     => $listId,
              'time'        => time()
            ]
          );
        }
      }
    }

    return Redirect::to(base64_decode($redirect));
  }

  public function trackClick($source, $redirect)
  {
    list($campaignId, $listId, $subscriberId) = explode('_', $source);
    $redirectTo = base64_decode($redirect);
    if($campaignId && $listId && $subscriberId)
    {
      DB::table('campaign_events')->insert(
        [
          'campaign_id'   => $campaignId,
          'list_id'       => $listId,
          'subscriber_id' => $subscriberId,
          'event_type'    => 'clicked',
          'reference'     => $redirectTo,
          'time'          => time()
        ]
      );
    }
    return Redirect::to($redirectTo);
  }

  public function test()
  {
    echo "boo";
  }
}
