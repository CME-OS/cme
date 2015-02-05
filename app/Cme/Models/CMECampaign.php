<?php
/**
 * @author  oke.ugwu
 */
namespace Cme\Models;

class CMECampaign extends Model
{
  protected $table = 'campaigns';

  public function brand()
  {
    return $this->belongsTo('Cme\Models\CMEBrand');
  }

  public static function getKeyedListFor($field)
  {
    return self::orderBy('id', 'asc')->lists($field, 'id');
  }

  public static function fields()
  {
    return [
      'id',
      'subject',
      'from',
      'html_content',
      'text_content',
      'list_id',
      'brand_id',
      'send_time',
      'send_priority',
      'status',
      'created'
    ];
  }

  public static function saveData($data)
  {
    foreach($data as $key => $value)
    {
      if(!in_array($key, CMECampaign::fields()))
      {
        unset($data[$key]);
      }
    }
    if(isset($data['id']))
    {
      DB::table('campaigns')->where('id', '=', $data['id'])
        ->update($data);
    }
    else
    {
      DB::table('campaigns')->insert($data);
    }
  }
}
