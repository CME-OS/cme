<?php
/**
 * @author  oke.ugwu
 */
namespace Cme\Models;

use Illuminate\Support\Facades\DB;

class CMESmtpProvider extends Model
{
  protected $table = 'smtp_providers';

  public function campaigns()
  {
    return $this->hasMany('CMECampaign', 'smtp_provider_id');
  }

  public static function getAllActive()
  {
    return self::whereNull('deleted_at')->get();
  }

  public static function getAnyBrand()
  {
    return head(DB::select("SELECT * FROM brands LIMIT 1"));
  }
}
