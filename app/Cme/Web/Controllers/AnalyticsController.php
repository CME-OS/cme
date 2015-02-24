<?php
namespace Cme\Web\Controllers;

use Cme\Helpers\ListHelper;
use Cme\Models\CMECampaign;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;

class AnalyticsController extends BaseController
{
  public function index()
  {
    $id                 = (int)Route::input('id');
    $data['selectedId'] = $id;
    if($id)
    {
      $data['stats']        = $this->_getStats($id);
      $data['opens']        = $this->_getLastTenOfEvent('opened', $id);
      $data['unsubscribes'] = $this->_getLastTenOfEvent('unsubscribed', $id);
      $data['clicks']       = $this->_getLinkActivity($id);
      $data['campaign']     = CMECampaign::find($id);
    }
    $data['campaigns'] = CMECampaign::getKeyedListFor('subject');
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

    $stats = [];

    foreach($eventTypes as $type)
    {
      $stats[$type] = 0;
    }

    $lastId = 0;
    do
    {
      $events = DB::select(
        "SELECT * FROM campaign_events
         WHERE event_id > $lastId
         AND campaign_id = $id
         AND subscriber_id > 0
         ORDER BY event_id ASC LIMIT 1000"
      );
      foreach($events as $event)
      {
        if(isset($stats[$event->event_type]))
        {
          $stats[$event->event_type]++;
        }
        $lastId = $event->event_id;
      }
    }
    while($events);

    return $stats;
  }

  private function _getLinkActivity($campaignId)
  {
    $clicks = DB::select(
      "SELECT count(*) as total, subscriber_id, reference FROM campaign_events
      WHERE campaign_id = $campaignId
      AND subscriber_id > 0
      AND event_type='clicked'
      GROUP BY reference, subscriber_id"
    );

    $stats = [];
    foreach($clicks as $c)
    {
      if(!isset($stats[$c->reference]))
      {
        $stats[$c->reference]['unique'] = 0;
        $stats[$c->reference]['total']  = 0;
      }
      $stats[$c->reference]['unique']++;
      $stats[$c->reference]['total'] += $c->total;
    }

    return $stats;
  }

  private function _getLastTenOfEvent($eventType, $campaignId)
  {
    $campaign  = CMECampaign::find($campaignId);
    $listTable = ListHelper::getTable($campaign->list_id);

    $subscribers = DB::select(
      "SELECT subscriber_id FROM campaign_events
      WHERE campaign_id = $campaignId
      AND subscriber_id > 0
      AND event_type='$eventType'
      GROUP BY subscriber_id
      ORDER BY event_id DESC LIMIT 10"
    );

    $subscriber_ids = [];
    foreach($subscribers as $subscriber)
    {
      $subscriber_ids[] = $subscriber->subscriber_id;
    }

    $result = [];
    if($subscriber_ids)
    {
      $result = DB::select(
        "SELECT * FROM $listTable
         WHERE id IN (" . implode(',', $subscriber_ids) . ")"
      );
    }

    return $result;
  }
}
