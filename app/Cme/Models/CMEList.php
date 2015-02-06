<?php
/**
 * @author  oke.ugwu
 */
namespace Cme\Models;

use Cme\Helpers\ListHelper;
use Illuminate\Support\Facades\DB;

class CMEList extends Model
{
  protected $table = 'lists';

  public static function getAllActive()
  {
    return self::whereNull('deleted_at')->get();
  }

  public static function getAnySubscriber($listId)
  {
    $tableName = ListHelper::getTable($listId);
    return head(DB::select("SELECT * FROM $tableName LIMIT 1"));
  }
}
