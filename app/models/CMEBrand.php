<?php
/**
 * @author  oke.ugwu
 */

class CMEBrand extends Eloquent
{
  protected $table = 'brands';

  public function campaigns()
  {
    return $this->hasMany('CMECampaign', 'brand_id');
  }
}
