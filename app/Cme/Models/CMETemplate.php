<?php
/**
 * @author  oke.ugwu
 */
namespace Cme\Models;

class CMETemplate extends Model
{
  protected $table = 'templates';
  public $timestamps = false;

  public static function getAllActive()
  {
    return self::whereNull('deleted_at')->get();
  }

  public static function getKeyedListFor($field)
  {
    return self::whereNull('deleted_at')
      ->orderBy('id', 'asc')->lists($field, 'id');
  }

  public static function fields()
  {
    return [
      'id',
      'name',
      'content',
      'screenshot',
      'created',
      'deleted_at',
    ];
  }

  public static function saveData($data)
  {
    foreach($data as $key => $value)
    {
      if(!in_array($key, self::fields()))
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
      $data['created'] = time();
      self::insert($data);
    }
  }
}
