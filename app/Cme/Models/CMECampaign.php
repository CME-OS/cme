<?php
/**
 * @author  oke.ugwu
 */
namespace Cme\Models;

class CMECampaign extends Model
{
  protected $table = 'campaigns';
  public $timestamps = false;

  public function brand()
  {
    return $this->belongsTo('Cme\Models\CMEBrand');
  }

  public function lists()
  {
    return $this->belongsTo('Cme\Models\CMEList', 'list_id');
  }

  public function smtpProvider()
  {
    return $this->belongsTo('Cme\Models\CMESmtpProvider', 'smtp_provider_id');
  }

  public static function getKeyedListFor($field)
  {
    return self::orderBy('id', 'asc')->lists($field, 'id');
  }

  public static function getAllActive()
  {
    return self::whereNull('deleted_at')->get();
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
      'type',
      'filters',
      'created',
      'frequency',
      'tested',
      'previewed',
      'smtp_provider_id',
      'deleted_at',
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
      self::where('id', '=', $data['id'])
        ->update($data);
    }
    else
    {
      if(isset($data['filters']))
      {
        $data['filters'] = json_encode($data['filters']);
      }
      $data['created'] = time();
      self::insert($data);
    }
  }
}
