<?php
/**
 * @author  oke.ugwu
 */

class CMECampaign extends Eloquent
{
  protected $table = 'campaigns';

  public function brand()
  {
    return $this->belongsTo('CMEBrand');
  }
}
