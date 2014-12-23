<?php
/**
 * @author  oke.ugwu
 */
namespace Cme\Models;

class CMEBrand extends Model
{
  protected $table = 'brands';

  public function campaigns()
  {
    return $this->hasMany('CMECampaign', 'brand_id');
  }

  public static function getAllActive()
  {
    return self::where('brand_deleted_at', '=', 'NULL')->get();
  }

  public static function getAnyBrand()
  {
    return head(DB::select("SELECT * FROM brands LIMIT 1"));
  }
}
