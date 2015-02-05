<?php
/**
 * @author  oke.ugwu
 */
namespace Cme\Models;

class CMECampaignEvent extends Model
{
  protected $table = 'campaign_events';

  public static function getCampaignEvents($id)
  {
    return self::where('campaign_id', '=', $id)->get();
  }
}
