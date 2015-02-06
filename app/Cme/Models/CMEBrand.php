<?php
/**
 * @author  oke.ugwu
 */
namespace Cme\Models;

use Illuminate\Support\Facades\DB;

class CMEBrand extends Model
{
  protected $table = 'brands';

  public function campaigns()
  {
    return $this->hasMany('CMECampaign', 'brand_id');
  }

  public static function getAllActive()
  {
    return self::whereNull('brand_deleted_at')->get();
  }

  public static function getAnyBrand()
  {
    return head(DB::select("SELECT * FROM brands LIMIT 1"));
  }
}
