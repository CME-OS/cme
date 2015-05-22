<?php
namespace Cme\Web\Controllers;

use CmeData\CampaignEventData;
use CmeKernel\Core\CmeKernel;
use CmeKernel\Enums\EventType;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;

class TrackingController extends BaseController
{
  public function trackOpen($source)
  {
    list($campaignId, $listId, $subscriberId) = explode('_', $source);

    if($campaignId && $listId && $subscriberId)
    {
      $data = [
        'campaign_id'   => $campaignId,
        'list_id'       => $listId,
        'subscriber_id' => $subscriberId,
        'event_type'    => EventType::OPENED,
        'reference'     => $this->_getIpAddress(),
        'time'          => time()
      ];
      CmeKernel::CampaignEvent()->trackOpen(CampaignEventData::hydrate($data));
    }
  }

  public function trackUnsubscribe($source, $redirect)
  {
    list($campaignId, $listId, $subscriberId) = explode('_', $source);
    if($campaignId && $listId && $subscriberId)
    {
      $data = [
        'campaign_id'   => $campaignId,
        'list_id'       => $listId,
        'subscriber_id' => $subscriberId,
        'event_type'    => EventType::UNSUBSCRIBED,
        'time'          => time()
      ];
      CmeKernel::CampaignEvent()->trackUnsubscribe(
        CampaignEventData::hydrate($data)
      );
    }
    return Redirect::to(base64_decode($redirect));
  }

  public function trackClick($source, $redirect)
  {
    list($campaignId, $listId, $subscriberId) = explode('_', $source);
    $redirectTo = base64_decode($redirect);
    if($campaignId && $listId && $subscriberId)
    {
      $data = [
        'campaign_id'   => $campaignId,
        'list_id'       => $listId,
        'subscriber_id' => $subscriberId,
        'event_type'    => EventType::CLICKED,
        'reference'     => $redirectTo,
        'time'          => time()
      ];
      CmeKernel::CampaignEvent()->trackClick(CampaignEventData::hydrate($data));
    }
    return Redirect::to($redirectTo);
  }

  private function _getIpAddress()
  {
    $ip = Request::server(
      'HTTP_CLIENT_IP',
      Request::server(
        'HTTP_X_FORWARDED_FOR',
        Request::server('REMOTE_ADDR')
      )
    );
    return $ip;
  }
}
