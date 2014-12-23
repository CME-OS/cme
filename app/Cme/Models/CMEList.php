<?php
/**
 * @author  oke.ugwu
 */
namespace Cme\Models;

use Cme\Helpers\ListHelper;

class CMEList extends Model
{
  protected $table = 'lists';

  public static function getAllActive()
  {
    return self::where('deleted_at', '=', 'NULL')->get();
  }

  public static function getAnySubscriber($listId)
  {
    $tableName = ListHelper::getTable($listId);
    return head(DB::select("SELECT * FROM $tableName LIMIT 1"));
  }
}
