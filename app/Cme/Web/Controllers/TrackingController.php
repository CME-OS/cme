<?php
namespace Cme\Web\Controllers;

use Cme\Lib\Campaign\MessageId;
use CmeData\CampaignEventData;
use CmeKernel\Core\CmeKernel;
use CmeKernel\Enums\EventType;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;

class TrackingController extends BaseController
{
  public function trackOpen($source)
  {
    $mid = $this->_getMessageId($source);
    if($mid->campaignId && $mid->listId && $mid->subscriberId)
    {
      $data = [
        'campaign_id'   => $mid->campaignId,
        'list_id'       => $mid->listId,
        'subscriber_id' => $mid->subscriberId,
        'event_type'    => EventType::OPENED,
        'reference'     => $this->_getIpAddress(),
        'time'          => time()
      ];
      CmeKernel::CampaignEvent()->trackOpen(CampaignEventData::hydrate($data));
    }
  }

  public function trackUnsubscribe($source, $redirect)
  {
    $mid = $this->_getMessageId($source);
    if($mid->campaignId && $mid->listId && $mid->subscriberId)
    {
      $data = [
        'campaign_id'   => $mid->campaignId,
        'list_id'       => $mid->listId,
        'subscriber_id' => $mid->subscriberId,
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
    $mid        = $this->_getMessageId($source);
    $redirectTo = base64_decode($redirect);
    if($mid->campaignId && $mid->listId && $mid->subscriberId)
    {
      $data = [
        'campaign_id'   => $mid->campaignId,
        'list_id'       => $mid->listId,
        'subscriber_id' => $mid->subscriberId,
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

  /**
   * @param $source
   *
   * @return MessageId
   */
  private function _getMessageId($source)
  {
    //check if source is the old un-encrypted version
    if(count(explode('_', $source)) == 3)
    {
      list($campaignId, $listId, $subscriberId) = explode('_', $source);
    }
    else
    {
      $parts = Crypt::decrypt(base64_decode($source));
      list($campaignId, $listId, $subscriberId) = explode('_', $parts);
    }

    $mid               = new MessageId();
    $mid->campaignId   = (int)$campaignId;
    $mid->listId       = (int)$listId;
    $mid->subscriberId = (int)$subscriberId;

    return $mid;
  }
}
