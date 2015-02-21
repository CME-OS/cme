<?php
/**
 * @author  oke.ugwu
 */
namespace Cme\Models;

class CMECampaignEvent extends Model
{
  protected $table = 'campaign_events';

  public static function getCampaignEvents($campaignId)
  {
    return self::where('campaign_id', '=', $campaignId)->get();
  }

  public static function getSentMessages($campaignId)
  {
    return self::where(['campaign_id' => $campaignId, 'event_type' => 'Sent'])->count();
  }
}
